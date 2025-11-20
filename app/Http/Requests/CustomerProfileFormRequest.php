<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\OtherProfile;

class CustomerProfileFormRequest extends FormRequest
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
        $rules = [
            'company_name' => [
                'required',
                Rule::unique('customer_profiles')->where(function ($query) {
                    return $query->where('status', 0);
                }),
                function($attribute, $value, $fail) {
                    // Check if exists in others table
                    $exists = OtherProfile::where('company_name', $value)
                        ->where('status', 0)
                        ->exists();
                    
                    if ($exists) {
                        $fail('This company name already exists in Other Profiles.');
                    }
                }
            ],
            // 'address_line_1' => 'required',
            // 'state' => 'required',
            // 'city' => 'required',
            // 'postcode' => 'required',
            // 'contact_name_1'  => 'required',
            // 'contact_1'  => 'required',
            // 'email_1'  => 'required',
            // 'contact_name_2'  => 'required',
            // 'contact_2'  => 'required',
            // 'email_2'  => 'required',
            // 'contact_name_3'  => 'required',
            // 'contact_3'  => 'required',
            // 'email_3'  => 'required',
        ];

        // If this is an update, exclude the current record
        if ($this->route('id')) {
            $rules['company_name'][1] = Rule::unique('customer_profiles')
                ->where('status', 0)
                ->ignore($this->route('id'));
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'company_name.required' => 'The company name field is required.',
            'company_name.unique' => 'This company name already exists in Customer Profiles.',
        ];
    }
}
