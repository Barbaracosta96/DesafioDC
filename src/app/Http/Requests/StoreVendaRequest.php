<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id'             => ['nullable', 'exists:clientes,id'],
            'forma_pagamento'        => ['required', 'string', 'in:dinheiro,cartao_credito,cartao_debito,pix'],
            'desconto'               => ['nullable', 'numeric', 'min:0'],
            'observacoes'            => ['nullable', 'string', 'max:1000'],
            'itens'                  => ['required', 'array', 'min:1'],
            'itens.*.produto_id'     => ['required', 'exists:produtos,id'],
            'itens.*.quantidade'     => ['required', 'integer', 'min:1'],
            'itens.*.preco_unitario' => ['required', 'numeric', 'min:0'],
            'itens.*.desconto'       => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'forma_pagamento.required' => 'A forma de pagamento é obrigatória.',
            'forma_pagamento.in'       => 'Forma de pagamento inválida.',
            'itens.required'           => 'Adicione ao menos um produto à venda.',
            'itens.min'                => 'Adicione ao menos um produto à venda.',
            'itens.*.produto_id.required' => 'Produto inválido.',
            'itens.*.produto_id.exists'   => 'Produto não encontrado.',
            'itens.*.quantidade.required' => 'Informe a quantidade.',
            'itens.*.quantidade.min'      => 'A quantidade mínima é 1.',
        ];
    }
}
