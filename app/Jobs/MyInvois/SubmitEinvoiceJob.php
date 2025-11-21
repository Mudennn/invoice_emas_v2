<?php

namespace App\Jobs\MyInvois;

use App\Models\Einvoice;
use App\Services\MyInvois\DocumentBuilderService;
use App\Services\MyInvois\SubmissionService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SubmitEinvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 3;

    /**
     * Delete the job if its models no longer exist.
     */
    public bool $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(public Einvoice $einvoice)
    {
        $this->onQueue('myinvois-submissions');
    }

    /**
     * Execute the job.
     */
    public function handle(DocumentBuilderService $builder, SubmissionService $submission): void
    {
        try {
            // Check if already submitted or valid
            if (in_array($this->einvoice->status, ['submitted', 'valid', 'cancelled'])) {
                Log::info('Einvoice already processed', [
                    'einvoice_id' => $this->einvoice->id,
                    'status' => $this->einvoice->status,
                ]);

                return;
            }

            // Load the documentable relationship
            $documentable = $this->einvoice->documentable;

            if (! $documentable) {
                throw new Exception('Documentable record not found');
            }

            // Build the document based on type
            $document = $this->buildDocument($builder, $documentable);

            // Store the payload
            $this->einvoice->update([
                'einvoice_payload' => $document,
            ]);

            // Submit to MyInvois
            $response = $submission->submitDocument($document, $this->einvoice->document_type_code);

            // Update einvoice with submission details
            $this->einvoice->update([
                'submission_uid' => $response['submissionUid'] ?? null,
                'status' => 'submitted',
                'api_response' => $response,
                'submitted_at' => now(),
                'error_message' => null,
            ]);

            // Dispatch status check job (delayed by 30 seconds to allow processing)
            CheckEinvoiceStatusJob::dispatch($this->einvoice)
                ->delay(now()->addSeconds(30))
                ->onQueue('myinvois-status');

            Log::info('Einvoice submitted successfully', [
                'einvoice_id' => $this->einvoice->id,
                'submission_uid' => $response['submissionUid'] ?? null,
            ]);
        } catch (Exception $e) {
            // Increment retry count
            $this->einvoice->incrementRetry();

            // Update error status
            $this->einvoice->update([
                'status' => 'error',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('Einvoice submission failed', [
                'einvoice_id' => $this->einvoice->id,
                'error' => $e->getMessage(),
                'retry_count' => $this->einvoice->retry_count,
            ]);

            // Re-throw to trigger job retry mechanism
            throw $e;
        }
    }

    /**
     * Build document based on documentable type.
     */
    private function buildDocument(DocumentBuilderService $builder, $documentable): array
    {
        return match (get_class($documentable)) {
            'App\Models\Invoice' => $builder->buildInvoiceDocument($documentable),
            'App\Models\CreditNote' => $builder->buildCreditNoteDocument($documentable),
            'App\Models\DebitNote' => $builder->buildDebitNoteDocument($documentable),
            'App\Models\RefundNote' => $builder->buildRefundNoteDocument($documentable),
            default => throw new Exception('Unsupported documentable type: '.get_class($documentable)),
        };
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        // Update einvoice to permanently failed status
        $this->einvoice->update([
            'status' => 'error',
            'error_message' => 'Job failed after '.$this->tries.' attempts: '.$exception->getMessage(),
        ]);

        Log::error('Einvoice job permanently failed', [
            'einvoice_id' => $this->einvoice->id,
            'error' => $exception->getMessage(),
            'attempts' => $this->tries,
        ]);
    }
}
