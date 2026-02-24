<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'O nome é obrigatório.',
            'email.required'     => 'O e-mail é obrigatório.',
            'email.unique'       => 'Este e-mail já está cadastrado.',
            'password.required'  => 'A senha é obrigatória.',
            'password.confirmed' => 'A confirmação de senha não confere.',
        ];
    }
}
