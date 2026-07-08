@extends('layouts.app')

@section('title', 'Confirmation de paiement — Code OTP')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            {{-- Icône sécurité --}}
            <div class="text-center mb-4">
                <div class="otp-shield mx-auto mb-3">
                    <i class="bi bi-shield-lock-fill" style="font-size:2.8rem;color:#2563eb;"></i>
                </div>
                <h1 class="fw-bold mb-1" style="font-size:1.6rem;">Confirmation de paiement</h1>
                <p class="text-muted mb-0">
                    Un code de confirmation a été envoyé à<br>
                    <strong>{{ $maskedEmail }}</strong>
                </p>
            </div>

            {{-- Alertes --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                </div>
            @endif

            {{-- Formulaire OTP --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    {{-- Minuteur --}}
                    <div class="text-center mb-4">
                        <p class="text-muted small mb-1">Ce code expire dans</p>
                        <div id="countdown" class="fw-bold" style="font-size:1.8rem;color:#2563eb;letter-spacing:.1em;">
                            <span id="minutes">05</span>:<span id="seconds">00</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('payment.otp.confirm') }}" id="otp-form">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-center w-100">
                                Entrez votre code à 6 chiffres
                            </label>

                            {{-- Champs OTP individuels (UX) --}}
                            <div class="d-flex gap-2 justify-content-center mb-2" id="otp-inputs">
                                @for($i = 0; $i < 6; $i++)
                                    <input type="text"
                                           inputmode="numeric"
                                           maxlength="1"
                                           class="otp-digit form-control text-center fw-bold p-2 @error('otp') is-invalid @enderror"
                                           style="width:48px;height:56px;font-size:1.4rem;border-radius:10px;"
                                           autocomplete="one-time-code">
                                @endfor
                            </div>

                            {{-- Champ caché qui reçoit les 6 chiffres combinés --}}
                            <input type="hidden" name="otp" id="otp-hidden" value="{{ old('otp') }}">

                            @error('otp')
                                <div class="text-danger small text-center mt-2">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold" id="btn-verify">
                            <i class="bi bi-shield-check me-2"></i>Confirmer le paiement
                        </button>
                    </form>

                    <hr class="my-3">

                    {{-- Renvoyer le code --}}
                    <div class="text-center">
                        <p class="text-muted small mb-2">Vous n'avez pas reçu le code ?</p>
                        <form method="POST" action="{{ route('payment.otp.resend') }}" id="resend-form">
                            @csrf
                            <button type="submit" class="btn btn-link p-0 text-decoration-none" id="btn-resend" disabled>
                                <i class="bi bi-arrow-clockwise me-1"></i>Renvoyer un nouveau code
                            </button>
                        </form>
                        <p class="text-muted" style="font-size:0.75rem;" id="resend-hint">
                            Disponible dans <span id="resend-countdown">30</span>s
                        </p>
                    </div>
                </div>
            </div>

            {{-- Note sécurité --}}
            <div class="mt-3 p-3 rounded" style="background:#f0f9ff;border:1px solid #bae6fd;">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-info-circle-fill text-info mt-1" style="flex-shrink:0;"></i>
                    <p class="mb-0 small text-muted">
                        Ce code est valide <strong>5 minutes</strong> et à usage unique.
                        Si vous n'avez pas demandé ce paiement, ignorez cet email
                        et <a href="mailto:support@shopci.ci">contactez le support</a>.
                    </p>
                </div>
            </div>

            {{-- Lien retour --}}
            <div class="text-center mt-3">
                <a href="{{ route('orders.checkout') }}" class="text-muted small">
                    <i class="bi bi-arrow-left me-1"></i>Annuler et retourner au panier
                </a>
            </div>

        </div>
    </div>
</div>

<style>
.otp-digit:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,.15);
}
.otp-digit.filled {
    background-color: #eff6ff;
    border-color: #2563eb;
}
#countdown.expired { color: #dc2626; }
</style>

@push('scripts')
<script>
// ── Gestion des champs OTP ────────────────────────────────────────────
const digits  = document.querySelectorAll('.otp-digit');
const hidden  = document.getElementById('otp-hidden');

digits.forEach((input, idx) => {
    input.addEventListener('input', () => {
        input.value = input.value.replace(/\D/g, '').slice(-1);
        if (input.value) {
            input.classList.add('filled');
            if (idx < digits.length - 1) digits[idx + 1].focus();
        } else {
            input.classList.remove('filled');
        }
        syncHidden();
    });

    input.addEventListener('keydown', e => {
        if (e.key === 'Backspace' && !input.value && idx > 0) {
            digits[idx - 1].focus();
            digits[idx - 1].value = '';
            digits[idx - 1].classList.remove('filled');
            syncHidden();
        }
    });

    input.addEventListener('paste', e => {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
        [...paste].slice(0, 6).forEach((ch, i) => {
            if (digits[i]) {
                digits[i].value = ch;
                digits[i].classList.add('filled');
            }
        });
        syncHidden();
        const next = Math.min(paste.length, 5);
        digits[next].focus();
    });
});

function syncHidden() {
    hidden.value = [...digits].map(d => d.value).join('');
}

// Pré-remplir si old value
if (hidden.value) {
    [...hidden.value].forEach((ch, i) => {
        if (digits[i]) { digits[i].value = ch; digits[i].classList.add('filled'); }
    });
}

// Focus sur le premier champ
digits[0]?.focus();

// ── Minuteur ──────────────────────────────────────────────────────────
let secondsLeft = {{ $secondsLeft }};
const countdownEl = document.getElementById('countdown');
const minsEl      = document.getElementById('minutes');
const secsEl      = document.getElementById('seconds');
const btnVerify   = document.getElementById('btn-verify');

function updateCountdown() {
    if (secondsLeft <= 0) {
        minsEl.textContent = '00';
        secsEl.textContent = '00';
        countdownEl.classList.add('expired');
        btnVerify.disabled = true;
        btnVerify.innerHTML = '<i class="bi bi-clock me-2"></i>Code expiré — Renvoyez un nouveau code';
        return;
    }
    const m = Math.floor(secondsLeft / 60);
    const s = secondsLeft % 60;
    minsEl.textContent = String(m).padStart(2, '0');
    secsEl.textContent = String(s).padStart(2, '0');
    secondsLeft--;
    setTimeout(updateCountdown, 1000);
}
updateCountdown();

// ── Bouton Renvoyer (dispo après 30s) ─────────────────────────────────
let resendDelay = 30;
const btnResend   = document.getElementById('btn-resend');
const resendHint  = document.getElementById('resend-hint');
const resendCount = document.getElementById('resend-countdown');

function updateResend() {
    if (resendDelay <= 0) {
        btnResend.disabled = false;
        resendHint.style.display = 'none';
        return;
    }
    resendCount.textContent = resendDelay--;
    setTimeout(updateResend, 1000);
}
updateResend();
</script>
@endpush

@endsection
