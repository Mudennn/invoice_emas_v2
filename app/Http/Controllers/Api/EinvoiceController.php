<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkSubmitEinvoiceRequest;
use App\Jobs\MyInvois\CheckEinvoiceStatusJob;
use App\Jobs\MyInvois\SubmitEinvoiceJob;
use App\Models\CreditNote;
use App\Models\DebitNote;
use App\Models\Einvoice;
use App\Models\Invoice;
use App\Models\RefundNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EinvoiceController extends Controller
{
    /**
     * Bulk submit documents to MyInvois.
     */
    public function bulkSubmit(BulkSubmitEinvoiceRequest $request): JsonResponse
    {
        $documentIds = $request->input('document_ids');
        $documentType = $request->input('document_type');

        try {
            DB::beginTransaction();

            $results = [
                'total' => count($documentIds),
                'queued' => 0,
                'skipped' => 0,
                'errors' => [],
            ];

            foreach ($documentIds as $documentId) {
                try {
                    // Get the document based on type
                    $document = $this->getDocument($documentType, $documentId);

                    if (! $document) {
                        $results['skipped']++;
                        $results['errors'][] = [
                            'document_id' => $documentId,
                            'error' => 'Document not found',
                        ];

                        continue;
                    }

                    // Check if already has valid einvoice
                    $existingEinvoice = $document->einvoices()
                        ->where('status', 'valid')
                        ->first();

                    if ($existingEinvoice) {
                        $results['skipped']++;
                        $results['errors'][] = [
                            'document_id' => $documentId,
                            'error' => 'Already has valid e-invoice',
                        ];

                        continue;
                    }

                    // Create or get pending einvoice record
                    $einvoice = $document->einvoices()
                        ->whereIn('status', ['pending', 'error'])
                        ->first();

                    if (! $einvoice) {
                        $einvoice = Einvoice::create([
                            'documentable_id' => $document->id,
                            'documentable_type' => $this->getDocumentClass($documentType),
                            'document_type_code' => $this->getDocumentTypeCode($documentType),
                            'status' => 'pending',
                            'retry_count' => 0,
                        ]);
                    }

                    // Dispatch submission job
                    SubmitEinvoiceJob::dispatch($einvoice);

                    $results['queued']++;
                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'document_id' => $documentId,
                        'error' => $e->getMessage(),
                    ];
                    Log::error('Failed to queue einvoice submission', [
                        'document_id' => $documentId,
                        'document_type' => $documentType,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$results['queued']} document(s) queued for submission to MyInvois",
                'data' => $results,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Bulk einvoice submission failed', [
                'error' => $e->getMessage(),
                'document_type' => $documentType,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process bulk submission',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Submit single document to MyInvois.
     */
    public function submitSingle(Request $request, int $id): JsonResponse
    {
        $documentType = $request->route()->parameter('type') ?? 'invoice';

        // Use bulk submit internally
        $bulkRequest = BulkSubmitEinvoiceRequest::createFrom($request);
        $bulkRequest->merge([
            'document_ids' => [$id],
            'document_type' => $documentType,
        ]);

        return $this->bulkSubmit($bulkRequest);
    }

    /**
     * Check status of a single einvoice.
     */
    public function checkStatus(int $id): JsonResponse
    {
        $einvoice = Einvoice::find($id);

        if (! $einvoice) {
            return response()->json([
                'success' => false,
                'message' => 'E-invoice not found',
            ], 404);
        }

        // Dispatch status check job if not in final status
        if (! in_array($einvoice->status, ['valid', 'invalid', 'cancelled', 'rejected'])) {
            CheckEinvoiceStatusJob::dispatch($einvoice);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $einvoice->id,
                'status' => $einvoice->status,
                'submission_uid' => $einvoice->submission_uid,
                'document_uuid' => $einvoice->document_uuid,
                'long_id' => $einvoice->long_id,
                'submitted_at' => $einvoice->submitted_at?->toIso8601String(),
                'validated_at' => $einvoice->validated_at?->toIso8601String(),
                'error_message' => $einvoice->error_message,
                'validation_errors' => $einvoice->validation_errors,
                'retry_count' => $einvoice->retry_count,
            ],
        ]);
    }

    /**
     * Resubmit a failed einvoice.
     */
    public function resubmit(int $id): JsonResponse
    {
        $einvoice = Einvoice::find($id);

        if (! $einvoice) {
            return response()->json([
                'success' => false,
                'message' => 'E-invoice not found',
            ], 404);
        }

        if (! $einvoice->canRetry()) {
            return response()->json([
                'success' => false,
                'message' => 'E-invoice cannot be retried (max attempts reached or invalid status)',
            ], 400);
        }

        // Reset status to pending and dispatch job
        $einvoice->update([
            'status' => 'pending',
            'error_message' => null,
        ]);

        SubmitEinvoiceJob::dispatch($einvoice);

        return response()->json([
            'success' => true,
            'message' => 'E-invoice resubmission queued',
            'data' => [
                'id' => $einvoice->id,
                'status' => $einvoice->status,
                'retry_count' => $einvoice->retry_count,
            ],
        ]);
    }

    /**
     * Get submission history for a document.
     */
    public function getHistory(string $documentType, int $id): JsonResponse
    {
        $document = $this->getDocument($documentType, $id);

        if (! $document) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found',
            ], 404);
        }

        $einvoices = $document->einvoices()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($einvoice) {
                return [
                    'id' => $einvoice->id,
                    'status' => $einvoice->status,
                    'submission_uid' => $einvoice->submission_uid,
                    'document_uuid' => $einvoice->document_uuid,
                    'long_id' => $einvoice->long_id,
                    'submitted_at' => $einvoice->submitted_at?->toIso8601String(),
                    'validated_at' => $einvoice->validated_at?->toIso8601String(),
                    'error_message' => $einvoice->error_message,
                    'retry_count' => $einvoice->retry_count,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $einvoices,
        ]);
    }

    /**
     * Get document based on type and ID.
     */
    private function getDocument(string $type, int $id)
    {
        return match ($type) {
            'invoice' => Invoice::find($id),
            'credit_note' => CreditNote::find($id),
            'debit_note' => DebitNote::find($id),
            'refund_note' => RefundNote::find($id),
            default => null,
        };
    }

    /**
     * Get document class name.
     */
    private function getDocumentClass(string $type): string
    {
        return match ($type) {
            'invoice' => Invoice::class,
            'credit_note' => CreditNote::class,
            'debit_note' => DebitNote::class,
            'refund_note' => RefundNote::class,
        };
    }

    /**
     * Get MyInvois document type code.
     */
    private function getDocumentTypeCode(string $type): string
    {
        return match ($type) {
            'invoice' => '01',
            'credit_note' => '02',
            'debit_note' => '03',
            'refund_note' => '04',
        };
    }
}
