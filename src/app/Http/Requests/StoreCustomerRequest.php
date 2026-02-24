<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('customer')?->id;

        return [
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['nullable', 'string', 'email', 'max:150', "unique:customers,email,{$id}"],
            'phone'    => ['nullable', 'string', 'max:20'],
            'document' => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string'],
            'city'     => ['nullable', 'string', 'max:100'],
            'state'    => ['nullable', 'string', 'max:2'],
        ];
    }
}
