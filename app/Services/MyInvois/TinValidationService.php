<?php

namespace App\Services\MyInvois;

use App\Models\CustomerProfile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TinValidationService
{
    private AuthenticationService $authService;

    private string $baseUrl;

    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
        $this->baseUrl = config('myinvois.api.base_url');
    }

    /**
     * Validate TIN (Tax Identification Number)
     *
     * @param  string  $tin  The TIN to validate
     * @param  string  $idType  Type of identification (BRN, NRIC, PASSPORT, ARMY)
     * @param  string  $idValue  The identification number
     * @return array Validation result
     */
    public function validateTin(string $tin, string $idType, string $idValue): array
    {
        Log::channel('myinvois')->info('Validating TIN', [
            'tin' => $tin,
            'id_type' => $idType,
        ]);

        $token = $this->authService->getToken();

        try {
            $response = Http::withToken($token)
                ->timeout(config('myinvois.api.timeout'))
                ->get("{$this->baseUrl}/api/v1.0/taxpayer/validate/{$tin}", [
                    'idType' => $idType,
                    'idValue' => $idValue,
                ]);

            if (! $response->successful()) {
                Log::channel('myinvois')->error('TIN validation failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'tin' => $tin,
                ]);

                return [
                    'valid' => false,
                    'error' => $response->body(),
                    'status_code' => $response->status(),
                ];
            }

            $data = $response->json();

            Log::channel('myinvois')->info('TIN validation successful', [
                'tin' => $tin,
                'valid' => $data['valid'] ?? false,
            ]);

            return [
                'valid' => true,
                'data' => $data,
            ];

        } catch (\Exception $e) {
            Log::channel('myinvois')->error('TIN validation exception', [
                'message' => $e->getMessage(),
                'tin' => $tin,
            ]);

            return [
                'valid' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Search for TIN by identification details
     *
     * @param  string  $idType  Type of identification (BRN, NRIC, PASSPORT, ARMY)
     * @param  string  $idValue  The identification number
     * @param  string  $name  Name associated with the TIN
     * @return array Search results
     */
    public function searchTin(string $idType, string $idValue, string $name): array
    {
        Log::channel('myinvois')->info('Searching for TIN', [
            'id_type' => $idType,
            'name' => $name,
        ]);

        $token = $this->authService->getToken();

        try {
            $response = Http::withToken($token)
                ->timeout(config('myinvois.api.timeout'))
                ->get("{$this->baseUrl}/api/v1.0/taxpayer/search", [
                    'idType' => $idType,
                    'idValue' => $idValue,
                    'name' => $name,
                ]);

            if (! $response->successful()) {
                Log::channel('myinvois')->error('TIN search failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'found' => false,
                    'error' => $response->body(),
                ];
            }

            $data = $response->json();

            Log::channel('myinvois')->info('TIN search completed', [
                'found' => ! empty($data),
            ]);

            return [
                'found' => true,
                'data' => $data,
            ];

        } catch (\Exception $e) {
            Log::channel('myinvois')->error('TIN search exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'found' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Validate customer profile before invoice creation
     *
     * @param  CustomerProfile  $customer  The customer profile to validate
     * @return bool True if valid, false otherwise
     */
    public function validateCustomerBeforeInvoice(CustomerProfile $customer): bool
    {
        if (! $customer->tin) {
            Log::channel('myinvois')->warning('Customer has no TIN', [
                'customer_id' => $customer->id,
                'company_name' => $customer->company_name,
            ]);

            return false;
        }

        // Determine ID type and value
        $idType = null;
        $idValue = null;

        if ($customer->business_registration_no) {
            $idType = 'BRN';
            $idValue = $customer->business_registration_no;
        } elseif ($customer->identification_number) {
            $idType = 'NRIC';
            $idValue = $customer->identification_number;
        } elseif ($customer->passport_number) {
            $idType = 'PASSPORT';
            $idValue = $customer->passport_number;
        } else {
            Log::channel('myinvois')->warning('Customer has no identification number', [
                'customer_id' => $customer->id,
            ]);

            return false;
        }

        // Validate TIN
        $result = $this->validateTin($customer->tin, $idType, $idValue);

        return $result['valid'] ?? false;
    }

    /**
     * Bulk validate all customers without validated TIN
     *
     * @param  int  $limit  Maximum number of customers to validate
     * @return array Summary of validation results
     */
    public function bulkValidateCustomers(int $limit = 50): array
    {
        Log::channel('myinvois')->info('Starting bulk customer TIN validation', ['limit' => $limit]);

        $customers = CustomerProfile::where('status', '0')
            ->whereNotNull('tin')
            ->limit($limit)
            ->get();

        $summary = [
            'total' => $customers->count(),
            'valid' => 0,
            'invalid' => 0,
            'errors' => 0,
        ];

        foreach ($customers as $customer) {
            try {
                $isValid = $this->validateCustomerBeforeInvoice($customer);

                if ($isValid) {
                    $summary['valid']++;
                } else {
                    $summary['invalid']++;
                }

                // Add delay to respect rate limits
                usleep(250000); // 250ms delay

            } catch (\Exception $e) {
                $summary['errors']++;
                Log::channel('myinvois')->warning('Failed to validate customer TIN in bulk', [
                    'customer_id' => $customer->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::channel('myinvois')->info('Bulk TIN validation completed', $summary);

        return $summary;
    }

    /**
     * Validate TIN format (Malaysia format: C + 10 digits)
     *
     * @param  string  $tin  The TIN to validate format
     * @return bool True if format is valid
     */
    public function validateTinFormat(string $tin): bool
    {
        // Malaysia TIN format: C followed by 10 digits
        return preg_match('/^C[0-9]{10}$/', $tin) === 1;
    }

    /**
     * Get ID type from customer profile
     *
     * @return array ['type' => string, 'value' => string] or null
     */
    public function getCustomerIdType(CustomerProfile $customer): ?array
    {
        if ($customer->business_registration_no) {
            return [
                'type' => 'BRN',
                'value' => $customer->business_registration_no,
            ];
        }

        if ($customer->identification_number) {
            return [
                'type' => 'NRIC',
                'value' => $customer->identification_number,
            ];
        }

        if ($customer->passport_number) {
            return [
                'type' => 'PASSPORT',
                'value' => $customer->passport_number,
            ];
        }

        return null;
    }
}
