<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\CustomerProfile;
use App\Models\OtherProfile;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SelfBilledInvoiceFormRequest extends FormRequest
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
        $selfBilledInvoiceId = $this->route('id'); // Get the invoice ID from the route if it exists

        return [
            'company_name'  => 'nullable',
            // TO CHECK IF COMPANY NAME ALREADY EXISTS IN CUSTOMER PROFILE OR OTHER PROFILE
            'other_company_name' => [
                'required_if:company_name,Other',
                function ($attribute, $value, $fail) {
                    if ($this->input('company_name') === 'Other') {
                        // Check CustomerProfile
                        $existsInCustomer = CustomerProfile::where('company_name', $value)
                            ->where('status', 0)
                            ->exists();

                        // Check OtherProfile
                        $existsInOther = OtherProfile::where('company_name', $value)
                            ->where('status', 0)
                            ->exists();

                        if ($existsInCustomer || $existsInOther) {
                            $fail('This company name already exists.');
                        }
                    }
                }
            ],
            'self_billed_invoice_date'  => 'nullable|date',
            'self_billed_invoice_no'  => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('self_billed_invoices', 'self_billed_invoice_no')
                    ->where('status', '0')
                    ->ignore($selfBilledInvoiceId)
            ],
            // 'goods_received_by'  => 'nullable',
            // 'payment_received_by'  => 'nullable',
            'gold_price_id' => 'nullable|exists:gold_prices,id',
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
                        $fail('Maximum of 8 valid items allowed per self-billed invoice.');
                    }
                }
            ],
            'items.*.reference_no' => 'nullable|string|max:50',
            'items.*.particulars' => 'nullable|string',
            'items.*.weight' => 'nullable|numeric|min:0',
            'items.*.wastage' => 'nullable|numeric|min:0',
            'items.*.total_weight' => 'nullable|numeric|min:0',
            // 'items.*.gold_price_id' => 'nullable|exists:gold_prices,id',
            // 'items.*.gold' => 'nullable|numeric|min:0',
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
                    
                    if (!str_contains($itemType, 'with-gold') && empty($value)) {
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
                    
                    if (!str_contains($itemType, 'with-gold') && empty($value)) {
                        $fail('Unit price is required for items without gold price.');
                    }
                }
            ],
            'items.*.total' => 'nullable|numeric|min:0',
            // 'currency_code' => 'required|string',
            // 'classification' => 'required|string',
            // 'tax_type' => 'required|string',
            // 'tax_rate' => 'required|string',
            // 'tax_amount' => 'required|string',
            // 'tax_exemption' => 'required|string',
            // 'amount_tax_exemption' => 'required|string',
            'items.*.pure_gold' => 'nullable|string',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'company_name.required' => 'Please select or enter a company name',
            'self_billed_invoice_date.required' => 'Invoice date is required',
            'self_billed_invoice_date.date' => 'Please enter a valid date',
            'self_billed_invoice_no.required' => 'Invoice number is required',
            'self_billed_invoice_no.max' => 'Invoice number cannot exceed 50 characters',
            'self_billed_invoice_no.unique' => 'This invoice number is already in use',
            'items.required' => 'At least one invoice item is required',
            'items.min' => 'At least one invoice item is required',
            'items.*.reference_no.required' => 'Reference number is required for all items',
            'items.*.reference_no.max' => 'Reference number cannot exceed 50 characters',
            'items.*.particulars.required' => 'Particulars are required for all items',
            'items.*.weight.required' => 'Weight is required for all items',
            'items.*.weight.numeric' => 'Weight must be a number',
            'items.*.weight.min' => 'Weight cannot be negative',
            'items.*.wastage.required' => 'Wastage is required for all items',
            'items.*.wastage.numeric' => 'Wastage must be a number',
            'items.*.wastage.min' => 'Wastage cannot be negative',
            'items.*.total_weight.required' => 'Total weight is required for all items',
            'items.*.total_weight.numeric' => 'Total weight must be a number',
            'items.*.total_weight.min' => 'Total weight cannot be negative',
            'items.*.gold_price_id.required' => 'Gold price must be selected for all items',
            'items.*.gold_price_id.exists' => 'Selected gold price is invalid',
            'items.*.gold.required' => 'Gold amount is required for all items',
            'items.*.gold.numeric' => 'Gold amount must be a number',
            'items.*.gold.min' => 'Gold amount cannot be negative',
            'items.*.workmanship.required' => 'Workmanship is required for all items',
            'items.*.workmanship.numeric' => 'Workmanship must be a number',
            'items.*.workmanship.min' => 'Workmanship cannot be negative',
            'items.*.total.required' => 'Total amount is required for all items',
            'items.*.total.numeric' => 'Total amount must be a number',
            'items.*.total.min' => 'Total amount cannot be negative',
            'currency_code.required' => 'Currency code is required',
            'classification.required' => 'Classification is required',
            'tax_type.required' => 'Tax type is required',
            'tax_rate.required' => 'Tax rate is required',
            'tax_amount.required' => 'Tax amount is required',
            'tax_exemption.required' => 'Tax exemption is required',
            'amount_tax_exemption.required' => 'Amount tax exemption is required',
            'items.*.pure_gold.required' => 'Pure gold is required for all items',
            'items.*.pure_gold.string' => 'Pure gold must be a string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if (request()->wantsJson()) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422));
        }

        parent::failedValidation($validator);
    }
}