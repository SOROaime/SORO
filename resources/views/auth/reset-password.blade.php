@extends('layouts.app')
@section('title', 'Réinitialiser le mot de passe')

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
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <h2 class="fw-800 mb-1" style="font-size:1.5rem;letter-spacing:-.04em;">Nouveau mot de passe</h2>
                        <p class="text-muted" style="font-size:.875rem;">
                            Choisissez un mot de passe sécurisé (8 caractères min., majuscule + chiffre).
                        </p>
                    </div>

                    {{-- Erreurs --}}
                    @if($errors->any())
                        <div class="alert d-flex align-items-center gap-2 mb-4"
                             style="background:var(--danger-l);border:1px solid #fecaca;color:var(--danger);border-radius:12px;">
                            <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <form action="{{ route('password.update') }}" method="POST" novalidate>
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-600" style="font-size:.875rem;">Adresse email</label>
                            <div class="input-icon-group">
                                <i class="bi bi-envelope input-icon"></i>
                                <input type="email" id="email" name="email"
                                       class="form-control form-control-lg ps-icon @error('email') is-invalid @enderror"
                                       value="{{ old('email', request('email')) }}"
                                       placeholder="votre@email.com"
                                       autofocus>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Nouveau mot de passe --}}
                        <div class="mb-3">
                            <label for="password" class="form-label fw-600" style="font-size:.875rem;">Nouveau mot de passe</label>
                            <div class="input-icon-group">
                                <i class="bi bi-lock input-icon"></i>
                                <input type="password" id="password" name="password"
                                       class="form-control form-control-lg ps-icon pe-icon @error('password') is-invalid @enderror"
                                       placeholder="••••••••">
                                <button type="button" class="input-icon-right" onclick="togglePwd('password','eyeNew')" tabindex="-1">
                                    <i class="bi bi-eye" id="eyeNew"></i>
                                </button>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Confirmation --}}
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-600" style="font-size:.875rem;">Confirmer le mot de passe</label>
                            <div class="input-icon-group">
                                <i class="bi bi-lock-fill input-icon"></i>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="form-control form-control-lg ps-icon pe-icon"
                                       placeholder="••••••••">
                                <button type="button" class="input-icon-right" onclick="togglePwd('password_confirmation','eyeConfirm')" tabindex="-1">
                                    <i class="bi bi-eye" id="eyeConfirm"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-700 mb-4">
                            <i class="bi bi-check-lg"></i>Réinitialiser le mot de passe
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
.input-icon-right {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    background: none; border: none; color: var(--text-light);
    cursor: pointer; padding: 4px; z-index: 3;
    font-size: .95rem; line-height: 1;
    display: flex; align-items: center;
}
.input-icon-right:hover { color: var(--primary); }
.ps-icon { padding-left: 2.6rem !important; }
.pe-icon { padding-right: 2.8rem !important; }
footer { margin-top: 0 !important; }
</style>

<script>
function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
@endsection
