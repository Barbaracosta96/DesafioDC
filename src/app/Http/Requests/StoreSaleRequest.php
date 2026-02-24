<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create sales');
    }

    public function rules(): array
    {
        return [
            'customer_id'          => ['nullable', 'exists:customers,id'],
            'discount'             => ['nullable', 'numeric', 'min:0'],
            'notes'                => ['nullable', 'string', 'max:500'],
            'items'                => ['required', 'array', 'min:1'],
            'items.*.product_id'   => ['required', 'exists:products,id'],
            'items.*.quantity'     => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'             => 'A venda precisa ter pelo menos um item.',
            'items.*.product_id.required' => 'Produto inválido.',
            'items.*.quantity.min'       => 'A quantidade mínima é 1.',
        ];
    }
}
