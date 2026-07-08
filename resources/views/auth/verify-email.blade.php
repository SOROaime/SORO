@extends('layouts.app')
@section('title', 'Vérifier votre email')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card text-center" style="border-radius:20px;border:1.5px solid var(--border);box-shadow:var(--shadow-md);">
                <div class="card-body p-5">

                    <div style="width:72px;height:72px;border-radius:20px;
                                background:linear-gradient(135deg,#eff6ff,#dbeafe);
                                border:2px solid #bfdbfe;
                                display:flex;align-items:center;justify-content:center;
                                margin:0 auto 1.5rem;font-size:2rem;color:var(--primary);">
                        <i class="bi bi-envelope-check"></i>
                    </div>

                    <h2 class="fw-900 mb-2" style="font-size:1.4rem;letter-spacing:-.04em;">
                        Vérifiez votre email
                    </h2>

                    @if(session('success'))
                        <div class="alert mb-3 py-2"
                             style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;border-radius:10px;font-size:.85rem;">
                            <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                        </div>
                    @endif

                    <p class="text-muted mb-4" style="font-size:.9rem;line-height:1.7;">
                        Merci pour votre inscription ! Avant de continuer, veuillez vérifier votre email en cliquant sur le lien que nous venons de vous envoyer.
                    </p>

                    <p class="text-muted mb-4" style="font-size:.85rem;">
                        Si vous n'avez pas reçu l'email, vérifiez vos spams ou cliquez ci-dessous pour en recevoir un nouveau.
                    </p>

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary fw-700 w-100 mb-3" style="border-radius:12px;">
                            <i class="bi bi-send me-2"></i>Renvoyer l'email de vérification
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm fw-600 w-100" style="border-radius:10px;">
                            <i class="bi bi-box-arrow-right me-1"></i>Se déconnecter
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
