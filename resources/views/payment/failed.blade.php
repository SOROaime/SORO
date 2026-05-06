@extends('layouts.app')

@section('title', 'Paiement échoué')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            {{-- Icône échec animée --}}
            <div class="text-center mb-5">
                <div class="fail-circle mx-auto mb-4">
                    <i class="bi bi-x-lg"></i>
                </div>
                <h1 class="fw-900 mb-2" style="font-size:2rem;color:#dc2626;letter-spacing:-.04em;">
                    Paiement échoué
                </h1>
                <p class="text-muted" style="font-size:1.05rem;">
                    Votre paiement n'a pas pu être traité.<br>
                    <strong>Aucun montant n'a été débité.</strong>
                </p>
            </div>

            {{-- Raison de l'échec --}}
            @if($order->payment && $order->payment->failure_reason)
                <div class="alert d-flex align-items-start gap-3 mb-4 rounded-3"
                     style="background:#fee2e2;border:1.5px solid #fca5a5;color:#991b1b;">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1 fs-5"></i>
                    <div>
                        <strong>Raison :</strong> {{ $order->payment->failure_reason }}
                    </div>
                </div>
            @endif

            {{-- Infos commande --}}
            <div class="card mb-4" style="border-radius:16px;border:1.5px solid var(--border);">
                <div class="card-header bg-white py-3 px-4"
                     style="border-bottom:1.5px solid var(--border);border-radius:16px 16px 0 0;">
                    <h5 class="fw-800 mb-0 d-flex align-items-center gap-2">
                        <div style="width:34px;height:34px;background:#fee2e2;border-radius:10px;
                                    display:flex;align-items:center;justify-content:center;color:#dc2626;">
                            <i class="bi bi-receipt-cutoff"></i>
                        </div>
                        Détails de la commande
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background:var(--light-bg);border:1px solid var(--border);">
                                <div class="text-muted mb-1" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;font-weight:600;">N° Commande</div>
                                <div class="fw-700" style="font-size:.95rem;">{{ $order->order_number }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background:#fee2e2;border:1px solid #fca5a5;">
                                <div class="mb-1" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;font-weight:600;color:#991b1b;">Statut</div>
                                <div class="fw-800" style="color:#dc2626;">
                                    <i class="bi bi-x-circle-fill me-1"></i>Annulée
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Que faire ? --}}
            <div class="card mb-4" style="border-radius:16px;border:1.5px solid rgba(37,99,235,.2);">
                <div class="card-body p-4">
                    <h6 class="fw-800 mb-3 d-flex align-items-center gap-2" style="color:var(--primary);">
                        <i class="bi bi-lightbulb-fill" style="color:#f59e0b;"></i>
                        Que faire maintenant ?
                    </h6>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size:.88rem;">
                        <li class="d-flex gap-2 align-items-start">
                            <i class="bi bi-check2-circle flex-shrink-0 mt-1" style="color:var(--primary);"></i>
                            Vérifiez que vos informations de carte sont correctes
                        </li>
                        <li class="d-flex gap-2 align-items-start">
                            <i class="bi bi-check2-circle flex-shrink-0 mt-1" style="color:var(--primary);"></i>
                            Assurez-vous que votre carte n'est pas expirée
                        </li>
                        <li class="d-flex gap-2 align-items-start">
                            <i class="bi bi-check2-circle flex-shrink-0 mt-1" style="color:var(--primary);"></i>
                            Contactez votre banque si le problème persiste
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('cart.index') }}" class="btn btn-primary btn-lg px-5 fw-800">
                    <i class="bi bi-arrow-repeat me-2"></i>Réessayer le paiement
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg px-4 fw-600">
                    <i class="bi bi-house me-2"></i>Accueil
                </a>
            </div>

        </div>
    </div>
</div>

@push('styles')
<style>
.fail-circle {
    width: 96px; height: 96px;
    background: linear-gradient(135deg, #dc2626, #ef4444);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 2.8rem;
    box-shadow: 0 12px 40px rgba(220,38,38,.35);
    animation: popIn .65s cubic-bezier(.175,.885,.32,1.275) forwards;
}
@keyframes popIn {
    0%   { transform: scale(0); opacity: 0; }
    60%  { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
}
</style>
@endpush
@endsection
