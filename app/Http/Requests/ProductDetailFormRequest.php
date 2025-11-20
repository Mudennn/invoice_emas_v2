<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductDetailFormRequest extends FormRequest
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
        $productId = $this->route('id'); // Get the product ID from the route if it exists

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_details', 'name')
                    ->where('status', '0')
                    ->ignore($productId)
            ],
            'code' => [
                'required',
                'string',
                'regex:/^\d+$/', // Only numbers allowed
                Rule::unique('product_details', 'code')
                    ->where('status', '0')
                    ->where(function($query) {
                        // Check uniqueness with WO- prefix
                        $query->where('code', 'WO-' . $this->code);
                    })
                    ->ignore($productId)
            ],
            'category' => 'required',
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
            'name.required' => 'Product name is required',
            'name.max' => 'Product name cannot exceed 255 characters',
            'name.unique' => 'This product name is already in use',
            'code.required' => 'Product code is required',
            'code.regex' => 'Please enter numbers only',
            'code.unique' => 'This product code is already in use',
            'category.required' => 'Category is required',
        ];
    }
}
