<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /** Vérifie si un produit est en favori pour un utilisateur */
    public static function isFavorite(int $userId, int $productId): bool
    {
        return self::where('user_id', $userId)
                   ->where('product_id', $productId)
                   ->exists();
    }

    /** Toggle : ajoute ou retire des favoris */
    public static function toggle(int $userId, int $productId): bool
    {
        $existing = self::where('user_id', $userId)
                        ->where('product_id', $productId)
                        ->first();

        if ($existing) {
            $existing->delete();
            return false; // retiré
        }

        self::create(['user_id' => $userId, 'product_id' => $productId]);
        return true; // ajouté
    }
}
