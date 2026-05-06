@extends('layouts.app')

@section('title', 'Inscription')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="text-center mb-4">
                <i class="bi bi-person-plus-fill display-4 text-primary"></i>
                <h2 class="fw-bold mt-2">Créer un compte</h2>
                <p class="text-muted">Rejoignez ShopCI gratuitement</p>
            </div>

            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-body p-4">

                    <form action="{{ route('register') }}" method="POST" novalidate>
                        @csrf

                        {{-- Nom --}}
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">
                                <i class="bi bi-person me-1"></i>Nom complet
                            </label>
                            <input
                                type="text"
                                class="form-control form-control-lg @error('name') is-invalid @enderror"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Jean Dupont"
                                autocomplete="name"
                                autofocus
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

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
                                    autocomplete="new-password"
                                    placeholder="Minimum 8 caractères"
                                >
                                <button class="btn btn-outline-secondary" type="button" id="togglePwd">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">
                                Au moins 8 caractères, avec majuscule et chiffre.
                            </div>
                        </div>

                        {{-- Confirmation --}}
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold">
                                <i class="bi bi-lock-fill me-1"></i>Confirmer le mot de passe
                            </label>
                            <input
                                type="password"
                                class="form-control form-control-lg"
                                id="password_confirmation"
                                name="password_confirmation"
                                autocomplete="new-password"
                                placeholder="Répétez le mot de passe"
                            >
                        </div>

                        {{-- ===================================================
                             SECTION COMPTE ADMIN (facultative)
                             Cocher la case révèle le champ clé secrète.
                             Seul un responsable possédant la clé peut créer
                             un compte administrateur depuis cette page.
                        =================================================== --}}
                        <div class="mb-3">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="isAdmin"
                                    name="is_admin"
                                    value="1"
                                    {{ old('is_admin') ? 'checked' : '' }}
                                >
                                <label class="form-check-label fw-semibold text-secondary" for="isAdmin">
                                    <i class="bi bi-shield-lock me-1"></i>
                                    Je souhaite créer un compte administrateur
                                </label>
                            </div>
                        </div>

                        {{-- Champ clé secrète — masqué par défaut, visible si la case est cochée --}}
                        <div class="mb-4" id="adminKeySection" style="display:none;">
                            <div class="alert alert-warning py-2 small mb-2">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Réservé aux responsables autorisés. Saisissez la clé fournie par l'administrateur principal.
                            </div>
                            <label for="admin_key" class="form-label fw-semibold">
                                <i class="bi bi-key me-1"></i>Clé secrète d'administration
                            </label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    class="form-control form-control-lg @error('admin_key') is-invalid @enderror"
                                    id="admin_key"
                                    name="admin_key"
                                    placeholder="Clé secrète"
                                    autocomplete="off"
                                >
                                <button class="btn btn-outline-secondary" type="button" id="toggleKey">
                                    <i class="bi bi-eye" id="eyeKeyIcon"></i>
                                </button>
                                @error('admin_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                            <i class="bi bi-person-check me-2"></i>Créer mon compte
                        </button>
                    </form>
                </div>
            </div>

            <p class="text-center text-muted mt-4">
                Déjà un compte ?
                <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Se connecter</a>
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toggle affichage mot de passe principal
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

    // Toggle affichage clé secrète
    document.getElementById('toggleKey').addEventListener('click', function () {
        const key  = document.getElementById('admin_key');
        const icon = document.getElementById('eyeKeyIcon');
        if (key.type === 'password') {
            key.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            key.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });

    // Afficher/masquer la section clé secrète selon la case à cocher
    const isAdminCheckbox   = document.getElementById('isAdmin');
    const adminKeySection   = document.getElementById('adminKeySection');
    const adminKeyInput     = document.getElementById('admin_key');

    function toggleAdminSection() {
        if (isAdminCheckbox.checked) {
            adminKeySection.style.display = 'block';
            adminKeyInput.focus();
        } else {
            adminKeySection.style.display = 'none';
            adminKeyInput.value = ''; // Effacer la clé si on décoche
        }
    }

    isAdminCheckbox.addEventListener('change', toggleAdminSection);

    // Afficher la section si une erreur de validation est retournée (old('is_admin'))
    @if(old('is_admin'))
        isAdminCheckbox.checked = true;
        adminKeySection.style.display = 'block';
    @endif
</script>
@endpush
@endsection
