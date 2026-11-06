<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * ChangePasswordRequest handles the validation of user password change requests.
 * It ensures that the current password is provided, the new password meets security requirements,
 * and that the new password is confirmed and different from the current password.
 */
class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * In this case, any authenticated user can change their password.
     * @return bool
     */
    public function authorize(): bool //if user is authenticated, then authorize the request
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * These rules ensure that the current password is provided, the new password meets security requirements,
     * and that the new password is confirmed and different from the current password.
     * @return array
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'string',
                'confirmed',
                'different:current_password',
                Password::min(8)->letters()->numbers(), // Password must be at least 8 characters long and contain both letters and numbers.
            ],
        ];
    }
}