<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Couche 5 — Validation stricte + Sanitization
 *
 * prepareForValidation() nettoie les entrées avant validation :
 *  - strip_tags()  → élimine toute balise HTML/script (anti-XSS)
 *  - trim()        → supprime les espaces superflus
 *  - preg_replace  → normalise le numéro de téléphone (chiffres et + uniquement)
 *  - htmlspecialchars → encode les caractères spéciaux résiduels
 */
class ProcessPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'shipping_address'  => $this->sanitizeText($this->shipping_address),
            'shipping_phone'    => $this->sanitizePhone($this->shipping_phone),
            'shipping_city'     => $this->sanitizeText($this->shipping_city),
            'shipping_commune'  => $this->sanitizeText($this->shipping_commune),
            'shipping_quartier' => $this->sanitizeText($this->shipping_quartier),
            'notes'             => $this->sanitizeText($this->notes),
            'coupon_code'       => $this->coupon_code ? strtoupper(trim(strip_tags($this->coupon_code))) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'payment_method'    => ['required', 'in:geniuspay,cash_on_delivery'],
            'installment_count' => ['required', 'integer', 'in:1,2,3,4'],
            'shipping_address'  => ['nullable', 'string', 'min:5', 'max:255'],
            // Format téléphone CI : 07 XX XX XX XX ou +225 XX XX XX XX XX
            'shipping_phone'    => ['required', 'string', 'min:8', 'max:25', 'regex:/^[+0-9\s\-]{8,25}$/'],
            'shipping_city'     => ['required', 'string', 'min:2', 'max:100'],
            'shipping_commune'  => ['required', 'string', 'min:2', 'max:100'],
            'shipping_quartier' => ['required', 'string', 'min:2', 'max:100'],
            'notes'             => ['nullable', 'string', 'max:500'],
            'coupon_code'       => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required'    => 'Veuillez choisir un mode de paiement.',
            'payment_method.in'          => 'Mode de paiement invalide.',
            'installment_count.in'       => 'Le nombre de tranches doit être 1, 2, 3 ou 4.',
            'shipping_phone.required'    => 'Le numéro de téléphone de livraison est obligatoire.',
            'shipping_phone.min'         => 'Le numéro de téléphone doit comporter au moins 8 chiffres.',
            'shipping_phone.regex'       => 'Le numéro de téléphone contient des caractères non valides.',
            'shipping_city.required'     => 'La ville est obligatoire.',
            'shipping_commune.required'  => 'La commune est obligatoire.',
            'shipping_quartier.required' => 'Le quartier est obligatoire.',
            'notes.max'                  => 'Les notes ne peuvent pas dépasser 500 caractères.',
        ];
    }

    // ── Méthodes privées de sanitization ──────────────────────────────

    private function sanitizeText(?string $value): ?string
    {
        if ($value === null) return null;
        // 1. Supprimer toutes les balises HTML (anti-XSS et injection)
        $value = strip_tags($value);
        // 2. Encoder les caractères spéciaux HTML résiduels
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // 3. Supprimer les espaces superflus
        return trim($value);
    }

    private function sanitizePhone(?string $value): ?string
    {
        if ($value === null) return null;
        // Supprimer tout sauf chiffres, +, espaces et tirets
        $value = preg_replace('/[^0-9+\s\-]/', '', $value ?? '');
        return trim($value);
    }
}
