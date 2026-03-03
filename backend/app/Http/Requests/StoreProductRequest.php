<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3'],
            'sale_price' => ['required', 'numeric', 'gt:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'name is required',
            'name.min' => 'name must be at least 3 characters',
            'sale_price.gt' => 'sale_price must be greater than 0',
        ];
    }
}
