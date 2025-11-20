<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MsicFormRequest extends FormRequest
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
            'msic_code' => 'required|string|max:5',
            'description' => 'required|string|max:255',
            'category_reference' => 'nullable|string|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'msic_code.required' => 'The MSIC code is required.',
            'msic_code.string' => 'The MSIC code must be a string.',
            'msic_code.max' => 'The MSIC code must be less than 255 characters.',
            'description.required' => 'The description is required.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description must be less than 255 characters.',
            'category_reference.required' => 'The category reference is required.',
            'category_reference.string' => 'The category reference must be a string.',
            'category_reference.max' => 'The category reference must be less than 10 characters.',
        ];
    }
}
