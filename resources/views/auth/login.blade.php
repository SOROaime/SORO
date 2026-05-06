@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="text-center mb-4">
                <i class="bi bi-bag-heart-fill display-4 text-primary"></i>
                <h2 class="fw-bold mt-2">Connexion</h2>
                <p class="text-muted">Accédez à votre compte ShopCI</p>
            </div>

            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-body p-4">

                    <form action="{{ route('login') }}" method="POST" novalidate>
                        @csrf

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="bi bi-envelope me-1"></i>Adresse email
                            </label>
                            <input
                                type="email"
                                class="form-control form-control-lg @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="votre@email.com"
                                autocomplete="email"
                                autofocus
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Mot de passe --}}
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">
                                <i class="bi bi-lock me-1"></i>Mot de passe
                            </label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    class="form-control form-control-lg @error('password') is-invalid @enderror"
                                    id="password"
                                    name="password"
                                    placeholder="Votre mot de passe"
                                    autocomplete="current-password"
                                >
                                <button class="btn btn-outline-secondary" type="button" id="togglePwd">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Se souvenir de moi --}}
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label text-muted small" for="remember">
                                Rester connecté
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
                        </button>
                    </form>
                </div>
            </div>

            <p class="text-center text-muted mt-4">
                Pas encore de compte ?
                <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Créer un compte</a>
            </p>

            {{-- Compte demo --}}
            <div class="alert alert-info small mt-3">
                <i class="bi bi-info-circle me-1"></i>
                <strong>Démo admin :</strong> admin@shopci.com / Admin123!
            </div>
        </div>
    </div>
</div>

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
