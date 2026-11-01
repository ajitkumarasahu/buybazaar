<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class LoginRequest
 *
 * This class handles the validation of login requests.
 * It ensures that the email and password fields are present and valid.
 */
class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    // /**
    //  * Prepare the data for validation.
    //  * This method trims and lowercases the email input to ensure consistency.
    //  */
    // protected function prepareForValidation(): void
    // {
    //     if ($this->has('email')) {
    //         $this->merge(['email' => strtolower(trim((string) $this->input('email')))]);
    //     }
    // }

    /**
     * Prepare the data for validation.
     * This method trims and lowercases the email input to ensure consistency.
     */
    protected function prepareForValidation(): void
    {
        //
        if (is_string($this->input('email'))) {
            $this->merge(['email' => strtolower(trim($this->input('email')))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}