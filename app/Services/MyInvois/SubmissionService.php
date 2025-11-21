<?php

namespace App\Services\MyInvois;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubmissionService
{
    private AuthenticationService $authService;

    private string $baseUrl;

    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
        $this->baseUrl = config('myinvois.api.base_url');
    }

    /**
     * Submit document to MyInvois API
     *
     * @param  array  $document  UBL 2.1 JSON document
     * @param  string  $codeNumber  Internal reference number (e.g., invoice_no)
     * @return array Response with submissionUid and accepted documents
     */
    public function submitDocument(array $document, string $codeNumber): array
    {
        Log::channel('myinvois')->info('Submitting document to MyInvois', [
            'code_number' => $codeNumber,
        ]);

        // Get authentication token
        $token = $this->authService->getToken();

        // Prepare submission payload
        $payload = $this->prepareSubmissionPayload($document, $codeNumber);

        try {
            $response = Http::withToken($token)
                ->timeout(config('myinvois.api.timeout'))
                ->post("{$this->baseUrl}/api/v1.0/documentsubmissions", $payload);

            Log::channel('myinvois')->info('Submission response received', [
                'status' => $response->status(),
                'code_number' => $codeNumber,
            ]);

            // MyInvois returns 202 Accepted for successful submissions
            if ($response->status() !== 202) {
                Log::channel('myinvois')->error('Submission failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'code_number' => $codeNumber,
                ]);

                throw new \Exception("Submission failed: {$response->body()}");
            }

            return $this->handleSubmissionResponse($response->json());

        } catch (\Exception $e) {
            Log::channel('myinvois')->error('Submission exception', [
                'message' => $e->getMessage(),
                'code_number' => $codeNumber,
            ]);

            throw $e;
        }
    }

    /**
     * Prepare submission payload with base64 encoding and hash
     */
    private function prepareSubmissionPayload(array $document, string $codeNumber): array
    {
        // Convert document to JSON string
        $documentJson = json_encode($document);

        // Base64 encode the document
        $encodedDocument = $this->encodeDocument($documentJson);

        // Calculate SHA256 hash of the original JSON (before encoding)
        $documentHash = $this->calculateDocumentHash($documentJson);

        Log::channel('myinvois')->debug('Document prepared for submission', [
            'code_number' => $codeNumber,
            'document_size' => strlen($documentJson),
            'encoded_size' => strlen($encodedDocument),
            'hash' => $documentHash,
        ]);

        return [
            'documents' => [
                [
                    'format' => 'JSON',
                    'document' => $encodedDocument,
                    'documentHash' => $documentHash,
                    'codeNumber' => $codeNumber,
                ],
            ],
        ];
    }

    /**
     * Encode document to Base64
     */
    private function encodeDocument(string $documentJson): string
    {
        return base64_encode($documentJson);
    }

    /**
     * Calculate SHA256 hash of document
     */
    private function calculateDocumentHash(string $documentJson): string
    {
        return hash('sha256', $documentJson);
    }

    /**
     * Handle submission response from MyInvois
     */
    private function handleSubmissionResponse(array $response): array
    {
        $submissionUid = $response['submissionUid'] ?? null;
        $acceptedDocuments = $response['acceptedDocuments'] ?? [];
        $rejectedDocuments = $response['rejectedDocuments'] ?? [];

        Log::channel('myinvois')->info('Submission processed', [
            'submission_uid' => $submissionUid,
            'accepted_count' => count($acceptedDocuments),
            'rejected_count' => count($rejectedDocuments),
        ]);

        // Check if document was rejected immediately
        if (! empty($rejectedDocuments)) {
            $rejection = $rejectedDocuments[0];
            Log::channel('myinvois')->warning('Document rejected', [
                'rejection' => $rejection,
            ]);

            throw new \Exception('Document rejected: '.json_encode($rejection));
        }

        // Extract document UUID from accepted documents
        $documentUuid = null;
        if (! empty($acceptedDocuments)) {
            $documentUuid = $acceptedDocuments[0]['uuid'] ?? null;
        }

        return [
            'submission_uid' => $submissionUid,
            'document_uuid' => $documentUuid,
            'accepted_documents' => $acceptedDocuments,
            'status' => 'submitted',
        ];
    }

    /**
     * Submit multiple documents in batch (max 100 documents)
     */
    public function submitBatch(array $documents): array
    {
        if (count($documents) > 100) {
            throw new \Exception('Maximum 100 documents allowed per batch');
        }

        Log::channel('myinvois')->info('Submitting batch of documents', [
            'count' => count($documents),
        ]);

        $token = $this->authService->getToken();

        $payload = ['documents' => []];

        foreach ($documents as $doc) {
            $documentJson = json_encode($doc['document']);
            $payload['documents'][] = [
                'format' => 'JSON',
                'document' => $this->encodeDocument($documentJson),
                'documentHash' => $this->calculateDocumentHash($documentJson),
                'codeNumber' => $doc['codeNumber'],
            ];
        }

        try {
            $response = Http::withToken($token)
                ->timeout(config('myinvois.api.timeout'))
                ->post("{$this->baseUrl}/api/v1.0/documentsubmissions", $payload);

            if ($response->status() !== 202) {
                throw new \Exception("Batch submission failed: {$response->body()}");
            }

            return $this->handleSubmissionResponse($response->json());

        } catch (\Exception $e) {
            Log::channel('myinvois')->error('Batch submission exception', [
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Validate document size before submission
     * Max 300 KB per document, Max 5 MB total per submission
     */
    public function validateDocumentSize(array $document): bool
    {
        $documentJson = json_encode($document);
        $sizeInBytes = strlen($documentJson);
        $sizeInKB = $sizeInBytes / 1024;

        if ($sizeInKB > 300) {
            Log::channel('myinvois')->warning('Document size exceeds limit', [
                'size_kb' => $sizeInKB,
                'limit_kb' => 300,
            ]);

            return false;
        }

        return true;
    }
}
