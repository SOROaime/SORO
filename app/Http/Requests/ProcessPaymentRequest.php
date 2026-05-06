<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // Données de livraison
            'shipping_address'     => ['required', 'string', 'min:5'],
            'shipping_city'        => ['required', 'string', 'min:2'],
            'shipping_postal_code' => ['required', 'string', 'min:4', 'max:10'],
            'notes'                => ['nullable', 'string', 'max:500'],

            // Données carte (simulées - jamais stockées en entier)
            'card_holder_name'     => ['required', 'string', 'min:2', 'max:100'],
            'card_number'          => ['required', 'string', 'regex:/^\d{16}$/'],  // 16 chiffres
            'card_expiry'          => ['required', 'string', 'regex:/^\d{2}\/\d{2}$/'], // MM/YY
            'card_cvv'             => ['required', 'string', 'regex:/^\d{3,4}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_address.required'     => 'L\'adresse de livraison est obligatoire.',
            'shipping_city.required'        => 'La ville est obligatoire.',
            'shipping_postal_code.required' => 'Le code postal est obligatoire.',
            'shipping_postal_code.regex'    => 'Le code postal doit contenir 5 chiffres.',
            'card_holder_name.required'     => 'Le nom du porteur de carte est obligatoire.',
            'card_number.required'          => 'Le numéro de carte est obligatoire.',
            'card_number.regex'             => 'Le numéro de carte doit contenir 16 chiffres.',
            'card_expiry.required'          => 'La date d\'expiration est obligatoire.',
            'card_expiry.regex'             => 'Format de date invalide. Utilisez MM/AA.',
            'card_cvv.required'             => 'Le CVV est obligatoire.',
            'card_cvv.regex'                => 'Le CVV doit contenir 3 ou 4 chiffres.',
        ];
    }
}
