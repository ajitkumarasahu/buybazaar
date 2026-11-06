<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // the route already requires a logged-in user
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        //if the phone number is a string, remove spaces, dashes, and parentheses
        if (is_string($this->input('phone'))) {
            // "+91 98765-43210" -> "+919876543210"
            $this->merge(['phone' => preg_replace('/[\s\-()]/', '', $this->input('phone'))]);
        }
    }

    public function rules(): array
    {
        // Email is not editable here: changing it needs re-verification (later).
        return [
            // 'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'regex:/^\+?[0-9]{7,15}$/'],
        ];
    }
}