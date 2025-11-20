<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReceiptFormRequest extends FormRequest
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
            'receipt_no' => [
                'required',
                Rule::unique('receipts', 'receipt_no')->ignore($this->route('id')),
            ],
            'receipt_date' => 'required|date',
            'receipt_note' => 'nullable',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|dimensions:max_width=6000,max_height=4000'
        ];
    }

    public function messages(): array
    {
        return [
            'receipt_no.required' => 'The receipt number is required.',
            'receipt_no.unique' => 'This receipt number has already been taken.',
            'receipt_image.image' => 'The receipt image must be a valid image file.',
            'receipt_image.mimes' => 'The receipt image must be a file of type: jpeg, png, jpg, gif.',
            'receipt_image.dimensions' => 'The receipt image dimensions are too large. Maximum dimensions are 6000x4000 pixels.',
            'receipt_image.uploaded' => 'The receipt image failed to upload. Please try again with a smaller image.'
        ];
    }
}
