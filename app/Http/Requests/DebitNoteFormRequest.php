<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DebitNoteFormRequest extends FormRequest
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
            'debit_note_no' => 'nullable|string|unique:debit_notes,debit_note_no,' . $this->id,
            'invoice_no' => 'nullable|string|exists:invoices,invoice_no',
            'date' => 'nullable|date',
            'reason' => 'nullable|string',
            'note' => 'nullable|string',
            // 'grand_total' => 'required|numeric|min:0',
            'items' => [
                'nullable',
                'array',
                'min:1',
                'max:8',
                function ($attribute, $value, $fail) {
                    // Count only valid items (those with at least reference_no or particulars)
                    $validItems = collect($value)->filter(function ($item) {
                        return !empty($item['reference_no']) || !empty($item['particulars']);
                    })->count();
                    
                    if ($validItems > 8) {
                        $fail('Maximum of 8 valid items allowed per debit note.');
                    }
                }
            ],
            'items.*.reference_no' => 'nullable|string|max:50',
            'items.*.particulars' => 'nullable|string',
            'items.*.weight' => 'nullable|numeric|min:0',
            'items.*.wastage' => 'nullable|numeric|min:0',
            'items.*.total_weight' => 'nullable|numeric|min:0',
            // 'items.*.gold' => 'nullable|numeric|min:0',
            // 'items.*.gold_price_id' => 'nullable|exists:gold_prices,id',
            'items.*.workmanship' => [
                'nullable',
                'string',
                'min:0',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $itemType = $this->input("items.{$index}.item_type");
                    
                    if (str_contains($itemType, 'with-gold') && ($value === null || $value === '')) {
                        $fail('Workmanship is required for items with gold price.');
                    }
                }
            ],
            'items.*.quantity' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $itemType = $this->input("items.{$index}.item_type");
                    
                    if (!str_contains($itemType, 'with-gold') && ($value === null || $value === '')) {
                        $fail('Quantity is required for items without gold price.');
                    }
                }
            ],
            'items.*.unit_price' => [
                'nullable',
                'string',
                'min:0',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $itemType = $this->input("items.{$index}.item_type");
                    
                    if (!str_contains($itemType, 'with-gold') && ($value === null || $value === '')) {
                        $fail('Unit price is required for items without gold price.');
                    }
                }
            ],
            'items.*.total' => 'nullable|numeric|min:0',
            'subtotal' => 'nullable|numeric|min:0',
            'items.*.remark' => 'nullable|string',
            // 'currency_code' => 'required|string',
            // 'classification' => 'required|string',
            // 'tax_type' => 'required|string',
            // 'tax_rate' => 'required|string',
            // 'tax_amount' => 'required|string',
            // 'tax_exemption' => 'required|string',
            // 'amount_tax_exemption' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'debit_note_no.required' => 'Debit note number is required',
            'debit_note_no.unique' => 'Debit note number must be unique',
            'invoice_no.required' => 'Invoice number is required',
            'invoice_no.exists' => 'Selected invoice does not exist',
            'date.required' => 'Date is required',
            'items.required' => 'At least one item is required',
            'items.min' => 'At least one item is required',
            'items.*.reference_no.required' => 'Reference number is required for all items',
            'items.*.reference_no.regex' => 'Reference number must contain numbers only',
            'items.*.reference_no.max' => 'Reference number cannot exceed 50 characters',
            'items.*.particulars.required' => 'Particulars are required for all items',
            'items.*.weight.required' => 'Weight is required for all items',
            'items.*.wastage.required' => 'Wastage is required for all items',
            // 'items.*.gold.required' => 'Gold price is required for all items',
            // 'items.*.gold_price_id.required' => 'Gold price selection is required for all items',
            'items.*.workmanship.required' => 'Workmanship is required for all items',
            'items.*.quantity.required' => 'Quantity is required for all items',
            'items.*.unit_price.required' => 'Unit price is required for all items',
            'items.*.total.required' => 'Total is required for all items',
            'subtotal.required' => 'Subtotal is required',
            'currency_code.required' => 'Currency code is required',
            'classification.required' => 'Classification is required',
            'tax_type.required' => 'Tax type is required',
            'tax_rate.required' => 'Tax rate is required',
            'tax_amount.required' => 'Tax amount is required',
            'tax_exemption.required' => 'Tax exemption is required',
        ];
    }
}