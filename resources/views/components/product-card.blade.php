{{-- Composant réutilisable : Carte produit --}}
<div class="card product-card h-100">
    {{-- Image --}}
    <a href="{{ route('products.show', $product) }}">
        <img
            src="{{ $product->image_url }}"
            alt="{{ $product->name }}"
            class="card-img-top"
            onerror="this.src='https://placehold.co/400x220/e2e8f0/94a3b8?text=Produit'"
        >
    </a>

    <div class="card-body d-flex flex-column">
        {{-- Catégorie --}}
        @if($product->category)
            <span class="badge badge-category mb-2 align-self-start">{{ $product->category }}</span>
        @endif

        {{-- Nom --}}
        <h5 class="card-title fw-bold mb-1">
            <a href="{{ route('products.show', $product) }}" class="text-dark text-decoration-none stretched-link-off">
                {{ Str::limit($product->name, 50) }}
            </a>
        </h5>

        {{-- Description courte --}}
        <p class="card-text text-muted small flex-grow-1">
            {{ Str::limit($product->description, 80) }}
        </p>

        {{-- Prix + Stock --}}
        <div class="d-flex justify-content-between align-items-center mt-2 mb-3">
            <span class="price">{{ $product->formatted_price }}</span>
            @if($product->stock > 0)
                <span class="badge bg-success-subtle text-success stock-badge">
                    <i class="bi bi-check-circle me-1"></i>En stock ({{ $product->stock }})
                </span>
            @else
                <span class="badge bg-danger-subtle text-danger stock-badge">
                    <i class="bi bi-x-circle me-1"></i>Rupture
                </span>
            @endif
        </div>

        {{-- Actions --}}
        <div class="d-flex gap-2">
            <a href="{{ route('products.show', $product) }}"
               class="btn btn-outline-primary btn-sm flex-grow-1">
                <i class="bi bi-eye me-1"></i>Voir
            </a>
            @auth
                @if($product->isAvailable())
                    <form action="{{ route('cart.add') }}" method="POST" class="d-flex">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-cart-plus"></i>
                        </button>
                    </form>
                @else
                    <button class="btn btn-secondary btn-sm" disabled>
                        <i class="bi bi-cart-x"></i>
                    </button>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-cart-plus"></i>
                </a>
            @endauth
        </div>
    </div>
</div>
