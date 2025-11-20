<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentFormRequest extends FormRequest
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
            'invoice_id' => 'required|exists:invoices,id',
            'total_payment' => 'nullable|numeric|min:0',
            'balance_payment' => 'nullable|numeric|min:0',
            // 'is_image' => 'required|boolean',
            // 'payment_date' => 'required|date',
            'payment_voucher' => 'nullable',
            // 'payment_notes' => 'nullable',
            // 'payment_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|dimensions:max_width=6000,max_height=4000'
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'invoice_id.required' => 'The invoice is required.',
            'invoice_id.exists' => 'The selected invoice is invalid.',
            'total_payment.required' => 'The total payment amount is required.',
            'total_payment.numeric' => 'The total payment must be a number.',
            'total_payment.min' => 'The total payment cannot be negative.',
            'payment_voucher.required' => 'The payment voucher is required.',
            // 'balance_payment.required' => 'The balance payment is required.',
            // 'balance_payment.numeric' => 'The balance payment must be a number.',
            // 'balance_payment.min' => 'The balance payment cannot be negative.',
            // 'is_image.required' => 'Please specify if there is an image attached.',
            // 'is_image.boolean' => 'Invalid image attachment status.',
            // 'payment_date.required' => 'The payment date is required.',
            // 'payment_date.date' => 'Please enter a valid date.',
            // 'payment_image.image' => 'The payment image must be a valid image file.',
            // 'payment_image.mimes' => 'The payment image must be a file of type: jpeg, png, jpg, gif.',
            // 'payment_image.dimensions' => 'The payment image dimensions are too large. Maximum dimensions are 6000x4000 pixels.'
        ];
    }
}
