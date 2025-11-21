<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkSubmitEinvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'document_ids' => ['required', 'array', 'min:1', 'max:100'],
            'document_ids.*' => ['required', 'integer'],
            'document_type' => ['required', 'string', 'in:invoice,credit_note,debit_note,refund_note'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'document_ids.required' => 'Please select at least one document to submit.',
            'document_ids.array' => 'Invalid document selection format.',
            'document_ids.min' => 'Please select at least one document.',
            'document_ids.max' => 'You can only submit up to 100 documents at once.',
            'document_ids.*.required' => 'Each document ID is required.',
            'document_ids.*.integer' => 'Document IDs must be valid integers.',
            'document_type.required' => 'Document type is required.',
            'document_type.in' => 'Invalid document type. Must be: invoice, credit_note, debit_note, or refund_note.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'document_ids' => 'documents',
            'document_type' => 'document type',
        ];
    }
}
