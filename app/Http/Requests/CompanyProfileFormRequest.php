<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyProfileFormRequest extends FormRequest
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
            'company_name'  => 'required|string|max:255',
            'address_line_1'  => 'required|string|max:255',
            'address_line_2'  => 'required|string|max:255',
            'state'  => 'required|exists:selections,id',
            'city'  => 'required|string|max:100',
            'postcode'  => 'required|string|max:10',
            'email'  => 'required|email|max:255',
            'contact'  => 'required|string|max:20',
            'tin'  => 'required|string|regex:/^C[0-9]{10}$/|unique:company_profiles,tin',
            'sst_registration_no'  => 'required|string|max:50',
            'msic_code'  => 'required|string|exists:msics,msic_code',
            // 'business_registration_number'  => 'required',
            // 'identification_number'  => 'required',
            // 'passport_number'  => 'required',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'company_name.required' => 'Company name is required.',
            'company_name.string' => 'Company name must be a valid string.',
            'company_name.max' => 'Company name must not exceed 255 characters.',
            'address_line_1.required' => 'Address line 1 is required.',
            'address_line_2.required' => 'Address line 2 is required.',
            'state.required' => 'State is required.',
            'state.exists' => 'The selected state is invalid.',
            'city.required' => 'City is required.',
            'postcode.required' => 'Postcode is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'contact.required' => 'Contact number is required.',
            'tin.required' => 'TIN (Tax Identification Number) is required.',
            'tin.regex' => 'TIN must start with "C" followed by 10 digits (e.g., C1234567890).',
            'tin.unique' => 'This TIN is already registered in the system.',
            'sst_registration_no.required' => 'SST Registration Number is required.',
            'msic_code.required' => 'MSIC Code is required.',
            'msic_code.exists' => 'The selected MSIC Code is invalid.',
        ];
    }
}
