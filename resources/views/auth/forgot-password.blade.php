@extends('layouts.app')
@section('title', 'Mot de passe oublié')

@section('content')

<div class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height:calc(100vh - var(--navbar-h) - 60px);">
            <div class="col-sm-10 col-md-7 col-lg-5 col-xl-4">
                <div class="card p-4 p-md-5 fade-in-up">

                    {{-- Icône --}}
                    <div class="text-center mb-4">
                        <div class="mx-auto mb-3" style="width:56px;height:56px;border-radius:16px;
                             background:linear-gradient(135deg,var(--primary),#6366f1);
                             display:flex;align-items:center;justify-content:center;
                             color:#fff;font-size:1.5rem;box-shadow:var(--shadow-blue);">
                            <i class="bi bi-key-fill"></i>
                        </div>
                        <h2 class="fw-800 mb-1" style="font-size:1.5rem;letter-spacing:-.04em;">Mot de passe oublié ?</h2>
                        <p class="text-muted" style="font-size:.875rem;">
                            Entrez votre email et nous vous enverrons un lien de réinitialisation.
                        </p>
                    </div>

                    {{-- Succès --}}
                    @if(session('success'))
                        <div class="alert d-flex align-items-center gap-2 mb-4"
                             style="background:var(--success-l);border:1px solid #bbf7d0;color:#15803d;border-radius:12px;">
                            <i class="bi bi-envelope-check-fill flex-shrink-0"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    {{-- Erreur --}}
                    @if($errors->any())
                        <div class="alert d-flex align-items-center gap-2 mb-4"
                             style="background:var(--danger-l);border:1px solid #fecaca;color:var(--danger);border-radius:12px;">
                            <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <form action="{{ route('password.email') }}" method="POST" novalidate>
                        @csrf
                        <div class="mb-4">
                            <label for="email" class="form-label fw-600" style="font-size:.875rem;">Adresse email</label>
                            <div class="input-icon-group">
                                <i class="bi bi-envelope input-icon"></i>
                                <input type="email" id="email" name="email"
                                       class="form-control form-control-lg ps-icon @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}"
                                       placeholder="votre@email.com"
                                       autofocus>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-700 mb-4">
                            <i class="bi bi-send"></i>Envoyer le lien
                        </button>
                    </form>

                    <p class="text-center text-muted mb-0" style="font-size:.875rem;">
                        <a href="{{ route('login') }}" class="fw-600 text-decoration-none" style="color:var(--primary);">
                            <i class="bi bi-arrow-left"></i> Retour à la connexion
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.auth-wrapper {
    background: linear-gradient(160deg, #f8fafc 0%, var(--primary-xl) 100%);
    padding: 2rem 0;
}
.input-icon-group { position: relative; }
.input-icon {
    position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
    color: var(--text-light); font-size: .95rem; pointer-events: none; z-index: 3;
}
.ps-icon { padding-left: 2.6rem !important; }
footer { margin-top: 0 !important; }
</style>
@endsection
