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
            'company_name'  => 'required',
            'address_line_1'  => 'required',
            'address_line_2'  => 'required',
            'state'  => 'required',
            'city'  => 'required',
            'postcode'  => 'required',
            'email'  => 'required',
            'contact'  => 'required',
            'tin'  => 'required',
            'sst_registration_no'  => 'required',
            'msic_code'  => 'required',
            // 'business_registration_number'  => 'required',
            // 'identification_number'  => 'required',
            // 'passport_number'  => 'required',
        ];
    }
}
