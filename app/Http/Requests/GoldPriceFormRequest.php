<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GoldPriceFormRequest extends FormRequest
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
        $goldPriceId = $this->route('id'); // Get the gold price ID from the route if it exists

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('gold_prices', 'name')
                    ->where('status', '0')
                    ->ignore($goldPriceId)
            ],
            'price' => 'required|numeric|min:0',
            'date_change' => 'required|date',
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
            'name.required' => 'Gold price name is required',
            'name.max' => 'Gold price name cannot exceed 255 characters',
            'name.unique' => 'This gold price name is already in use',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
            'price.min' => 'Price cannot be negative',
            'date_change.required' => 'Date is required',
            'date_change.date' => 'Please enter a valid date',
        ];
    }
}
