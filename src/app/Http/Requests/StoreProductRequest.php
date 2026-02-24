<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create products');
    }

    public function rules(): array
    {
        return [
            'category_id'    => ['nullable', 'exists:categories,id'],
            'name'           => ['required', 'string', 'max:200'],
            'sku'            => ['nullable', 'string', 'max:50', 'unique:products,sku'],
            'description'    => ['nullable', 'string'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price'     => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'min_stock'      => ['nullable', 'integer', 'min:0'],
            'image'          => ['nullable', 'image', 'max:2048'],
            'status'         => ['nullable', 'in:active,inactive'],
        ];
    }
}
