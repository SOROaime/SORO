@extends('layouts.app')

@section('title', 'Finaliser la commande')

@section('content')
<div class="container py-5">
    <h1 class="section-title mb-2">
        <i class="bi bi-credit-card me-2"></i>Finaliser la commande
    </h1>
    <p class="text-muted mb-4">Étape 1 sur 1 — Livraison &amp; Paiement</p>

    {{-- Erreurs de validation --}}
    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Veuillez corriger les erreurs suivantes :</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('payment.process') }}" method="POST" id="checkoutForm">
        @csrf

        <div class="row g-4">
            {{-- FORMULAIRES --}}
            <div class="col-lg-7">

                {{-- LIVRAISON --}}
                <div class="card border-0 rounded-4 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-truck me-2 text-primary"></i>Adresse de livraison
                        </h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Adresse</label>
                            <input type="text" name="shipping_address"
                                   class="form-control @error('shipping_address') is-invalid @enderror"
                                   placeholder="12 rue de la Paix"
                                   value="{{ old('shipping_address', auth()->user()->address) }}">
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row g-3">
                            <div class="col-8">
                                <label class="form-label fw-semibold">Ville</label>
                                <input type="text" name="shipping_city"
                                       class="form-control @error('shipping_city') is-invalid @enderror"
                                       placeholder="Paris"
                                       value="{{ old('shipping_city') }}">
                                @error('shipping_city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-semibold">Code postal</label>
                                <input type="text" name="shipping_postal_code"
                                       class="form-control @error('shipping_postal_code') is-invalid @enderror"
                                       placeholder="75001"
                                       value="{{ old('shipping_postal_code') }}"
                                       maxlength="5">
                                @error('shipping_postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label fw-semibold">Notes (optionnel)</label>
                            <textarea name="notes" class="form-control" rows="2"
                                      placeholder="Instructions particulières pour la livraison...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- PAIEMENT --}}
                <div class="card border-0 rounded-4 shadow-sm">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-credit-card me-2 text-primary"></i>Informations de paiement
                            </h5>
                            <div class="d-flex gap-1">
                                <span class="badge bg-secondary"><i class="bi bi-shield-lock"></i> SSL</span>
                                <span class="badge bg-info">Simulé</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">

                        <div class="alert alert-info small mb-4 rounded-3">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Mode démo :</strong> Utilisez n'importe quel numéro de carte fictif.
                            Aucune vraie transaction ne sera effectuée.
                        </div>

                        {{-- Nom porteur --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nom du porteur</label>
                            <input type="text" name="card_holder_name"
                                   class="form-control @error('card_holder_name') is-invalid @enderror"
                                   placeholder="JEAN DUPONT"
                                   value="{{ old('card_holder_name', strtoupper(auth()->user()->name)) }}">
                            @error('card_holder_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Numéro carte --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Numéro de carte</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-credit-card text-primary"></i>
                                </span>
                                <input type="text" name="card_number" id="cardNumber"
                                       class="form-control @error('card_number') is-invalid @enderror"
                                       placeholder="1234 5678 9012 3456"
                                       maxlength="19"
                                       value="{{ old('card_number') }}">
                                @error('card_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3">
                            {{-- Expiration --}}
                            <div class="col-6">
                                <label class="form-label fw-semibold">Date d'expiration</label>
                                <input type="text" name="card_expiry" id="cardExpiry"
                                       class="form-control @error('card_expiry') is-invalid @enderror"
                                       placeholder="MM/AA"
                                       maxlength="5"
                                       value="{{ old('card_expiry') }}">
                                @error('card_expiry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- CVV --}}
                            <div class="col-6">
                                <label class="form-label fw-semibold">
                                    CVV
                                    <i class="bi bi-question-circle text-muted ms-1"
                                       data-bs-toggle="tooltip"
                                       title="3 chiffres au dos de votre carte"></i>
                                </label>
                                <input type="password" name="card_cvv"
                                       class="form-control @error('card_cvv') is-invalid @enderror"
                                       placeholder="•••"
                                       maxlength="4"
                                       value="{{ old('card_cvv') }}">
                                @error('card_cvv')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3 small text-muted">
                            <i class="bi bi-shield-check text-success me-1"></i>
                            Vos données de carte ne sont jamais stockées intégralement.
                        </div>
                    </div>
                </div>
            </div>

            {{-- RÉSUMÉ COMMANDE --}}
            <div class="col-lg-5">
                <div class="card border-0 rounded-4 shadow-sm sticky-top" style="top: 80px;">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold">Votre commande</h5>
                    </div>
                    <div class="card-body px-4">
                        @foreach($cart->items as $item)
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <img
                                    src="{{ $item->product->image_url }}"
                                    alt="{{ $item->product->name }}"
                                    class="rounded-2"
                                    style="width: 50px; height: 50px; object-fit: cover;"
                                    onerror="this.src='https://placehold.co/50x50/e2e8f0/94a3b8?text=?'"
                                >
                                <div class="flex-grow-1">
                                    <div class="fw-semibold small">{{ Str::limit($item->product->name, 30) }}</div>
                                    <div class="text-muted small">× {{ $item->quantity }}</div>
                                </div>
                                <div class="fw-bold small">{{ $item->formatted_subtotal }}</div>
                            </div>
                        @endforeach

                        <hr>

                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Sous-total</span>
                            <span>{{ $cart->formatted_total }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Livraison</span>
                            <span class="text-success fw-semibold">Gratuite</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                            <span>Total à payer</span>
                            <span class="text-primary">{{ $cart->formatted_total }}</span>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold" id="payBtn">
                            <i class="bi bi-lock-fill me-2"></i>Payer {{ $cart->formatted_total }}
                        </button>

                        <div class="text-center mt-3 small text-muted">
                            <i class="bi bi-shield-lock me-1"></i>Paiement sécurisé SSL
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Formatage automatique du numéro de carte
    document.getElementById('cardNumber').addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, '').replace(/(.{4})/g, '$1 ').trim();
        e.target.value = v;
        // Extraction des chiffres pour la validation (sans espaces)
    });

    // Formatage date expiration MM/AA
    document.getElementById('cardExpiry').addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, '');
        if (v.length >= 2) v = v.substring(0,2) + '/' + v.substring(2);
        e.target.value = v;
    });

    // Nettoyage du numéro de carte avant soumission (sans espaces)
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        const cardInput = document.getElementById('cardNumber');
        const payBtn    = document.getElementById('payBtn');
        cardInput.value = cardInput.value.replace(/\s+/g, '');
        payBtn.disabled = true;
        payBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Traitement...';
    });

    // Tooltips Bootstrap
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });
</script>
@endpush
@endsection
