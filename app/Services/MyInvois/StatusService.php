<?php

namespace App\Services\MyInvois;

use App\Models\Einvoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StatusService
{
    private AuthenticationService $authService;

    private string $baseUrl;

    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
        $this->baseUrl = config('myinvois.api.base_url');
    }

    /**
     * Get submission status by submission UID
     *
     * @param  string  $submissionUid  The submission UID returned when document was submitted
     * @return array Status information
     */
    public function getSubmissionStatus(string $submissionUid): array
    {
        Log::channel('myinvois')->info('Checking submission status', [
            'submission_uid' => $submissionUid,
        ]);

        $token = $this->authService->getToken();

        try {
            $response = Http::withToken($token)
                ->timeout(config('myinvois.api.timeout'))
                ->get("{$this->baseUrl}/api/v1.0/documentsubmissions/{$submissionUid}");

            if (! $response->successful()) {
                Log::channel('myinvois')->error('Status check failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'submission_uid' => $submissionUid,
                ]);

                throw new \Exception("Status check failed: {$response->body()}");
            }

            $data = $response->json();

            Log::channel('myinvois')->info('Status retrieved', [
                'submission_uid' => $submissionUid,
                'status' => $data['status'] ?? 'unknown',
            ]);

            return $data;

        } catch (\Exception $e) {
            Log::channel('myinvois')->error('Status check exception', [
                'message' => $e->getMessage(),
                'submission_uid' => $submissionUid,
            ]);

            throw $e;
        }
    }

    /**
     * Get document details by document UUID
     *
     * @param  string  $documentUuid  The document UUID
     * @return array Document details
     */
    public function getDocumentDetails(string $documentUuid): array
    {
        Log::channel('myinvois')->info('Getting document details', [
            'document_uuid' => $documentUuid,
        ]);

        $token = $this->authService->getToken();

        try {
            $response = Http::withToken($token)
                ->timeout(config('myinvois.api.timeout'))
                ->get("{$this->baseUrl}/api/v1.0/documents/{$documentUuid}/details");

            if (! $response->successful()) {
                Log::channel('myinvois')->error('Document details retrieval failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'document_uuid' => $documentUuid,
                ]);

                throw new \Exception("Document details retrieval failed: {$response->body()}");
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::channel('myinvois')->error('Document details exception', [
                'message' => $e->getMessage(),
                'document_uuid' => $documentUuid,
            ]);

            throw $e;
        }
    }

    /**
     * Update einvoice status based on MyInvois response
     *
     * @param  Einvoice  $einvoice  The einvoice record
     */
    public function updateEinvoiceStatus(Einvoice $einvoice): void
    {
        if (! $einvoice->submission_uid) {
            Log::channel('myinvois')->warning('Cannot update status: no submission UID', [
                'einvoice_id' => $einvoice->id,
            ]);

            return;
        }

        try {
            $statusData = $this->getSubmissionStatus($einvoice->submission_uid);

            // Extract document status from submission
            $documentStatus = $statusData['documentSummary'][0]['status'] ?? null;
            $longId = $statusData['documentSummary'][0]['longId'] ?? null;
            $validationErrors = $statusData['documentSummary'][0]['validationResults'] ?? null;

            // Map MyInvois status to our status
            $newStatus = $this->mapStatus($documentStatus);

            // Update einvoice record
            $updateData = [
                'status' => $newStatus,
                'api_response' => $statusData,
            ];

            if ($longId) {
                $updateData['long_id'] = $longId;
            }

            if ($newStatus === 'valid') {
                $updateData['validated_at'] = now();
            }

            if ($validationErrors) {
                $updateData['validation_errors'] = $validationErrors;
                $updateData['error_message'] = $this->parseValidationErrors($validationErrors);
            }

            $einvoice->update($updateData);

            Log::channel('myinvois')->info('Einvoice status updated', [
                'einvoice_id' => $einvoice->id,
                'old_status' => $einvoice->status,
                'new_status' => $newStatus,
            ]);

        } catch (\Exception $e) {
            Log::channel('myinvois')->error('Failed to update einvoice status', [
                'einvoice_id' => $einvoice->id,
                'message' => $e->getMessage(),
            ]);

            $einvoice->update([
                'status' => 'error',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Parse validation errors into readable format
     */
    public function parseValidationErrors(array $validationResults): string
    {
        $errors = [];

        foreach ($validationResults as $result) {
            if (isset($result['error'])) {
                $errors[] = $result['error']['message'] ?? 'Unknown error';
            }
        }

        return implode('; ', $errors);
    }

    /**
     * Map MyInvois status to our internal status
     */
    private function mapStatus(?string $myInvoisStatus): string
    {
        return match ($myInvoisStatus) {
            'Valid' => 'valid',
            'Invalid' => 'invalid',
            'Submitted' => 'submitted',
            'Cancelled' => 'cancelled',
            'Rejected' => 'rejected',
            default => 'pending',
        };
    }

    /**
     * Get recent document by status
     *
     * @param  string  $status  Document status filter
     * @param  int  $pageSize  Number of results per page
     * @param  int  $pageNo  Page number
     * @return array Documents list
     */
    public function getRecentDocuments(string $status = 'Valid', int $pageSize = 100, int $pageNo = 1): array
    {
        Log::channel('myinvois')->info('Getting recent documents', [
            'status' => $status,
            'page_size' => $pageSize,
            'page_no' => $pageNo,
        ]);

        $token = $this->authService->getToken();

        try {
            $response = Http::withToken($token)
                ->timeout(config('myinvois.api.timeout'))
                ->get("{$this->baseUrl}/api/v1.0/documents/recent", [
                    'pageSize' => $pageSize,
                    'pageNo' => $pageNo,
                    'status' => $status,
                ]);

            if (! $response->successful()) {
                throw new \Exception("Failed to get recent documents: {$response->body()}");
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::channel('myinvois')->error('Get recent documents exception', [
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Bulk update status for all submitted einvoices
     *
     * @param  int  $limit  Maximum number of einvoices to check
     * @return array Summary of updates
     */
    public function bulkUpdateStatus(int $limit = 20): array
    {
        Log::channel('myinvois')->info('Starting bulk status update', ['limit' => $limit]);

        $einvoices = Einvoice::where('status', 'submitted')
            ->whereNotNull('submission_uid')
            ->limit($limit)
            ->get();

        $summary = [
            'total' => $einvoices->count(),
            'updated' => 0,
            'failed' => 0,
            'statuses' => [],
        ];

        foreach ($einvoices as $einvoice) {
            try {
                $this->updateEinvoiceStatus($einvoice);
                $summary['updated']++;
                $summary['statuses'][$einvoice->status] = ($summary['statuses'][$einvoice->status] ?? 0) + 1;

                // Add delay to respect rate limits (300 req/min = ~200ms between requests)
                usleep(250000); // 250ms delay

            } catch (\Exception $e) {
                $summary['failed']++;
                Log::channel('myinvois')->warning('Failed to update einvoice in bulk', [
                    'einvoice_id' => $einvoice->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::channel('myinvois')->info('Bulk status update completed', $summary);

        return $summary;
    }
}
