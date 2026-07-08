@extends('layouts.app')
@section('title', 'Paiement en cours')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5 text-center">

            <div class="waiting-circle mx-auto mb-4">
                <i class="bi bi-credit-card-2-front"></i>
            </div>

            <h1 class="fw-900 mb-2" style="font-size:1.9rem;letter-spacing:-.04em;color:var(--primary);">
                Paiement en cours…
            </h1>
            <p class="text-muted mb-4" style="font-size:1rem;">
                Commande <strong>{{ $order->order_number }}</strong><br>
                Complétez votre paiement sur la page GeniusPay ouverte.
            </p>

            {{-- Bouton vers GeniusPay --}}
            @if($checkoutUrl)
            <a href="{{ $checkoutUrl }}" target="_blank" id="gpBtn"
               class="btn btn-primary px-5 fw-700 mb-4"
               style="border-radius:12px;padding:.85em 2em;font-size:1rem;">
                <i class="bi bi-box-arrow-up-right me-2"></i>Ouvrir GeniusPay
            </a>
            @endif

            {{-- Indicateur de poll --}}
            <div class="mb-3" id="pollStatus">
                <div style="background:#f1f5f9;border-radius:12px;padding:.75em 1.2em;font-size:.85rem;color:#64748b;">
                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                    Vérification automatique du paiement…
                </div>
            </div>

            <div class="alert d-flex align-items-start gap-3 rounded-3 text-start"
                 style="background:#eff6ff;border:1.5px solid var(--primary-l);color:var(--primary);font-size:.85rem;">
                <i class="bi bi-info-circle-fill flex-shrink-0 mt-1"></i>
                <div>
                    Après votre paiement sur GeniusPay, vous serez redirigé automatiquement.
                    <br>Vous pouvez aussi fermer GeniusPay et revenir ici.
                </div>
            </div>

            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary mt-3 fw-600">
                <i class="bi bi-arrow-left me-2"></i>Mes commandes
            </a>

        </div>
    </div>
</div>

@push('scripts')
<script>
    const statusUrl  = "{{ route('payment.status', $order) }}";
    const orderId    = {{ $order->id }};

    // Ouvrir GeniusPay automatiquement
    @if($checkoutUrl)
    window.open("{{ e($checkoutUrl) }}", "_blank");
    @endif

    let pollCount = 0;
    const MAX_POLLS = 100; // 5 minutes max (100 × 3s)

    function checkStatus() {
        pollCount++;

        // Timeout après 5 minutes → rediriger vers mes commandes
        if (pollCount > MAX_POLLS) {
            document.getElementById('pollStatus').innerHTML =
                '<div style="background:#fef9c3;border-radius:12px;padding:.75em 1.2em;font-size:.85rem;color:#92400e;">' +
                '<i class="bi bi-exclamation-triangle-fill me-2"></i>Délai dépassé. Vérifiez vos commandes.</div>';
            setTimeout(() => { window.location.href = "{{ route('orders.index') }}"; }, 3000);
            return;
        }

        fetch(statusUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('pollStatus').innerHTML =
                        '<div style="background:#dcfce7;border-radius:12px;padding:.75em 1.2em;font-size:.85rem;color:#16a34a;">' +
                        '<i class="bi bi-check-circle-fill me-2"></i>Paiement confirmé ! Redirection…</div>';
                    window.location.href = data.redirect;
                } else if (data.status === 'failed') {
                    document.getElementById('pollStatus').innerHTML =
                        '<div style="background:#fee2e2;border-radius:12px;padding:.75em 1.2em;font-size:.85rem;color:#dc2626;">' +
                        '<i class="bi bi-x-circle-fill me-2"></i>Paiement échoué. Redirection…</div>';
                    setTimeout(() => { window.location.href = data.redirect; }, 1500);
                } else {
                    setTimeout(checkStatus, 3000);
                }
            })
            .catch(() => { setTimeout(checkStatus, 3000); });
    }

    setTimeout(checkStatus, 3000);
</script>
@endpush

@push('styles')
<style>
.waiting-circle {
    width: 96px; height: 96px;
    background: linear-gradient(135deg, var(--primary), var(--primary-d));
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 2.6rem;
    box-shadow: 0 12px 40px rgba(37,99,235,.3);
    animation: pulse 1.5s ease-in-out infinite;
}
@keyframes pulse {
    0%, 100% { box-shadow: 0 12px 40px rgba(37,99,235,.3); transform: scale(1); }
    50%       { box-shadow: 0 12px 56px rgba(37,99,235,.55); transform: scale(1.05); }
}
</style>
@endpush
@endsection
