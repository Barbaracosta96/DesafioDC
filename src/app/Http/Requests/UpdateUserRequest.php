<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        $id = $this->route('user')->id;

        return [
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'string', 'email', 'max:150', "unique:users,email,{$id}"],
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
            'role'     => ['required', 'string', 'exists:roles,name'],
        ];
    }
}
