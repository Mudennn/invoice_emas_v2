<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceItemFormRequest extends FormRequest
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
            // 'invoice_id'  => 'required',
            // 'reference_no'  => 'required',
            // 'particulars'  => 'required',
            // 'weight'  => 'required',
            // 'wastage'  => 'required',
            // 'total_weight'  => 'required',
            // 'gold'  => 'required',
            // 'workmanship'  => 'required',
            // 'total'  => 'required',
            // 'grand_total'  => 'required',
            // 'remark'  => 'required',
        ];
    }
}
