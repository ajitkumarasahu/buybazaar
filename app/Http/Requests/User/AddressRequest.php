<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    /* if user is authenticated, then authorize the request */
    public function authorize(): bool
    {
        return true; // ownership is checked by the route's policy
    }

    /**
     * Prepare the data for validation.
     * This method is called before the validation rules are applied. 
     * It can be used to modify the request data, such as normalizing phone numbers or country codes.
     */
    protected function prepareForValidation(): void
    {
        // Normalize the phone number by removing spaces, dashes, and parentheses.
        if (is_string($this->input('phone'))) {
            $this->merge(['phone' => preg_replace('/[\s\-()]/', '', $this->input('phone'))]);
        }

        // Normalize the country code to uppercase and trim whitespace.
        if (is_string($this->input('country'))) {
            $this->merge(['country' => strtoupper(trim($this->input('country')))]);
        }
    }

    // Define the validation rules for the address request. These rules ensure that the input data is in the correct format and meets the specified criteria.
    public function rules(): array
    {
        // Validation rules for creating or updating an address. The rules ensure that the input data is in the correct format and meets the specified criteria.
        $rules = [
            'label' => ['nullable', 'string', 'max:30'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^\+?[0-9]{7,15}$/'],
            'line1' => ['required', 'string', 'max:150'],
            'line2' => ['nullable', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:80'],
            'state' => ['required', 'string', 'max:80'],
            'postal_code' => ['required', 'string', 'max:12'],
            'country' => ['required', 'string', 'alpha', 'size:2'],
        ];

        // Only when creating. To change the default later, clients use the
        // dedicated "set default" endpoint, so an update can never leave
        // the user with no default address.
        if ($this->isMethod('post')) {
            $rules['is_default'] = ['sometimes', 'boolean'];
        }

        // Only when updating. To change the default later, clients use the
        // dedicated "set default" endpoint, so an update can never leave
        return $rules;
    }
}