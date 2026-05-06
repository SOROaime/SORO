@extends('layouts.app')

@section('title', 'Connexion')

@section('content')

<div class="auth-wrapper">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">

                {{-- Logo & titre --}}
                <div class="text-center mb-4 fade-in-up">
                    <div class="auth-logo mx-auto mb-3">
                        <i class="bi bi-bag-heart-fill"></i>
                    </div>
                    <h2 class="fw-900 mb-1" style="letter-spacing:-.04em;">Connexion</h2>
                    <p class="text-muted" style="font-size:.9rem;">Accédez à votre compte <strong>ShopCI</strong></p>
                </div>

                {{-- Card --}}
                <div class="auth-card card fade-in-up-1">
                    <div class="card-body p-4 p-sm-5">
                        <form action="{{ route('login') }}" method="POST" novalidate>
                            @csrf

                            {{-- Email --}}
                            <div class="mb-4">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-1 text-muted"></i>Adresse email
                                </label>
                                <input type="email"
                                       class="form-control form-control-lg @error('email') is-invalid @enderror"
                                       id="email" name="email"
                                       value="{{ old('email') }}"
                                       placeholder="votre@email.com"
                                       autocomplete="email" autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Mot de passe --}}
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock me-1 text-muted"></i>Mot de passe
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control form-control-lg @error('password') is-invalid @enderror"
                                           id="password" name="password"
                                           placeholder="••••••••"
                                           autocomplete="current-password">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePwd"
                                            style="border-left:0;border-radius:0 10px 10px 0;">
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Se souvenir --}}
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                    <label class="form-check-label text-muted" for="remember" style="font-size:.875rem;">
                                        Rester connecté
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-700">
                                <i class="bi bi-box-arrow-in-right"></i>Se connecter
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Lien inscription --}}
                <p class="text-center text-muted mt-4" style="font-size:.9rem;">
                    Pas encore de compte ?
                    <a href="{{ route('register') }}" class="fw-700 text-decoration-none" style="color:var(--primary);">
                        Créer un compte
                    </a>
                </p>

                {{-- Démo admin --}}
                <div class="demo-card fade-in-up-2">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-info-circle-fill mt-1 flex-shrink-0" style="color:var(--primary);"></i>
                        <div>
                            <div class="fw-700 mb-1" style="font-size:.82rem;color:var(--primary);">Compte démo admin</div>
                            <div style="font-size:.8rem;color:var(--text-muted);line-height:1.6;">
                                Email : <code>admin@shopci.com</code><br>
                                Mot de passe : <code>Admin123!</code>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .auth-wrapper {
        min-height: calc(100vh - 68px - 420px);
        background: linear-gradient(160deg, #f8fafc 60%, #eff6ff 100%);
    }
    .auth-logo {
        width: 64px; height: 64px;
        background: linear-gradient(135deg, var(--primary), var(--primary-d));
        border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem; color: #fff;
        box-shadow: 0 8px 24px rgba(37,99,235,.25);
    }
    .auth-card {
        border-radius: 20px !important;
        box-shadow: 0 4px 32px rgba(0,0,0,.09), 0 1px 3px rgba(0,0,0,.05) !important;
        border: 1px solid var(--border) !important;
    }
    .demo-card {
        background: #eff6ff;
        border: 1px solid rgba(37,99,235,.15);
        border-radius: 12px;
        padding: 1rem 1.1rem;
        margin-top: 1rem;
    }
    .demo-card code {
        background: rgba(37,99,235,.1);
        color: var(--primary);
        padding: .1em .4em;
        border-radius: 4px;
        font-size: .78rem;
        font-weight: 700;
    }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('togglePwd').addEventListener('click', function () {
        const pwd  = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            pwd.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
</script>
@endpush
@endsection
