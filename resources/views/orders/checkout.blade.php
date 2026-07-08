@extends('layouts.app')
@section('title', 'Finaliser la commande')

@section('content')
<div class="container py-5">

    {{-- En-tête + étapes --}}
    <div class="mb-5">
        <div class="d-flex align-items-center gap-3 mb-3">
            <a href="{{ route('cart.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1 class="section-title mb-0">
                <i class="bi bi-credit-card me-2" style="color:var(--accent)"></i>Finaliser la commande
            </h1>
        </div>

        <div class="d-flex align-items-center gap-2 ms-5 ps-3" style="font-size:.82rem;">
            <span class="fw-700" style="color:var(--primary);">
                <i class="bi bi-check-circle-fill me-1"></i>Panier
            </span>
            <div style="height:2px;width:32px;background:var(--primary);border-radius:2px;"></div>
            <span class="fw-700" style="color:var(--primary);">
                <i class="bi bi-circle-fill me-1" style="font-size:.45rem;vertical-align:middle;"></i>
                Livraison
            </span>
            <div style="height:2px;width:32px;background:#e2e8f0;border-radius:2px;"></div>
            <span class="text-muted fw-600">Paiement GeniusPay</span>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger d-flex align-items-start gap-2 mb-4 rounded-3">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1 fs-5"></i>
            <div>
                <strong>Veuillez corriger les erreurs :</strong>
                <ul class="mb-0 mt-2 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('payment.process') }}" method="POST" id="checkoutForm">
        @csrf
        <div class="row g-4">

            {{-- ─── COLONNE GAUCHE : Livraison ─── --}}
            <div class="col-lg-7">
                <div class="card mb-4" style="border-radius:16px;border:1.5px solid var(--border);">
                    <div class="card-header bg-white py-3 px-4"
                         style="border-bottom:1.5px solid var(--border);border-radius:16px 16px 0 0;">
                        <h5 class="fw-800 mb-0 d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;background:#dcfce7;border-radius:10px;
                                        display:flex;align-items:center;justify-content:center;color:#16a34a;">
                                <i class="bi bi-truck"></i>
                            </div>
                            Adresse de livraison
                        </h5>
                    </div>
                    <div class="card-body p-4">

                        <div class="mb-3">
                            <label class="form-label fw-600"
                                   style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                Adresse complète <span class="text-muted fw-400" style="font-size:.75rem;">(optionnel)</span>
                            </label>
                            <input type="text" name="shipping_address"
                                   class="form-control @error('shipping_address') is-invalid @enderror"
                                   placeholder="12 rue de la Paix, Plateau"
                                   value="{{ old('shipping_address') }}">
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Téléphone de livraison --}}
                        <div class="mb-3">
                            <label class="form-label fw-600"
                                   style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                Téléphone de livraison <span class="text-danger">*</span>
                            </label>
                            <input type="tel" name="shipping_phone"
                                   class="form-control @error('shipping_phone') is-invalid @enderror"
                                   placeholder="07 XX XX XX XX"
                                   value="{{ old('shipping_phone', auth()->user()->phone) }}">
                            @error('shipping_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text" style="font-size:.75rem;">
                                <i class="bi bi-info-circle me-1"></i>
                                Le livreur utilisera ce numéro pour vous contacter.
                            </div>
                        </div>

                        {{-- Ville --}}
                        <div class="mb-3">
                            <label class="form-label fw-600"
                                   style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                Ville <span class="text-danger">*</span>
                            </label>
                            <select name="shipping_city" id="sel_city"
                                    class="form-select @error('shipping_city') is-invalid @enderror">
                                <option value="">-- Choisir une ville --</option>
                            </select>
                            @error('shipping_city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Commune --}}
                        <div class="mb-3">
                            <label class="form-label fw-600"
                                   style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                Commune <span class="text-danger">*</span>
                            </label>
                            <select name="shipping_commune" id="sel_commune"
                                    class="form-select @error('shipping_commune') is-invalid @enderror"
                                    disabled>
                                <option value="">-- Choisir une commune --</option>
                            </select>
                            @error('shipping_commune')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Quartier --}}
                        <div class="mb-3">
                            <label class="form-label fw-600"
                                   style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                Quartier <span class="text-danger">*</span>
                            </label>
                            <select name="shipping_quartier" id="sel_quartier"
                                    class="form-select @error('shipping_quartier') is-invalid @enderror"
                                    disabled>
                                <option value="">-- Choisir un quartier --</option>
                            </select>
                            @error('shipping_quartier')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-600"
                                   style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                Notes (optionnel)
                            </label>
                            <textarea name="notes" class="form-control" rows="2"
                                      placeholder="Instructions particulières pour la livraison...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- ─── Paiement par tranches ─── --}}
                <div class="card mb-4" style="border-radius:16px;border:1.5px solid var(--border);">
                    <div class="card-header bg-white py-3 px-4"
                         style="border-bottom:1.5px solid var(--border);border-radius:16px 16px 0 0;">
                        <h5 class="fw-800 mb-0 d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;background:#fef9c3;border-radius:10px;
                                        display:flex;align-items:center;justify-content:center;color:#b45309;">
                                <i class="bi bi-calendar-week"></i>
                            </div>
                            Paiement par tranches
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted mb-3" style="font-size:.85rem;">
                            Choisissez de payer en une ou plusieurs fois. Les tranches suivantes sont réglées à domicile selon les échéances.
                        </p>

                        <input type="hidden" name="installment_count" id="installment_count_input" value="{{ old('installment_count', 1) }}">

                        <div class="d-flex gap-2 flex-wrap mb-4" id="installmentBtns">
                            @foreach([1 => 'Payer en 1 fois', 2 => 'Payer en 2 fois', 3 => 'Payer en 3 fois', 4 => 'Payer en 4 fois'] as $n => $label)
                            <button type="button"
                                    class="installment-btn btn {{ old('installment_count', 1) == $n ? 'btn-primary' : 'btn-outline-secondary' }} fw-700"
                                    data-n="{{ $n }}"
                                    style="border-radius:10px;font-size:.82rem;">
                                {{ $label }}
                            </button>
                            @endforeach
                        </div>

                        <div id="installmentDetail"></div>
                    </div>
                </div>

                {{-- Choix du mode de paiement --}}
                <div class="card" style="border-radius:16px;border:1.5px solid var(--border);">
                    <div class="card-header bg-white py-3 px-4"
                         style="border-bottom:1.5px solid var(--border);border-radius:16px 16px 0 0;">
                        <h5 class="fw-800 mb-0 d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;background:#dbeafe;border-radius:10px;
                                        display:flex;align-items:center;justify-content:center;color:var(--primary);">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            Mode de paiement
                        </h5>
                    </div>
                    <div class="card-body p-0">

                        {{-- Option 1 : GeniusPay --}}
                        <label class="payment-option" id="opt-geniuspay">
                            <input type="radio" name="payment_method" value="geniuspay"
                                   id="pm_geniuspay" {{ old('payment_method', 'geniuspay') === 'geniuspay' ? 'checked' : '' }}>
                            <div class="payment-option-inner">
                                <div class="payment-option-icon" style="background:#dbeafe;color:var(--primary);">
                                    <i class="bi bi-phone-fill"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-700" style="font-size:.92rem;">Paiement en ligne</div>
                                    <div style="font-size:.76rem;color:var(--text-muted);">
                                        Via GeniusPay — Wave, Orange Money, MTN, Carte bancaire
                                    </div>
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        @foreach(['Wave' => '#1a56db', 'Orange' => '#f97316', 'MTN' => '#eab308', 'Carte' => '#6366f1'] as $name => $color)
                                        <span style="font-size:.68rem;font-weight:700;color:{{ $color }};
                                                     background:#f8fafc;border:1px solid #e2e8f0;
                                                     border-radius:6px;padding:.15rem .5rem;">
                                            {{ $name }}
                                        </span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="payment-option-check"><i class="bi bi-check-lg"></i></div>
                            </div>
                        </label>

                        <div style="height:1px;background:var(--border-2);margin:0 1.2rem;"></div>

                        {{-- Option 2 : Paiement à la livraison --}}
                        <label class="payment-option" id="opt-cod">
                            <input type="radio" name="payment_method" value="cash_on_delivery"
                                   id="pm_cod" {{ old('payment_method') === 'cash_on_delivery' ? 'checked' : '' }}>
                            <div class="payment-option-inner">
                                <div class="payment-option-icon" style="background:#dcfce7;color:#16a34a;">
                                    <i class="bi bi-cash-stack"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-700" style="font-size:.92rem;">Paiement à la livraison</div>
                                    <div style="font-size:.76rem;color:var(--text-muted);">
                                        Payez en espèces à la réception de votre commande
                                    </div>
                                </div>
                                <div class="payment-option-check"><i class="bi bi-check-lg"></i></div>
                            </div>
                        </label>

                    </div>
                </div>
            </div>

            {{-- ─── COLONNE DROITE : Récapitulatif ─── --}}
            <div class="col-lg-5">
                <div class="card sticky-top" style="top:80px;border-radius:16px;border:1.5px solid var(--border);">
                    <div class="card-header bg-white py-3 px-4"
                         style="border-bottom:1.5px solid var(--border);border-radius:16px 16px 0 0;">
                        <h5 class="mb-0 fw-800">
                            <i class="bi bi-receipt me-2" style="color:var(--accent)"></i>Votre commande
                        </h5>
                    </div>
                    <div class="card-body p-4">

                        <div class="d-flex flex-column gap-3 mb-4">
                            @foreach($cart->items as $item)
                            <div class="d-flex align-items-center gap-3">
                                <div class="position-relative flex-shrink-0">
                                    <img src="{{ $item->product->image_url }}"
                                         alt="{{ $item->product->name }}"
                                         class="rounded-3"
                                         style="width:54px;height:54px;object-fit:cover;"
                                         onerror="this.src='https://placehold.co/54x54/e2e8f0/94a3b8?text=?'">
                                    <span style="position:absolute;top:-6px;right:-6px;
                                                 background:var(--dark);color:#fff;border-radius:50%;
                                                 width:20px;height:20px;font-size:.65rem;font-weight:700;
                                                 display:flex;align-items:center;justify-content:center;">
                                        {{ $item->quantity }}
                                    </span>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-600 text-truncate" style="font-size:.88rem;">
                                        {{ Str::limit($item->product->name, 28) }}
                                    </div>
                                    <div class="text-muted" style="font-size:.76rem;">
                                        {{ $item->product->formatted_price }} / unité
                                    </div>
                                </div>
                                <div class="fw-700 flex-shrink-0" style="font-size:.9rem;">
                                    {{ $item->formatted_subtotal }}
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <hr style="border-color:var(--border);margin:.5rem 0 1rem;">

                        <div class="d-flex justify-content-between mb-2" style="font-size:.88rem;">
                            <span class="text-muted">Sous-total ({{ $cart->total_items }} article(s))</span>
                            <span class="fw-700">{{ $cart->formatted_total }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4" style="font-size:.88rem;">
                            <span class="text-muted">Livraison</span>
                            <span class="fw-700" style="color:#16a34a;">
                                <i class="bi bi-check-circle-fill me-1"></i>Gratuite
                            </span>
                        </div>

                        {{-- Total commande --}}
                        <div class="d-flex justify-content-between mb-2" style="font-size:.88rem;">
                            <span class="text-muted">Total commande</span>
                            <span class="fw-700">{{ $cart->formatted_total }}</span>
                        </div>

                        {{-- ── Code promo ── --}}
                        <div class="mb-3">
                            <div class="d-flex gap-2">
                                <input type="text" id="couponInput"
                                       class="form-control form-control-sm"
                                       placeholder="Code promo (ex: BIENVENUE10)"
                                       style="border-radius:10px;font-size:.82rem;text-transform:uppercase;"
                                       maxlength="50">
                                <button type="button" id="couponBtn"
                                        class="btn btn-outline-primary btn-sm fw-700"
                                        style="border-radius:10px;white-space:nowrap;font-size:.82rem;">
                                    Appliquer
                                </button>
                            </div>
                            <div id="couponMsg" class="mt-2" style="font-size:.78rem;"></div>
                            <input type="hidden" name="coupon_code" id="couponCodeHidden">
                        </div>

                        {{-- Ligne remise (masquée par défaut) --}}
                        <div id="discountRow" class="d-flex justify-content-between mb-2" style="font-size:.88rem;display:none!important;">
                            <span class="text-success fw-600"><i class="bi bi-tag-fill me-1"></i>Remise</span>
                            <span id="discountAmount" class="fw-700 text-success">-0 FCFA</span>
                        </div>

                        {{-- Montant dû maintenant (mis à jour par JS) --}}
                        <div id="dueNowBlock" class="p-3 rounded-3 mb-4"
                             style="background:linear-gradient(135deg,#eff6ff,#dbeafe);
                                    border:1.5px solid rgba(37,99,235,.2);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-800" style="color:var(--dark);">À payer maintenant</div>
                                    <div id="dueNowLabel" style="font-size:.72rem;color:#64748b;"></div>
                                </div>
                                <span id="dueNowAmount" class="fw-900"
                                      style="font-size:1.5rem;color:var(--primary);letter-spacing:-.04em;">
                                    {{ $cart->formatted_total }}
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-800 mb-3"
                                id="payBtn" style="border-radius:12px;">
                            <i class="bi bi-box-arrow-up-right me-2" id="payBtnIcon"></i>
                            <span id="payBtnText">Payer {{ $cart->formatted_total }} via GeniusPay</span>
                        </button>

                        <div class="text-center" style="font-size:.72rem;color:var(--text-muted);">
                            <i class="bi bi-shield-lock me-1"></i>
                            <span id="payBtnNote">Paiement sécurisé — Powered by GeniusPay</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

@push('styles')
<style>
    .payment-option {
        display: block;
        cursor: pointer;
        margin: 0;
        padding: 0;
    }
    .payment-option input[type="radio"] { display: none; }
    .payment-option-inner {
        display: flex;
        align-items: center;
        gap: .9rem;
        padding: 1.1rem 1.3rem;
        transition: background .15s;
    }
    .payment-option:hover .payment-option-inner {
        background: var(--primary-xl);
    }
    .payment-option input:checked ~ .payment-option-inner {
        background: var(--primary-xl);
    }
    .payment-option-icon {
        width: 42px; height: 42px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .payment-option-check {
        width: 24px; height: 24px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: flex; align-items: center; justify-content: center;
        font-size: .75rem;
        color: #fff;
        flex-shrink: 0;
        transition: all .18s;
    }
    .payment-option input:checked ~ .payment-option-inner .payment-option-check {
        background: var(--primary);
        border-color: var(--primary);
    }
</style>
@endpush

@push('scripts')
<script>
// ── Données géographiques Côte d'Ivoire ──────────────────────────────────────
const CI_DATA = {
    "Abidjan": {
        "Abobo": ["Abobo-Baoulé","Abobo-Gare","Abobo-Est","Abobo-Ouest","Avocatier","Derrière Rails","Dok","Sagbé","N'Dotré","Sogefiha"],
        "Adjamé": ["Adjamé-Liberté","Adjamé-220 Logements","Agban-Gare","Bracodi","Carrefour","Fraternité","Renault","Williamsville"],
        "Attécoubé": ["Attécoubé","Niangon-Nord","Niangon-Sud","Boribana","Faya","Sable","Santé","Gesco"],
        "Cocody": ["Cocody-Riviera","Riviera 1","Riviera 2","Riviera 3","Riviera 4","Riviera Palmeraie","Angré","Bonoumin","II Plateaux","Blockhauss","Danga","Ambassades"],
        "Koumassi": ["Koumassi-Centre","Koumassi-Est","Koumassi-Remblai","Grand-Campement","Résidence","Zone Industrielle"],
        "Marcory": ["Marcory-Zone 4","Zone 4","Anoumabo","Biétry","Cité des Arts","Résidentiel","Kouté"],
        "Plateau": ["Plateau-Centre","Plateau-Dokui","Commerce","République","Indénié","Cathédrale","Cité Administrative"],
        "Port-Bouët": ["Port-Bouët-Centre","Vridi","Gonzagueville","Adjouffou","Aéroport","Canal","Zone Industrielle Vridi"],
        "Treichville": ["Treichville-Centre","Washington","Anoumabo","Quartier France","Zone 1","Zone 2","Zone 3"],
        "Yopougon": ["Yopougon-Attié","Yopougon-Selmer","Yopougon-Kénedougou","Yopougon-Nord","Yopougon-M'Pouto","Yopougon-Niangon","Wassakara","Toits Rouges","Sicogi","Millionnaire"],
        "Bingerville": ["Bingerville-Centre","Akandjé","Blockauss","M'Badon","Moossou"],
        "Anyama": ["Anyama-Gare","Anyama-Adjamé","Ahoué","Libresso"],
        "Songon": ["Songon-Agban","Songon-Dagbe","Songon-Kassemble"],
        "Grand-Bassam": ["Grand-Bassam-Centre","Koquin","Moossou","Quartier France","Impérial"],
        "Jacqueville": ["Jacqueville-Centre","Adjouan","Azuretti","Gonzagueville"],
        "Dabou": ["Dabou-Centre","Amangui","Grand-Lahou-Ville"]
    },
    "Bouaké": {
        "Bouaké-Sud": ["Zone Industrielle","N'Gattakro","Dar-es-Salam","Commerce","Ancien Quartier"],
        "Bouaké-Nord-Est": ["Belleville","Nimbo","Éléphant","Sokoura","Broukro"],
        "Bouaké-Nord-Ouest": ["Air France","Éléphant 2","Dar-es-Salam Ext.","Broukro 2"],
        "Katiola": ["Katiola-Centre","Tortiya","Dabakala","Niakaramandougou"]
    },
    "Yamoussoukro": {
        "Yamoussoukro-Centre": ["Habitat","Millionnaire","Zone Résidentielle","Cité PDCI","Dioulakro"],
        "Attiégouakro": ["Attiégouakro-Village","Zone Extension"],
        "Kossou": ["Kossou-Village","Barrage"]
    },
    "San-Pédro": {
        "San-Pédro-Centre": ["Bardot","Bardo","Cité Plage","Zone Industrielle","Commerce"],
        "Sassandra": ["Sassandra-Centre","Gbanda","Port"],
        "Soubré": ["Soubré-Centre","San-Rémy","Gnagbodougnoa"]
    },
    "Korhogo": {
        "Korhogo-Centre": ["Koko","Plateau","Zone Résidentielle","Nanguin","Commerce"],
        "Dikodougou": ["Dikodougou-Centre","Karakoro"],
        "Sinématiali": ["Sinématiali-Centre","Koutouba"]
    },
    "Daloa": {
        "Daloa-Centre": ["Tazibouo","Orly","Commerce","Résidentiel","Lobia"],
        "Issia": ["Issia-Centre","Guessabo"],
        "Vavoua": ["Vavoua-Centre","Bogouiné"]
    },
    "Man": {
        "Man-Centre": ["Man-Centre","Diacohou","Commerce","Résidentiel","Zouroungueu"],
        "Biankouma": ["Biankouma-Centre","Ouaninou"],
        "Danané": ["Danané-Centre","Logoualé"]
    },
    "Abengourou": {
        "Abengourou-Centre": ["Commerce","Résidentiel","Habitat","Assikasso","Niabally"],
        "Agnibilékrou": ["Agnibilékrou-Centre","Zaranou"],
        "Bettié": ["Bettié-Centre"]
    },
    "Divo": {
        "Divo-Centre": ["Commerce","Résidentiel","Guitry","Lakota-Ville"],
        "Guitry": ["Guitry-Centre","Gnagbodougnoa"],
        "Lakota": ["Lakota-Centre","Divo-Nord"]
    },
    "Gagnoa": {
        "Gagnoa-Centre": ["Commerce","Résidentiel","Ouragahio","Bayota","Zone Nord"],
        "Ouragahio": ["Ouragahio-Centre"],
        "Oumé": ["Oumé-Centre","Guibéroua"]
    },
    "Dimbokro": {
        "Dimbokro-Centre": ["Commerce","Résidentiel","Kouassi-Kouassikro","Bocanda"],
        "Bocanda": ["Bocanda-Centre"],
        "Bongouanou": ["Bongouanou-Centre","Arrah","M'Batto"]
    },
    "Bondoukou": {
        "Bondoukou-Centre": ["Commerce","Résidentiel","Zanzan","Koulotana"],
        "Tanda": ["Tanda-Centre","Nassian"],
        "Bouna": ["Bouna-Centre","Doropo"]
    },
    "Odienné": {
        "Odienné-Centre": ["Commerce","Résidentiel","Dioulabougou","Seguelon"],
        "Gbéléban": ["Gbéléban-Centre"],
        "Samatiguila": ["Samatiguila-Centre"]
    },
    "Touba": {
        "Touba-Centre": ["Commerce","Résidentiel","Koro","Booko"],
        "Koro": ["Koro-Centre"],
        "Guiglo": ["Guiglo-Centre","Taï","Blolequin"]
    },
    "Agboville": {
        "Agboville-Centre": ["Commerce","Résidentiel","Thiassalé","Azaguié"],
        "Azaguié": ["Azaguié-Centre","Toupah"],
        "Thiassalé": ["Thiassalé-Centre","N'Douci"]
    }
};

// ── Initialisation des selects ────────────────────────────────────────────────
const selCity     = document.getElementById('sel_city');
const selCommune  = document.getElementById('sel_commune');
const selQuartier = document.getElementById('sel_quartier');

const oldCity     = @json(old('shipping_city', ''));
const oldCommune  = @json(old('shipping_commune', ''));
const oldQuartier = @json(old('shipping_quartier', ''));

// Remplir les villes
Object.keys(CI_DATA).sort().forEach(city => {
    const opt = new Option(city, city, false, city === oldCity);
    selCity.add(opt);
});

function fillCommunes(city, selectCommune = '') {
    selCommune.innerHTML = '<option value="">-- Choisir une commune --</option>';
    selQuartier.innerHTML = '<option value="">-- Choisir un quartier --</option>';
    selCommune.disabled = true;
    selQuartier.disabled = true;

    if (!city || !CI_DATA[city]) return;

    Object.keys(CI_DATA[city]).sort().forEach(commune => {
        const opt = new Option(commune, commune, false, commune === selectCommune);
        selCommune.add(opt);
    });
    selCommune.disabled = false;

    if (selectCommune && CI_DATA[city][selectCommune]) {
        fillQuartiers(city, selectCommune, oldQuartier);
    }
}

function fillQuartiers(city, commune, selectQuartier = '') {
    selQuartier.innerHTML = '<option value="">-- Choisir un quartier --</option>';
    selQuartier.disabled = true;

    if (!city || !commune || !CI_DATA[city] || !CI_DATA[city][commune]) return;

    CI_DATA[city][commune].forEach(q => {
        const opt = new Option(q, q, false, q === selectQuartier);
        selQuartier.add(opt);
    });
    selQuartier.disabled = false;
}

selCity.addEventListener('change', () => fillCommunes(selCity.value));
selCommune.addEventListener('change', () => fillQuartiers(selCity.value, selCommune.value));

// Restaurer old() après validation Laravel
if (oldCity) fillCommunes(oldCity, oldCommune);

// ── Paiement par tranches ────────────────────────────────────────────────────
const totalAmount       = {{ $cart->total_amount }};
const installmentInput  = document.getElementById('installment_count_input');
const installmentBtns   = document.querySelectorAll('.installment-btn');
const installmentDetail = document.getElementById('installmentDetail');

function formatFCFA(n) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(n)) + ' FCFA';
}

