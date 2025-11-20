<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IsFormRequest extends FormRequest
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
            'is_no' => [
                'required',
                Rule::unique('is', 'is_no')->ignore($this->route('id')),
            ],
            'is_date' => 'required|date',
            'company_name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'is_no.required' => 'The is number is required.',
            'is_no.unique' => 'This is number has already been taken.',
            'company_name.required' => 'The company name is required.',
            'company_name.string' => 'The company name must be a valid string.',
            'company_name.max' => 'The company name may not be greater than 255 characters.',
            'weight.required' => 'The weight is required.',
            'weight.numeric' => 'The weight must be a valid number.',
            'weight.min' => 'The weight must be at least 0.'
        ];
    }
}
