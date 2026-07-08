<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * ProductController — Gestion du catalogue produits
 * 
 * - Index/Show : accessibles à tous (liste et détail produits)
 * - Create/Store/Edit/Update/Destroy : réservés aux admins (via route middleware)
 */
class ProductController extends Controller
{
    // ========================
    // LISTE PUBLIQUE DES PRODUITS
    // ========================

    /** Page d'accueil avec produits en vedette */
    public function home()
    {
        $featuredProducts = Product::active()->inStock()->latest()->take(8)->get();
        $categories = Product::active()->distinct()->pluck('category')->filter()->sort()->values();

        return view('home', compact('featuredProducts', 'categories'));
    }

    /** Catalogue complet avec filtres et recherche */
    public function index(Request $request)
    {
        $query = Product::active();

        // Filtre par catégorie
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Recherche par nom/description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Tri
        match ($request->sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name'       => $query->orderBy('name', 'asc'),
            default      => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Product::active()->distinct()->pluck('category')->filter()->sort()->values();

        return view('products.index', compact('products', 'categories'));
    }

    /** Page de détail d'un produit */
    public function show(Product $product)
    {
        // Vérifier que le produit est visible
        if (!$product->is_active) {
            abort(404);
        }

        // Produits similaires (même catégorie)
        $relatedProducts = Product::active()
            ->inStock()
            ->where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        $reviews    = $product->reviews()->with('user')->get();
        $userReview = auth()->check()
            ? $reviews->firstWhere('user_id', auth()->id())
            : null;

        $schemaData = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Product',
            'name'        => $product->name,
            'description' => \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 300),
            'image'       => $product->image_url,
            'url'         => route('products.show', $product),
            'brand'       => ['@type' => 'Brand', 'name' => 'ShopCI'],
            'offers'      => [
                '@type'           => 'Offer',
                'priceCurrency'   => 'XOF',
                'price'           => (string) $product->price,
                'availability'    => $product->stock > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'seller'          => ['@type' => 'Organization', 'name' => 'ShopCI'],
            ],
        ];

        $avgRating    = round($reviews->avg('rating') ?? 0, 1);
        $reviewCount  = $reviews->count();
        if ($reviewCount > 0) {
            $schemaData['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => (string) $avgRating,
                'reviewCount' => (string) $reviewCount,
                'bestRating'  => '5',
                'worstRating' => '1',
            ];
        }

        $productSchema = json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return view('products.show', compact('product', 'relatedProducts', 'reviews', 'userReview', 'productSchema'));
    }

    // ========================
    // CRUD ADMIN
    // ========================

    /** Formulaire de création (admin) */
    public function create()
    {
        $categories = Product::distinct()->pluck('category')->filter()->sort()->values();
        return view('admin.products.create', compact('categories'));
    }

    /** Enregistrer un nouveau produit (admin) */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        // Gestion de l'upload d'image
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $product = Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', "Le produit \"{$product->name}\" a été créé avec succès.");
    }

    /** Formulaire d'édition (admin) */
    public function edit(Product $product)
    {
        $categories = Product::distinct()->pluck('category')->filter()->sort()->values();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /** Mettre à jour un produit (admin) */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        // Nouvelle image : supprimer l'ancienne
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', false);

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', "Le produit \"{$product->name}\" a été mis à jour.");
    }

    /** Supprimer un produit (admin) */
    public function destroy(Product $product)
    {
        // Supprimer l'image associée
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $productName = $product->name;
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Le produit \"{$productName}\" a été supprimé.");
    }
}
