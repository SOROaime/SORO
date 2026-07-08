@extends('layouts.app')
@section('title', 'Connexion')

@section('content')

<div class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height:calc(100vh - var(--navbar-h) - 60px);">
            <div class="col-md-10 col-lg-9 col-xl-8">
                <div class="auth-grid fade-in-up">

                    {{-- ─── Panneau gauche (branding) ─── --}}
                    <div class="auth-left d-none d-md-flex">
                        <div class="auth-left-inner">
                            <div class="auth-brand mb-auto">
                                <div class="brand-icon-lg mb-3">
                                    <i class="bi bi-bag-heart-fill"></i>
                                </div>
                                <div class="fw-900 mb-1" style="font-size:1.6rem;letter-spacing:-.05em;color:#fff;">
                                    Shop<span style="color:var(--accent);">CI</span>
                                </div>
                                <div style="font-size:.82rem;color:rgba(255,255,255,.5);">
                                    Votre boutique en ligne
                                </div>
                            </div>

                            <div class="auth-features">
                                @foreach([
                                    ['bi-shield-check',     '#4ade80', 'Paiement sécurisé SSL'],
                                    ['bi-truck-front-fill', '#60a5fa', 'Livraison gratuite'],
                                    ['bi-arrow-repeat',     '#fbbf24', 'Retours sous 30 jours'],
                                    ['bi-headset',          '#a78bfa', 'Support 7j/7'],
                                ] as $f)
                                <div class="auth-feature-item">
                                    <i class="bi {{ $f[0] }}" style="color:{{ $f[1] }};font-size:1rem;flex-shrink:0;"></i>
                                    <span>{{ $f[2] }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- ─── Panneau droit (formulaire) ─── --}}
                    <div class="auth-right">
                        <div class="text-center mb-5">
                            <div class="auth-logo-sm d-flex d-md-none mx-auto mb-3">
                                <i class="bi bi-bag-heart-fill"></i>
                            </div>
                            <h2 class="fw-900 mb-1" style="letter-spacing:-.045em;font-size:1.7rem;">
                                Bon retour ! 👋
                            </h2>
                            <p class="text-muted" style="font-size:.9rem;">
                                Connectez-vous à votre compte <strong>ShopCI</strong>
                            </p>
                        </div>

                        <form action="{{ route('login') }}" method="POST" novalidate>
                            @csrf

                            {{-- Erreur globale --}}
                            @if($errors->any())
                                <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
                                    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                                    <span>{{ $errors->first() }}</span>
                                </div>
                            @endif

                            {{-- Email --}}
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    Adresse email
                                </label>
                                <div class="input-icon-group">
                                    <i class="bi bi-envelope input-icon"></i>
                                    <input type="email" id="email" name="email"
                                           class="form-control form-control-lg ps-icon @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}"
                                           placeholder="votre@email.com"
                                           autocomplete="off" autofocus>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Mot de passe --}}
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    Mot de passe
                                </label>
                                <div class="input-icon-group">
                                    <i class="bi bi-lock input-icon"></i>
                                    <input type="password" id="password" name="password"
                                           class="form-control form-control-lg ps-icon pe-icon @error('password') is-invalid @enderror"
                                           placeholder="••••••••"
                                           autocomplete="off">
                                    <button type="button" class="input-icon-right" id="togglePwd"
                                            tabindex="-1">
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Se souvenir + mot de passe oublié --}}
                            <div class="d-flex align-items-center justify-content-between mb-5">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox"
                                           name="remember" id="remember">
                                    <label class="form-check-label text-muted"
                                           for="remember" style="font-size:.875rem;">
                                        Rester connecté
                                    </label>
                                </div>
                                <a href="{{ route('password.request') }}"
                                   class="text-decoration-none" style="font-size:.875rem;color:var(--primary);font-weight:600;">
                                    Mot de passe oublié ?
                                </a>
                            </div>

                            <button type="submit"
                                    class="btn btn-primary btn-lg w-100 fw-700 mb-4">
                                <i class="bi bi-box-arrow-in-right"></i>Se connecter
                            </button>
                        </form>

                        <p class="text-center text-muted mb-4" style="font-size:.9rem;">
                            Pas encore de compte ?
                            <a href="{{ route('register') }}"
                               class="fw-700 text-decoration-none"
                               style="color:var(--primary);">
                                Créer un compte
                            </a>
                        </p>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    footer { margin-top: 0 !important; }

    .auth-wrapper {
        background: linear-gradient(160deg, #f8fafc 0%, var(--primary-xl) 100%);
        padding: 2rem 0;
    }

    .auth-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 8px 48px rgba(0,0,0,.12), 0 2px 8px rgba(0,0,0,.06);
        border: 1px solid var(--border);
        background: #fff;
    }
    @media (max-width: 767px) {
        .auth-grid { grid-template-columns: 1fr; }
    }

    /* Panneau gauche */
    .auth-left {
        background: linear-gradient(145deg, var(--dark) 0%, var(--dark-2) 55%, #1a3060 100%);
        padding: 2.5rem;
        display: flex; flex-direction: column;
        position: relative; overflow: hidden;
    }
    .auth-left::before {
        content: '';
        position: absolute;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(37,99,235,.2) 0%, transparent 70%);
        top: -80px; right: -80px;
        pointer-events: none;
    }
    .auth-left::after {
        content: '';
        position: absolute;
        width: 200px; height: 200px;
        background: radial-gradient(circle, rgba(245,158,11,.12) 0%, transparent 70%);
        bottom: -60px; left: -40px;
        pointer-events: none;
    }
    .auth-left-inner {
        display: flex; flex-direction: column; height: 100%;
        position: relative; z-index: 1;
    }
    .brand-icon-lg {
        width: 52px; height: 52px;
        background: linear-gradient(135deg, var(--accent), #fb923c);
        border-radius: 15px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 1.5rem;
        box-shadow: 0 6px 20px rgba(245,158,11,.35);
    }
    .auth-features {
        display: flex; flex-direction: column; gap: 12px;
        margin-top: auto;
    }
    .auth-feature-item {
        display: flex; align-items: center; gap: 10px;
        font-size: .84rem; font-weight: 500;
        color: rgba(255,255,255,.7);
        padding: .65rem .9rem;
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.1);
        border-radius: 10px;
    }

    /* Panneau droit */
    .auth-right {
        padding: 2.5rem 2.5rem 2rem;
        display: flex; flex-direction: column; justify-content: center;
    }
    @media (max-width: 575px) {
        .auth-right { padding: 1.75rem 1.5rem; }
    }

    /* Logo mobile */
    .auth-logo-sm {
        width: 52px; height: 52px;
        background: linear-gradient(135deg, var(--primary), var(--primary-d));
        border-radius: 16px;
        align-items: center; justify-content: center;
        font-size: 1.5rem; color: #fff;
        box-shadow: 0 6px 20px rgba(37,99,235,.28);
    }

    /* Champs avec icône */
    .input-icon-group { position: relative; }
    .input-icon {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: var(--text-light); font-size: .95rem; pointer-events: none; z-index: 3;
    }
    .input-icon-right {
        position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
        background: none; border: none; color: var(--text-light);
        cursor: pointer; padding: 4px; z-index: 3;
        transition: color .2s;
        font-size: .95rem; line-height: 1;
        display: flex; align-items: center;
    }
    .input-icon-right:hover { color: var(--primary); }
    .ps-icon  { padding-left: 2.6rem !important; }
    .pe-icon  { padding-right: 2.8rem !important; }

</style>
@endpush

@push('scripts')
<script>
    // Empêcher le remplissage automatique du navigateur.
    // On ne réinitialise l'email que s'il ne vient pas d'une erreur de validation.
    const serverEmail = @json(old('email', ''));
    window.addEventListener('load', function () {
        document.getElementById('email').value    = serverEmail;
        document.getElementById('password').value = '';
    });

    document.getElementById('togglePwd').addEventListener('click', function () {
        const pwd  = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            pwd.type = 'password';
            icon.className = 'bi bi-eye';
        }
    });
</script>
@endpush
@endsection
