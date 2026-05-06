@extends('layouts.app')

@section('title', 'Finaliser la commande')

@section('content')
<div class="container py-5">

    {{-- En-tête + étapes --}}
    <div class="mb-5">
        <div class="d-flex align-items-center gap-3 mb-3">
            <a href="{{ route('cart.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1 class="section-title mb-0">
                <i class="bi bi-credit-card me-2" style="color:var(--accent)"></i>Finaliser la commande
            </h1>
        </div>

        {{-- Indicateur d'étapes --}}
        <div class="d-flex align-items-center gap-2 ms-5 ps-3" style="font-size:.82rem;">
            <span class="fw-700" style="color:var(--primary);">
                <i class="bi bi-check-circle-fill me-1"></i>Panier
            </span>
            <div style="height:2px;width:32px;background:var(--primary);border-radius:2px;"></div>
            <span class="fw-700" style="color:var(--primary);">
                <i class="bi bi-circle-fill me-1" style="font-size:.45rem;vertical-align:middle;"></i>
                Livraison & Paiement
            </span>
            <div style="height:2px;width:32px;background:#e2e8f0;border-radius:2px;"></div>
            <span class="text-muted fw-600">Confirmation</span>
        </div>
    </div>

    {{-- Erreurs --}}
    @if($errors->any())
        <div class="alert alert-danger d-flex align-items-start gap-2 mb-4 rounded-3">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1 fs-5"></i>
            <div>
                <strong>Veuillez corriger les erreurs :</strong>
                <ul class="mb-0 mt-2 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('payment.process') }}" method="POST" id="checkoutForm">
        @csrf
        <div class="row g-4">

            {{-- ─── COLONNE GAUCHE : Formulaires ─── --}}
            <div class="col-lg-7">

                {{-- LIVRAISON --}}
                <div class="card mb-4" style="border-radius:16px;border:1.5px solid var(--border);">
                    <div class="card-header bg-white py-3 px-4"
                         style="border-bottom:1.5px solid var(--border);border-radius:16px 16px 0 0;">
                        <h5 class="fw-800 mb-0 d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;background:#dcfce7;border-radius:10px;
                                        display:flex;align-items:center;justify-content:center;color:#16a34a;font-size:1rem;">
                                <i class="bi bi-truck"></i>
                            </div>
                            Adresse de livraison
                        </h5>
                    </div>
                    <div class="card-body p-4">

                        <div class="mb-3">
                            <label class="form-label fw-600" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                Adresse complète <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="shipping_address"
                                   class="form-control @error('shipping_address') is-invalid @enderror"
                                   placeholder="12 rue de la Paix"
                                   value="{{ old('shipping_address') }}">
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-7">
                                <label class="form-label fw-600" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                    Ville <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="shipping_city"
                                       class="form-control @error('shipping_city') is-invalid @enderror"
                                       placeholder="Paris"
                                       value="{{ old('shipping_city') }}">
                                @error('shipping_city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-5">
                                <label class="form-label fw-600" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                    Code postal <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="shipping_postal_code"
                                       class="form-control @error('shipping_postal_code') is-invalid @enderror"
                                       placeholder="00000" maxlength="10"
                                       value="{{ old('shipping_postal_code') }}">
                                @error('shipping_postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-600" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                Notes (optionnel)
                            </label>
                            <textarea name="notes" class="form-control" rows="2"
                                      placeholder="Instructions particulières pour la livraison...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- PAIEMENT --}}
                <div class="card" style="border-radius:16px;border:1.5px solid var(--border);">
                    <div class="card-header bg-white py-3 px-4"
                         style="border-bottom:1.5px solid var(--border);border-radius:16px 16px 0 0;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-800 mb-0 d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;background:#dbeafe;border-radius:10px;
                                            display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:1rem;">
                                    <i class="bi bi-shield-lock"></i>
                                </div>
                                Paiement sécurisé
                            </h5>
                            <div class="d-flex gap-1">
                                <span style="background:#dcfce7;color:#166534;font-size:.7rem;font-weight:700;
                                             padding:.25em .65em;border-radius:20px;">
                                    <i class="bi bi-shield-check me-1"></i>SSL
                                </span>
                                <span style="background:#dbeafe;color:#1d4ed8;font-size:.7rem;font-weight:700;
                                             padding:.25em .65em;border-radius:20px;">
                                    Simulation
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">

                        {{-- Bandeau info démo --}}
                        <div class="p-3 rounded-3 mb-4" style="background:#fef9c3;border:1.5px solid #fde68a;">
                            <div class="d-flex gap-2 align-items-start" style="font-size:.82rem;color:#854d0e;">
                                <i class="bi bi-info-circle-fill mt-1 flex-shrink-0"></i>
                                <div>
                                    <strong>Mode démo :</strong> utilisez n'importe quel numéro fictif.
                                    Aucune vraie transaction n'est effectuée.
                                </div>
                            </div>
                        </div>

                        {{-- Nom porteur --}}
                        <div class="mb-3">
                            <label class="form-label fw-600" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                Nom du porteur <span class="text-danger">*</span>
                            </label>
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
                            <label class="form-label fw-600" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                Numéro de carte <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8fafc;border-right:0;">
                                    <i class="bi bi-credit-card text-primary" id="cardIcon"></i>
                                </span>
                                <input type="text" name="card_number" id="cardNumber"
                                       class="form-control @error('card_number') is-invalid @enderror"
                                       placeholder="1234 5678 9012 3456"
                                       maxlength="19"
                                       value="{{ old('card_number') }}"
                                       style="border-left:0;letter-spacing:.08em;">
                                @error('card_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-600" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                    Expiration <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="card_expiry" id="cardExpiry"
                                       class="form-control @error('card_expiry') is-invalid @enderror"
                                       placeholder="MM/AA" maxlength="5"
                                       value="{{ old('card_expiry') }}">
                                @error('card_expiry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-600" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                    CVV <span class="text-danger">*</span>
                                    <i class="bi bi-question-circle text-muted ms-1"
                                       data-bs-toggle="tooltip" title="3 chiffres au dos de votre carte"></i>
                                </label>
                                <input type="password" name="card_cvv"
                                       class="form-control @error('card_cvv') is-invalid @enderror"
                                       placeholder="•••" maxlength="4"
                                       value="{{ old('card_cvv') }}">
                                @error('card_cvv')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3 d-flex align-items-center gap-2"
                             style="font-size:.78rem;color:var(--text-muted);">
                            <i class="bi bi-shield-check" style="color:#16a34a;"></i>
                            Vos données de carte ne sont jamais stockées intégralement.
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── COLONNE DROITE : Récapitulatif ─── --}}
            <div class="col-lg-5">
                <div class="card sticky-top" style="top:80px;border-radius:16px;border:1.5px solid var(--border);">
                    <div class="card-header bg-white py-3 px-4"
                         style="border-bottom:1.5px solid var(--border);border-radius:16px 16px 0 0;">
                        <h5 class="mb-0 fw-800">
                            <i class="bi bi-receipt me-2" style="color:var(--accent)"></i>Votre commande
                        </h5>
                    </div>
                    <div class="card-body p-4">

                        {{-- Articles --}}
                        <div class="d-flex flex-column gap-3 mb-4">
                            @foreach($cart->items as $item)
                            <div class="d-flex align-items-center gap-3">
                                <div class="position-relative flex-shrink-0">
                                    <img src="{{ $item->product->image_url }}"
                                         alt="{{ $item->product->name }}"
                                         class="rounded-3"
                                         style="width:54px;height:54px;object-fit:cover;"
                                         onerror="this.src='https://placehold.co/54x54/e2e8f0/94a3b8?text=?'">
                                    <span style="position:absolute;top:-6px;right:-6px;
                                                 background:var(--dark);color:#fff;border-radius:50%;
                                                 width:20px;height:20px;font-size:.65rem;font-weight:700;
                                                 display:flex;align-items:center;justify-content:center;">
                                        {{ $item->quantity }}
                                    </span>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-600 text-truncate" style="font-size:.88rem;">
                                        {{ Str::limit($item->product->name, 28) }}
                                    </div>
                                    <div class="text-muted" style="font-size:.76rem;">
                                        {{ $item->product->formatted_price }} / unité
                                    </div>
                                </div>
                                <div class="fw-700 flex-shrink-0" style="font-size:.9rem;color:var(--text);">
                                    {{ $item->formatted_subtotal }}
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <hr style="border-color:var(--border);margin:.5rem 0 1rem;">

                        <div class="d-flex justify-content-between mb-2" style="font-size:.88rem;">
                            <span class="text-muted">Sous-total ({{ $cart->total_items }} article(s))</span>
                            <span class="fw-700">{{ $cart->formatted_total }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4" style="font-size:.88rem;">
                            <span class="text-muted">Livraison</span>
                            <span class="fw-700" style="color:#16a34a;">
                                <i class="bi bi-check-circle-fill me-1"></i>Gratuite
                            </span>
                        </div>

                        {{-- Total --}}
                        <div class="p-3 rounded-3 mb-4"
                             style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1.5px solid rgba(37,99,235,.2);">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-800" style="color:var(--dark);">Total à payer</span>
                                <span class="fw-900" style="font-size:1.5rem;color:var(--primary);letter-spacing:-.04em;">
                                    {{ $cart->formatted_total }}
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 fw-800 mb-3" id="payBtn"
                                style="border-radius:12px;font-size:1rem;letter-spacing:.01em;">
                            <i class="bi bi-lock-fill me-2"></i>Payer {{ $cart->formatted_total }}
                        </button>

                        <div class="text-center d-flex align-items-center justify-content-center gap-2"
                             style="font-size:.75rem;color:var(--text-muted);">
                            <i class="bi bi-shield-lock"></i>
                            Paiement chiffré SSL — Données sécurisées
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

@push('scripts')
<script>
    // Formatage numéro carte : espaces tous les 4 chiffres
    document.getElementById('cardNumber').addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g,'').substring(0,16);
        v = v.replace(/(.{4})/g,'$1 ').trim();
        e.target.value = v;
    });

    // Formatage date expiration MM/AA
    document.getElementById('cardExpiry').addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g,'');
        if (v.length >= 2) v = v.substring(0,2) + '/' + v.substring(2,4);
        e.target.value = v;
    });

    // Soumission : nettoyer + désactiver
    document.getElementById('checkoutForm').addEventListener('submit', function() {
        const cardInput = document.getElementById('cardNumber');
        cardInput.value = cardInput.value.replace(/\s+/g,'');
        const payBtn = document.getElementById('payBtn');
        payBtn.disabled = true;
        payBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Traitement en cours...';
    });

    // Tooltips Bootstrap
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
</script>
@endpush
@endsection
