@php
    $isFav = auth()->check()
        ? \App\Models\Wishlist::isFavorite(auth()->id(), $product->id)
        : false;
@endphp

<article class="product-card">
    {{-- Image --}}
    <div class="img-wrapper">
        <a href="{{ route('products.show', $product) }}" tabindex="-1">
            <img src="{{ $product->image_url }}"
                 alt="{{ $product->name }}"
                 class="card-img-top"
                 loading="lazy"
                 onerror="this.src='https://placehold.co/400x200/f1f5f9/94a3b8?text={{ urlencode(Str::limit($product->name,12)) }}'">
        </a>

        {{-- Badge catégorie --}}
        @if($product->category)
            <span class="badge badge-category"
                  style="position:absolute;top:10px;left:10px;font-size:.65rem;
                         backdrop-filter:blur(8px);padding:.28em .72em;">
                {{ $product->category }}
            </span>
        @endif

        {{-- ❤️ Bouton Favori --}}
        @auth
        <button type="button"
                class="btn-heart {{ $isFav ? 'active' : '' }} wl-card-btn"
                data-product-id="{{ $product->id }}"
                data-toggle-url="{{ route('wishlist.toggle', $product) }}"
                data-csrf="{{ csrf_token() }}"
                title="{{ $isFav ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"
                style="position:absolute;top:10px;right:10px;z-index:3;">
            <i class="bi {{ $isFav ? 'bi-heart-fill' : 'bi-heart' }}"></i>
        </button>
        @endauth

        {{-- Badge stock --}}
        @if($product->stock === 0)
            <span class="stock-chip stock-empty"
                  style="position:absolute;{{ auth()->check() ? 'top:46px' : 'top:10px' }};right:10px;">
                <i class="bi bi-x-circle-fill"></i>Rupture
            </span>
        @elseif($product->stock <= 5)
            <span class="stock-chip stock-low"
                  style="position:absolute;{{ auth()->check() ? 'top:46px' : 'top:10px' }};right:10px;">
                <i class="bi bi-exclamation-triangle-fill"></i>{{ $product->stock }} restants
            </span>
        @endif

        {{-- Overlay hover --}}
        <div class="product-card-overlay">
            <a href="{{ route('products.show', $product) }}"
               class="btn btn-sm"
               style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);
                      color:#fff;backdrop-filter:blur(8px);border-radius:9px;
                      padding:.38rem .9rem;font-size:.8rem;font-weight:600;">
                <i class="bi bi-eye"></i>Voir le produit
            </a>
        </div>
    </div>

    <div class="card-body d-flex flex-column" style="padding:.95rem 1rem;">

        {{-- Nom --}}
        <h6 class="fw-700 mb-1" style="font-size:.88rem;line-height:1.35;color:var(--text);flex-grow:1;">
            <a href="{{ route('products.show', $product) }}"
               class="text-decoration-none" style="color:inherit;">
                {{ Str::limit($product->name, 52) }}
            </a>
        </h6>

        {{-- Note moyenne --}}
        @php $avg = $product->avg_rating; $cnt = $product->reviews_count; @endphp
        @if($cnt > 0)
        <div class="d-flex align-items-center gap-1 mb-1" style="font-size:.75rem;">
            @for($i=1;$i<=5;$i++)
                <i class="bi bi-star{{ $i <= round($avg) ? '-fill' : '' }}"
                   style="color:#f59e0b;font-size:.7rem;"></i>
            @endfor
            <span class="text-muted ms-1">{{ number_format($avg,1) }} ({{ $cnt }})</span>
        </div>
        @endif

        {{-- Description courte --}}
        @if($product->description)
            <p class="text-muted mb-2"
               style="font-size:.75rem;line-height:1.5;
                      display:-webkit-box;-webkit-line-clamp:2;
                      -webkit-box-orient:vertical;overflow:hidden;">
                {{ $product->description }}
            </p>
        @endif

        {{-- Prix + stock --}}
        <div class="d-flex align-items-center justify-content-between mt-auto pt-2"
             style="border-top:1px solid var(--border-2);">
            <span class="price">{{ $product->formatted_price }}</span>
            @if($product->stock > 5)
                <span class="stock-chip stock-ok">
                    <i class="bi bi-check-circle-fill"></i>En stock
                </span>
            @elseif($product->stock === 0)
                <span class="stock-chip stock-empty">Indisponible</span>
            @else
                <span class="stock-chip stock-low">{{ $product->stock }} restants</span>
            @endif
        </div>

        {{-- Actions --}}
        <div class="d-flex gap-2 mt-3">
            <a href="{{ route('products.show', $product) }}"
               class="btn btn-outline-primary btn-sm flex-grow-1">
                <i class="bi bi-eye"></i>Détails
            </a>

            @auth
                @if($product->isAvailable())
                    <form action="{{ route('cart.add') }}" method="POST" class="flex-grow-1">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-bag-plus"></i>Panier
                        </button>
                    </form>
                @else
                    <button class="btn btn-sm flex-grow-1" disabled
                            style="background:#f1f5f9;color:#94a3b8;
                                   border:1.5px solid var(--border);border-radius:8px;">
                        <i class="bi bi-x-circle"></i>Indispo
                    </button>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-bag-plus"></i>Panier
                </a>
            @endguest
        </div>
    </div>
