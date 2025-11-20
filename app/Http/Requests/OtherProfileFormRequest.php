<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\CustomerProfile;

class OtherProfileFormRequest extends FormRequest
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
                Rule::unique('others')->where(function ($query) {
                    return $query->where('status', 0);
                }),
                function($attribute, $value, $fail) {
                    // Check if exists in customer_profiles table
                    $exists = CustomerProfile::where('company_name', $value)
                        ->where('status', 0)
                        ->exists();
                    
                    if ($exists) {
                        $fail('This company name already exists in Customer Profiles.');
                    }
                }
            ],
            // 'address_line_1' => 'required',
            // 'state' => 'required',
            // 'city' => 'required',
            // 'postcode' => 'required',
        ];

        // If this is an update, exclude the current record
        if ($this->route('id')) {
            $rules['company_name'][1] = Rule::unique('others')
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
            'company_name.unique' => 'This company name already exists in Other Profiles.',
        ];
    }
}
