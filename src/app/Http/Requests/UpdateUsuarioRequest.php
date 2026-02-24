<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $usuarioId = $this->route('usuario')?->id ?? $this->route('usuario');

        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuarioId)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role'     => ['required', 'string', 'exists:roles,name'],
            'ativo'    => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email'    => 'Informe um e-mail válido.',
            'email.unique'   => 'Este e-mail já está em uso.',
            'role.required'  => 'Selecione um perfil de acesso.',
            'role.exists'    => 'O perfil selecionado não existe.',
        ];
    }
}
