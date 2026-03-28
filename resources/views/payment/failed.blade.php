@extends('layouts.app')

@section('title', 'Paiement échoué')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="mb-4">
                <div class="rounded-circle bg-danger d-inline-flex align-items-center justify-content-center"
                     style="width:100px;height:100px;">
                    <i class="bi bi-x-lg text-white" style="font-size:3rem;"></i>
                </div>
            </div>

            <h1 class="fw-bold text-danger mb-2">Paiement échoué</h1>
            <p class="lead text-muted mb-4">
                Votre paiement n'a pas pu être traité.<br>
                Aucun montant n'a été prélevé.
            </p>

            @if($order->payment && $order->payment->failure_reason)
                <div class="alert alert-danger rounded-3 mb-4">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ $order->payment->failure_reason }}
                </div>
            @endif

            <div class="card border-0 rounded-4 shadow-sm mb-4 text-start">
                <div class="card-body p-4">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="text-muted small">N° Commande</div>
                            <div class="fw-bold">{{ $order->order_number }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Statut</div>
                            <span class="badge bg-danger">Annulée</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('cart.index') }}" class="btn btn-primary px-4">
                    <i class="bi bi-arrow-repeat me-2"></i>Réessayer
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-house me-2"></i>Accueil
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
