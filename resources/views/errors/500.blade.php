@extends('layouts.app')
@section('title', 'Erreur serveur')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6 text-center py-5">

            <div style="font-size:6rem;line-height:1;margin-bottom:1.5rem;
                        background:linear-gradient(135deg,#dc2626,#f97316);
                        -webkit-background-clip:text;-webkit-text-fill-color:transparent;
                        font-weight:900;letter-spacing:-.05em;">
                500
            </div>

            <div style="width:80px;height:4px;background:linear-gradient(90deg,#dc2626,#f97316);
                        border-radius:2px;margin:0 auto 2rem;"></div>

            <h1 class="fw-900 mb-3" style="font-size:1.6rem;letter-spacing:-.04em;">
                Erreur serveur
            </h1>
            <p class="text-muted mb-5" style="font-size:.95rem;line-height:1.8;max-width:420px;margin:0 auto 2rem;">
                Une erreur inattendue s'est produite. Notre équipe a été notifiée et travaille à la résolution.
                Veuillez réessayer dans quelques instants.
            </p>

            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('home') }}" class="btn btn-primary btn-lg fw-700" style="border-radius:13px;">
                    <i class="bi bi-house me-2"></i>Retour à l'accueil
                </a>
                <a href="{{ route('contact') }}" class="btn btn-outline-secondary btn-lg fw-700" style="border-radius:13px;">
                    <i class="bi bi-envelope me-2"></i>Contacter le support
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
