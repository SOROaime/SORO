<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_order_amount',
        'max_uses', 'used_count', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'expires_at'       => 'datetime',
        'is_active'        => 'boolean',
        'value'            => 'float',
        'min_order_amount' => 'float',
    ];

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) return false;
        return true;
    }

    public function calculateDiscount(float $orderAmount): float
    {
        if ($orderAmount < $this->min_order_amount) return 0;

        if ($this->type === 'percent') {
            return round($orderAmount * ($this->value / 100), 0);
        }

        return min($this->value, $orderAmount);
    }

    public function getTypeLabel(): string
    {
        return $this->type === 'percent' ? "{$this->value}%" : number_format($this->value, 0, ',', ' ') . ' FCFA';
    }

    public static function findValid(string $code): ?self
    {
        $coupon = self::where('code', strtoupper(trim($code)))->first();
        return ($coupon && $coupon->isValid()) ? $coupon : null;
    }
}