function renderInstallments(n) {
    installmentInput.value = n;
    installmentBtns.forEach(b => {
        const active = parseInt(b.dataset.n) === n;
        b.className = 'installment-btn btn fw-700' + (active ? ' btn-primary' : ' btn-outline-secondary');
    });

    if (n === 1) {
        installmentDetail.innerHTML = '';
        return;
    }

    const amount = totalAmount / n;
    const colors = ['#1d4ed8','#16a34a','#b45309','#7c3aed'];
    let html = '<div class="d-flex flex-column gap-2">';
    for (let i = 1; i <= n; i++) {
        const days  = (i - 1) * 30;
        const date  = new Date(); date.setDate(date.getDate() + days);
        const label = i === 1 ? '1ère tranche — immédiate' : `Tranche ${i} — dans ${days} jours (${date.toLocaleDateString('fr-FR')})`;
        html += `<div style="display:flex;justify-content:space-between;align-items:center;
                             padding:.75rem 1rem;border-radius:10px;
                             background:${i===1?'#eff6ff':'#f8fafc'};
                             border:1.5px solid ${i===1?'#bfdbfe':'#e2e8f0'};">
            <span style="font-size:.84rem;color:${colors[(i-1)%4]};font-weight:700;">${label}</span>
            <span style="font-weight:900;color:${colors[(i-1)%4]};font-size:.95rem;">${formatFCFA(amount)}</span>
        </div>`;
    }
    html += '</div>';
    installmentDetail.innerHTML = html;
}

installmentBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        renderInstallments(parseInt(btn.dataset.n));
        updateDueNow(parseInt(btn.dataset.n));
        updatePayBtn();
    });
});

function updateDueNow(n) {
    const due    = Math.round(totalAmount / n);
    const fmtDue = formatFCFA(due);
    document.getElementById('dueNowAmount').textContent = fmtDue;
    document.getElementById('dueNowLabel').textContent  = n > 1
        ? `Tranche 1/${n} — les ${n - 1} suivante(s) dans 30, 60… jours`
        : '';
}

renderInstallments(parseInt(installmentInput.value) || 1);
updateDueNow(parseInt(installmentInput.value) || 1);

// ── Code Promo ───────────────────────────────────────────────────────────────
let appliedDiscount = 0;

document.getElementById('couponBtn').addEventListener('click', function () {
    const code = document.getElementById('couponInput').value.trim().toUpperCase();
    if (!code) return;

    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    fetch("{{ route('coupon.check') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ code }),
    })
    .then(r => r.json())
    .then(data => {
        const msg = document.getElementById('couponMsg');
        if (data.valid) {
            appliedDiscount = data.discount;
            document.getElementById('couponCodeHidden').value = data.code;
            document.getElementById('couponInput').value = data.code;
            document.getElementById('couponInput').readOnly = true;
            document.getElementById('couponBtn').textContent = '✓';
            document.getElementById('couponBtn').classList.replace('btn-outline-primary', 'btn-success');
            document.getElementById('couponBtn').disabled = true;

            // Afficher ligne remise
            document.getElementById('discountRow').style.setProperty('display', 'flex', 'important');
            document.getElementById('discountAmount').textContent = '-' + formatFCFA(data.discount);

            msg.innerHTML = `<span class="text-success fw-600"><i class="bi bi-check-circle me-1"></i>${data.message}</span>`;
            recalcDueNow();
        } else {
            msg.innerHTML = `<span class="text-danger"><i class="bi bi-x-circle me-1"></i>${data.message}</span>`;
            document.getElementById('couponBtn').disabled = false;
            document.getElementById('couponBtn').textContent = 'Appliquer';
        }
    })
    .catch(() => {
        document.getElementById('couponMsg').innerHTML = '<span class="text-danger">Erreur réseau. Réessayez.</span>';
        document.getElementById('couponBtn').disabled = false;
        document.getElementById('couponBtn').textContent = 'Appliquer';
    });
});