</article>

@once
@push('styles')
<style>
    .product-card-overlay {
        position: absolute; inset: 0;
        background: rgba(15,23,42,.42);
        display: flex; align-items: center; justify-content: center;
        opacity: 0;
        transition: opacity .3s ease;
        backdrop-filter: blur(2px);
    }
    .product-card:hover .product-card-overlay { opacity: 1; }
    .product-card .img-wrapper { position: relative; }

    /* ── Bouton cœur ── */
    .btn-heart {
        position: absolute;
        top: 10px; right: 10px;
        width: 34px; height: 34px;
        border-radius: 50%;
        background: rgba(255,255,255,.92);
        border: none;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
        color: #cbd5e1;
        cursor: pointer;
        transition: all .25s cubic-bezier(.175,.885,.32,1.275);
        box-shadow: 0 2px 8px rgba(0,0,0,.12);
        z-index: 3;
        padding: 0;
    }
    .btn-heart:hover,
    .btn-heart.active {
        color: #ef4444;
        background: #fff;
        transform: scale(1.18);
        box-shadow: 0 4px 14px rgba(239,68,68,.28);
    }
    .btn-heart.active i { animation: heartPop .3s ease; }
    @keyframes heartPop {
        0%   { transform: scale(1); }
        50%  { transform: scale(1.4); }
        100% { transform: scale(1); }
    }
</style>
@endpush
@push('scripts')
<script>
(function () {
    /* Délègue le clic sur tous les boutons .wl-card-btn */
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.wl-card-btn');
        if (!btn) return;
        e.preventDefault();

        const url  = btn.dataset.toggleUrl;
        const csrf = btn.dataset.csrf;
        const icon = btn.querySelector('i');
        const isNowFav = !btn.classList.contains('active');

        /* Feedback visuel immédiat */
        btn.classList.toggle('active', isNowFav);
        icon.className = isNowFav ? 'bi bi-heart-fill' : 'bi bi-heart';
        btn.title = isNowFav ? 'Retirer des favoris' : 'Ajouter aux favoris';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(r => r.json())
        .then(data => {
            /* Mise à jour du compteur navbar */
            const badge = document.getElementById('navFavCount');
            if (badge) {
                badge.textContent = data.count;
                badge.style.display = data.count > 0 ? 'flex' : 'none';
            }
            /* Toast */
            showCardFavToast(data.added ? '❤️ Ajouté aux favoris' : '🤍 Retiré des favoris');
        })
        .catch(() => {
            /* Revert si erreur réseau */
            btn.classList.toggle('active', !isNowFav);
            icon.className = !isNowFav ? 'bi bi-heart-fill' : 'bi bi-heart';
        });
    });

    function showCardFavToast(msg) {
        let toast = document.getElementById('cardFavToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'cardFavToast';
            toast.style.cssText = [
                'position:fixed', 'bottom:1.5rem', 'right:1.5rem',
                'background:#1e293b', 'color:#fff',
                'padding:.7rem 1.2rem', 'border-radius:12px',
                'font-size:.84rem', 'font-weight:600',
                'box-shadow:0 8px 28px rgba(0,0,0,.25)',
                'z-index:9999', 'display:flex', 'align-items:center', 'gap:.5rem',
                'transform:translateY(80px)', 'opacity:0',
                'transition:all .3s cubic-bezier(.175,.885,.32,1.275)'
            ].join(';');
            document.body.appendChild(toast);
        }
        toast.textContent = msg;
        requestAnimationFrame(() => {
            toast.style.transform = 'translateY(0)';
            toast.style.opacity   = '1';
        });
        clearTimeout(toast._t);
        toast._t = setTimeout(() => {
            toast.style.transform = 'translateY(80px)';
            toast.style.opacity   = '0';
        }, 2500);
    }
}());
</script>
@endpush
@endonce
