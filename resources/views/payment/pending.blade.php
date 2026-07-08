@extends('layouts.app')
@section('title', 'Paiement en cours de vérification')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5 text-center">

            <div class="pending-circle mx-auto mb-4">
                <i class="bi bi-hourglass-split"></i>
            </div>

            <h1 class="fw-900 mb-2" style="font-size:1.9rem;letter-spacing:-.04em;color:var(--primary);">
                Vérification en cours…
            </h1>
            <p class="text-muted mb-5" style="font-size:1rem;">
                Votre paiement est en cours de traitement.<br>
                Commande <strong>{{ $order->order_number }}</strong>.
            </p>

            <div class="alert d-flex align-items-start gap-3 mb-4 rounded-3 text-start"
                 style="background:#eff6ff;border:1.5px solid var(--primary-l);color:var(--primary);">
                <i class="bi bi-info-circle-fill flex-shrink-0 mt-1 fs-5"></i>
                <div style="font-size:.88rem;">
                    Vous recevrez une confirmation dès que le paiement sera validé.
                    Vérifiez vos commandes dans quelques instants.
                </div>
            </div>

            <div class="mb-4">
                <div style="background:#f1f5f9;border-radius:12px;padding:.75em 1.2em;font-size:.82rem;color:#64748b;">
                    <i class="bi bi-arrow-repeat me-1"></i>
                    Vérification automatique dans <span id="countdown" class="fw-700">10</span>s…
                </div>
            </div>

            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('orders.show', $order) }}"
                   class="btn btn-primary px-5 fw-700" id="checkBtn">
                    <i class="bi bi-eye me-2"></i>Vérifier maintenant
                </a>
                <a href="{{ route('products.index') }}"
                   class="btn btn-outline-secondary px-4 fw-600">
                    <i class="bi bi-grid me-2"></i>Continuer mes achats
                </a>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    let seconds = 10;
    const el = document.getElementById('countdown');
    const target = "{{ route('orders.show', $order) }}";
    const timer = setInterval(() => {
        seconds--;
        el.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(timer);
            window.location.href = target;
        }
    }, 1000);
</script>
@endpush

@push('styles')
<style>
.pending-circle {
    width: 96px; height: 96px;
    background: linear-gradient(135deg, var(--primary), var(--primary-d));
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 2.6rem;
    box-shadow: 0 12px 40px rgba(37,99,235,.3);
    animation: pulse 1.8s ease-in-out infinite;
}
@keyframes pulse {
    0%, 100% { box-shadow: 0 12px 40px rgba(37,99,235,.3); }
    50%       { box-shadow: 0 12px 56px rgba(37,99,235,.55); }
}
</style>
@endpush
@endsection
