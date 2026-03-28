<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'min:2', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'price'       => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'stock'       => ['required', 'integer', 'min:0'],
            'category'    => ['nullable', 'string', 'max:100'],
            'image'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_active'   => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Le nom du produit est obligatoire.',
            'price.required'       => 'Le prix est obligatoire.',
            'price.min'            => 'Le prix doit être supérieur à 0.',
            'stock.required'       => 'Le stock est obligatoire.',
            'stock.min'            => 'Le stock ne peut pas être négatif.',
        ];
    }
}
