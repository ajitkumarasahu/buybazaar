<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
//any one can register no need for any token 
    public function authorize(): bool
    {
        return true; // anyone may register
    }

    protected function prepareForValidation(): void
    {
        // "Ravi@Mail.com " and "ravi@mail.com" must be the same account.
        if ($this->has('email')) {
            //convert email to lowercase and trim whitespace before validation
            $this->merge(['email' => strtolower(trim((string) $this->input('email')))]);
        }
    }

    //rules is mandatory 
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],  //name are required 
            'email' => ['required', 'string', 'email:rfc', 'max:255', 'unique:users,email'], //email are required 
            'password' => ['required', 'string', 'confirmed',  //password are requried and must be confirmed with password_confirmation field
            Password::min(8)->letters()->numbers(),],
        ];
    }
}