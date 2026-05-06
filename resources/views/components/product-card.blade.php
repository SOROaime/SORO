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

        {{-- Badge stock --}}
        @if($product->stock === 0)
            <span class="stock-chip stock-empty"
                  style="position:absolute;top:10px;right:10px;">
                <i class="bi bi-x-circle-fill"></i>Rupture
            </span>
        @elseif($product->stock <= 5)
            <span class="stock-chip stock-low"
                  style="position:absolute;top:10px;right:10px;">
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
               class="text-decoration-none"
               style="color:inherit;">
                {{ Str::limit($product->name, 52) }}
            </a>
        </h6>

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
</style>
@endpush
@endonce
