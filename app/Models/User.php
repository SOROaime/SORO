<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ========================
    // HELPERS DE RÔLE
    // ========================

    /** Vérifie si l'utilisateur est admin */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Vérifie si l'utilisateur est un client */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    // ========================
    // RELATIONS ELOQUENT
    // ========================

    /** Un utilisateur a plusieurs paniers (historique) */
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    /** Le panier actif de l'utilisateur */
    public function activeCart()
    {
        return $this->hasOne(Cart::class)->where('status', 'active');
    }

    /** Un utilisateur a plusieurs commandes */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