function recalcDueNow() {
    const n   = parseInt(installmentInput.value) || 1;
    const net = Math.max(0, totalAmount - appliedDiscount);
    const due = Math.round(net / n);
    document.getElementById('dueNowAmount').textContent = formatFCFA(due);
    document.getElementById('dueNowLabel').textContent  = n > 1
        ? `Tranche 1/${n} — les ${n - 1} suivante(s) dans 30, 60… jours`
        : '';
}

// Surcharger updateDueNow pour tenir compte de la remise
const _origUpdateDueNow = updateDueNow;
function updateDueNow(n) { recalcDueNow(); }

// ── Bouton de paiement ───────────────────────────────────────────────────────
    const totalFormatted = "{{ $cart->formatted_total }}";

    function updatePayBtn() {
        const method = document.querySelector('input[name="payment_method"]:checked')?.value || 'geniuspay';
        const n      = parseInt(installmentInput.value) || 1;
        const due    = formatFCFA(Math.round(totalAmount / n));
        const labels = {
            geniuspay: {
                icon: 'bi-box-arrow-up-right',
                text: 'Payer ' + due + ' via GeniusPay',
                note: n > 1 ? `1ère tranche sur ${n} — paiement sécurisé GeniusPay` : 'Paiement sécurisé — Powered by GeniusPay',
                spinner: 'Redirection vers GeniusPay…',
            },
            cash_on_delivery: {
                icon: 'bi-check-circle-fill',
                text: 'Confirmer — ' + due + ' à la livraison',
                note: n > 1 ? `1ère tranche sur ${n} payée à la livraison` : 'Vous paierez en espèces à la livraison',
                spinner: 'Confirmation en cours…',
            },
        };
        const l = labels[method] || labels['geniuspay'];
        document.getElementById('payBtnIcon').className  = 'bi ' + l.icon + ' me-2';
        document.getElementById('payBtnText').textContent = l.text;
        document.getElementById('payBtnNote').textContent = l.note;
    }

    document.querySelectorAll('input[name="payment_method"]').forEach(r => {
        r.addEventListener('change', updatePayBtn);
    });

    updatePayBtn(); // init

    document.getElementById('checkoutForm').addEventListener('submit', function () {
        const method = document.querySelector('input[name="payment_method"]:checked')?.value || 'geniuspay';
        const btn = document.getElementById('payBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>'
                      + labels[method].spinner;
    });
</script>
@endpush
@endsection
