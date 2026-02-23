<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clienteId = $this->route('cliente')?->id;

        return [
            'nome'          => ['required', 'string', 'max:255'],
            'tipo'          => ['required', 'in:pessoa_fisica,pessoa_juridica'],
            'email'         => ['nullable', 'email', 'max:255', "unique:clientes,email,{$clienteId}"],
            'telefone'      => ['nullable', 'string', 'max:20'],
            'cpf_cnpj'      => ['nullable', 'string', 'max:20', "unique:clientes,cpf_cnpj,{$clienteId}"],
            'logradouro'    => ['nullable', 'string', 'max:255'],
            'numero'        => ['nullable', 'string', 'max:20'],
            'bairro'        => ['nullable', 'string', 'max:100'],
            'cidade'        => ['nullable', 'string', 'max:100'],
            'estado'        => ['nullable', 'string', 'max:2'],
            'cep'           => ['nullable', 'string', 'max:10'],
            'ativo'         => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'    => 'O nome é obrigatório.',
            'tipo.required'    => 'O tipo de pessoa é obrigatório.',
            'tipo.in'          => 'Tipo inválido.',
            'email.email'      => 'Informe um e-mail válido.',
            'email.unique'     => 'Este e-mail já está cadastrado.',
            'cpf_cnpj.unique'  => 'Este CPF/CNPJ já está cadastrado.',
        ];
    }
}
