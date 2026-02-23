<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProdutoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $produtoId = $this->route('produto')?->id;

        return [
            'nome'               => ['required', 'string', 'max:255'],
            'categoria_id'       => ['nullable', 'exists:categorias,id'],
            'codigo_sku'         => ['nullable', 'string', 'max:100', "unique:produtos,codigo_sku,{$produtoId}"],
            'descricao'          => ['nullable', 'string'],
            'preco_custo'        => ['required', 'numeric', 'min:0'],
            'preco_venda'        => ['required', 'numeric', 'min:0'],
            'quantidade_estoque' => ['required', 'integer', 'min:0'],
            'estoque_minimo'     => ['required', 'integer', 'min:0'],
            'unidade'            => ['required', 'string', 'max:10'],
            'ativo'              => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'               => 'O nome do produto é obrigatório.',
            'preco_custo.required'        => 'O preço de custo é obrigatório.',
            'preco_venda.required'        => 'O preço de venda é obrigatório.',
            'quantidade_estoque.required' => 'A quantidade em estoque é obrigatória.',
            'estoque_minimo.required'     => 'O estoque mínimo é obrigatório.',
            'unidade.required'            => 'A unidade é obrigatória.',
            'codigo_sku.unique'           => 'Este código SKU já está em uso.',
        ];
    }
}
