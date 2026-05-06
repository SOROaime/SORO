<div class="product-card card">
    {{-- Image --}}
    <div class="img-wrapper">
        <a href="{{ route('products.show', $product) }}">
            <img src="{{ $product->image_url }}"
                 alt="{{ $product->name }}"
                 class="card-img-top"
                 onerror="this.src='https://placehold.co/400x210/f1f5f9/94a3b8?text={{ urlencode(Str::limit($product->name,12)) }}'">
        </a>
        {{-- Overlay badge catégorie --}}
        @if($product->category)
            <span class="badge badge-category px-3 py-2"
                  style="position:absolute;top:10px;left:10px;font-size:.68rem;backdrop-filter:blur(8px);">
                {{ $product->category }}
            </span>
        @endif
        {{-- Badge stock --}}
        @if($product->stock === 0)
            <span class="stock-chip stock-empty"
                  style="position:absolute;top:10px;right:10px;">
                Rupture
            </span>
        @elseif($product->stock <= 5)
            <span class="stock-chip stock-low"
                  style="position:absolute;top:10px;right:10px;">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $product->stock }} restants
            </span>
        @endif
    </div>

    <div class="card-body p-3 d-flex flex-column">
        {{-- Nom --}}
        <h6 class="fw-700 mb-1 flex-grow-1" style="font-size:.9rem;line-height:1.35;color:var(--text);">
            <a href="{{ route('products.show', $product) }}"
               class="text-decoration-none stretched-link-custom"
               style="color:inherit;">
                {{ Str::limit($product->name, 52) }}
            </a>
        </h6>

        {{-- Description courte --}}
        @if($product->description)
            <p class="text-muted mb-2" style="font-size:.76rem;line-height:1.5;
               display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                {{ $product->description }}
            </p>
        @endif

        {{-- Prix + stock --}}
        <div class="d-flex align-items-center justify-content-between mt-auto pt-2"
             style="border-top:1px solid var(--border);">
            <span class="price">{{ $product->formatted_price }}</span>
            @if($product->stock > 5)
                <span class="stock-chip stock-ok">
                    <i class="bi bi-check-circle-fill me-1"></i>En stock
                </span>
            @elseif($product->stock === 0)
                <span class="stock-chip stock-empty">Indisponible</span>
            @else
                <span class="stock-chip stock-low">{{ $product->stock }} restants</span>
            @endif
        </div>

        {{-- Boutons d'action --}}
        <div class="d-flex gap-2 mt-3">
            <a href="{{ route('products.show', $product) }}"
               class="btn btn-outline-primary btn-sm flex-grow-1">
                <i class="bi bi-eye"></i>Voir
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
                            style="background:#f1f5f9;color:#94a3b8;border:1px solid var(--border);">
                        <i class="bi bi-x-circle"></i>Indisponible
                    </button>
                @endif
            @endauth
            @guest
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-bag-plus"></i>Panier
                </a>
            @endguest
        </div>
    </div>
</div>
