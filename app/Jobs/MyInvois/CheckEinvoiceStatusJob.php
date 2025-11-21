<?php

namespace App\Jobs\MyInvois;

use App\Models\Einvoice;
use App\Services\MyInvois\StatusService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckEinvoiceStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 5;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 120;

    /**
     * Delete the job if its models no longer exist.
     */
    public bool $deleteWhenMissingModels = true;

    /**
     * Maximum number of status check attempts.
     */
    private int $maxAttempts = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(public Einvoice $einvoice, public int $attempt = 1)
    {
        $this->onQueue('myinvois-status');
    }

    /**
     * Execute the job.
     */
    public function handle(StatusService $statusService): void
    {
        try {
            // Refresh the model to get latest status
            $this->einvoice->refresh();

            // If already in final status, no need to check
            if (in_array($this->einvoice->status, ['valid', 'invalid', 'cancelled', 'rejected'])) {
                Log::info('Einvoice already in final status', [
                    'einvoice_id' => $this->einvoice->id,
                    'status' => $this->einvoice->status,
                ]);

                return;
            }

            // Check if submission_uid exists
            if (! $this->einvoice->submission_uid) {
                Log::warning('Einvoice missing submission_uid, cannot check status', [
                    'einvoice_id' => $this->einvoice->id,
                ]);

                return;
            }

            // Get submission status from MyInvois
            $statusData = $statusService->getSubmissionStatus($this->einvoice->submission_uid);

            // Update the einvoice status
            $this->updateEinvoiceFromResponse($statusData);

            // If still in processing status and haven't exceeded max attempts, schedule another check
            if (in_array($this->einvoice->status, ['submitted', 'processing']) && $this->attempt < $this->maxAttempts) {
                $delay = $this->calculateDelay($this->attempt);

                self::dispatch($this->einvoice, $this->attempt + 1)
                    ->delay(now()->addSeconds($delay))
                    ->onQueue('myinvois-status');

                Log::info('Scheduled next status check', [
                    'einvoice_id' => $this->einvoice->id,
                    'attempt' => $this->attempt + 1,
                    'delay_seconds' => $delay,
                ]);
            } elseif ($this->attempt >= $this->maxAttempts && ! in_array($this->einvoice->status, ['valid', 'invalid', 'cancelled', 'rejected'])) {
                Log::warning('Max status check attempts reached', [
                    'einvoice_id' => $this->einvoice->id,
                    'attempts' => $this->attempt,
                    'current_status' => $this->einvoice->status,
                ]);
            }
        } catch (Exception $e) {
            Log::error('Status check failed', [
                'einvoice_id' => $this->einvoice->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempt,
            ]);

            // Re-throw to trigger job retry
            throw $e;
        }
    }

    /**
     * Update einvoice from API response.
     */
    private function updateEinvoiceFromResponse(array $statusData): void
    {
        $updateData = [
            'api_response' => $statusData,
        ];

        // Map MyInvois status to our internal status
        if (isset($statusData['status'])) {
            $updateData['status'] = $this->mapStatus($statusData['status']);
        }

        // Store document UUID if available
        if (isset($statusData['documentUuid'])) {
            $updateData['document_uuid'] = $statusData['documentUuid'];
        }

        // Store long ID if available
        if (isset($statusData['longId'])) {
            $updateData['long_id'] = $statusData['longId'];
        }

        // Store validation errors if any
        if (isset($statusData['validationResults']['validationSteps'])) {
            $errors = $this->extractValidationErrors($statusData['validationResults']['validationSteps']);
            if (! empty($errors)) {
                $updateData['validation_errors'] = $errors;
            }
        }

        // Set validated_at timestamp for final statuses
        if (in_array($updateData['status'] ?? null, ['valid', 'invalid', 'rejected'])) {
            $updateData['validated_at'] = now();
        }

        $this->einvoice->update($updateData);

        Log::info('Einvoice status updated', [
            'einvoice_id' => $this->einvoice->id,
            'new_status' => $updateData['status'] ?? 'unknown',
            'document_uuid' => $updateData['document_uuid'] ?? null,
        ]);
    }

    /**
     * Map MyInvois status to internal status.
     */
    private function mapStatus(string $myinvoisStatus): string
    {
        return match (strtolower($myinvoisStatus)) {
            'valid' => 'valid',
            'invalid' => 'invalid',
            'submitted' => 'submitted',
            'cancelled' => 'cancelled',
            'rejected' => 'rejected',
            default => 'processing',
        };
    }

    /**
     * Extract validation errors from validation steps.
     */
    private function extractValidationErrors(array $validationSteps): array
    {
        $errors = [];

        foreach ($validationSteps as $step) {
            if (isset($step['status']) && $step['status'] !== 'valid' && isset($step['error'])) {
                $errors[] = [
                    'step' => $step['name'] ?? 'unknown',
                    'status' => $step['status'],
                    'error' => $step['error'],
                ];
            }
        }

        return $errors;
    }

    /**
     * Calculate delay for next check with exponential backoff.
     */
    private function calculateDelay(int $attempt): int
    {
        // Exponential backoff: 30, 60, 120, 240, 480, 900, 1800, 3600 (capped at 1 hour)
        return min(30 * pow(2, $attempt - 1), 3600);
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error('Status check job permanently failed', [
            'einvoice_id' => $this->einvoice->id,
            'error' => $exception->getMessage(),
            'attempt' => $this->attempt,
        ]);
    }
}
