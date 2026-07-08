<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    // ─── AJAX : vérifier un code promo depuis le checkout ───────────
    public function check(Request $request)
    {
        $request->validate(['code' => 'required|string|max:50']);

        $coupon = Coupon::findValid($request->code);

        if (!$coupon) {
            return response()->json(['valid' => false, 'message' => 'Code invalide ou expiré.'], 422);
        }

        $cartTotal = \App\Models\Cart::getOrCreateActive(auth()->id())->total_amount;

        if ($cartTotal < $coupon->min_order_amount) {
            return response()->json([
                'valid'   => false,
                'message' => 'Commande minimum de ' . number_format($coupon->min_order_amount, 0, ',', ' ') . ' FCFA requise.',
            ], 422);
        }

        $discount = $coupon->calculateDiscount($cartTotal);

        return response()->json([
            'valid'          => true,
            'code'           => $coupon->code,
            'discount'       => $discount,
            'discount_label' => $coupon->getTypeLabel(),
            'new_total'      => $cartTotal - $discount,
            'message'        => "Code {$coupon->code} appliqué : -{$coupon->getTypeLabel()}",
        ]);
    }

    // ─── ADMIN : liste des coupons ──────────────────────────────────
    public function index()
    {
        $coupons = Coupon::latest()->paginate(20);
        return view('admin.coupons.index', compact('coupons'));
    }

    // ─── ADMIN : formulaire création ───────────────────────────────
    public function create()
    {
        return view('admin.coupons.create');
    }

    // ─── ADMIN : enregistrer un coupon ─────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'code'             => 'required|string|max:50|unique:coupons,code',
            'type'             => 'required|in:percent,fixed',
            'value'            => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses'         => 'nullable|integer|min:1',
            'expires_at'       => 'nullable|date|after:today',
            'is_active'        => 'boolean',
        ]);

        $data['code']             = strtoupper(trim($data['code']));
        $data['min_order_amount'] = $data['min_order_amount'] ?? 0;
        $data['is_active']        = $request->boolean('is_active', true);

        Coupon::create($data);

        return redirect()->route('admin.coupons.index')
            ->with('success', "Coupon \"{$data['code']}\" créé avec succès.");
    }

    // ─── ADMIN : formulaire édition ────────────────────────────────
    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    // ─── ADMIN : mettre à jour ─────────────────────────────────────
    public function update(Request $request, Coupon $coupon)
    {
        $data = $request->validate([
            'code'             => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type'             => 'required|in:percent,fixed',
            'value'            => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses'         => 'nullable|integer|min:1',
            'expires_at'       => 'nullable|date',
            'is_active'        => 'boolean',
        ]);

        $data['code']             = strtoupper(trim($data['code']));
        $data['min_order_amount'] = $data['min_order_amount'] ?? 0;
        $data['is_active']        = $request->boolean('is_active', false);

        $coupon->update($data);

        return redirect()->route('admin.coupons.index')
            ->with('success', "Coupon \"{$coupon->code}\" mis à jour.");
    }

    // ─── ADMIN : supprimer ─────────────────────────────────────────
    public function destroy(Coupon $coupon)
    {
        $code = $coupon->code;
        $coupon->delete();
        return redirect()->route('admin.coupons.index')
            ->with('success', "Coupon \"{$code}\" supprimé.");
    }
}
