<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class VendorUpdateRequest extends FormRequest
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
            'full_name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('vendors')->ignore($this->vendor)
            ],
            'phone' => 'sometimes|string|max:20',
            'store_name' => 'sometimes|string|max:255',
            'store_description' => 'nullable|string|max:1000',
            'business_category' => 'sometimes|string|max:100',
            'address' => 'sometimes|string|max:500',
            'business_hours' => 'nullable|array',
            'business_hours.*.day' => 'required_with:business_hours|string',
            'business_hours.*.open' => 'required_with:business_hours|string',
            'business_hours.*.close' => 'required_with:business_hours|string',
            'payment_details' => 'nullable|array',
            'payment_details.bank_name' => 'required_with:payment_details|string',
            'payment_details.account_number' => 'required_with:payment_details|string',
            'payment_details.account_name' => 'required_with:payment_details|string',
            'store_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered',
            'store_name.string' => 'Store name must be a valid text',
            'phone.string' => 'Please enter a valid phone number',
            'address.string' => 'Please enter a valid address',
            'store_logo.image' => 'The logo must be an image file',
            'store_logo.max' => 'Logo size should not exceed 2MB',
        ];
    }
}
