<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    protected $fillable = ['user_id', 'session_id', 'guest_name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id')->orderBy('created_at');
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->user) return $this->user->name;
        if ($this->guest_name) return $this->guest_name;
        return 'Visiteur #' . $this->id;
    }

    public function getLastMessageAttribute(): ?ChatMessage
    {
        return $this->messages()->latest()->first();
    }
}
