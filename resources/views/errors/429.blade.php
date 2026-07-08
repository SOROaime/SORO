@extends('layouts.app')
@section('title', 'Trop de requêtes')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6 text-center py-5">

            <div style="font-size:6rem;line-height:1;margin-bottom:1.5rem;
                        background:linear-gradient(135deg,var(--primary),#ef4444);
                        -webkit-background-clip:text;-webkit-text-fill-color:transparent;
                        font-weight:900;letter-spacing:-.05em;">
                429
            </div>

            <div style="width:80px;height:4px;background:linear-gradient(90deg,var(--primary),var(--accent));
                        border-radius:2px;margin:0 auto 2rem;"></div>

            <h1 class="fw-900 mb-3" style="font-size:1.6rem;letter-spacing:-.04em;">
                Trop de tentatives
            </h1>
            <p class="text-muted mb-4">
                Vous avez effectué trop de requêtes en peu de temps.<br>
                Veuillez patienter quelques minutes avant de réessayer.
            </p>

            <a href="{{ route('home') }}" class="btn btn-primary px-4 py-2 rounded-pill">
                <i class="bi bi-house-fill me-2"></i>Retour à l'accueil
            </a>
        </div>
    </div>
</div>
@endsection
