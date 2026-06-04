<?php
// FILE: app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'role',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ── Filament: siapa yang boleh akses panel ────────────────────────────
    public function canAccessPanel(Panel $panel): bool
    {
        return true; // semua user bisa login, akses dikontrol per resource
    }

    // ── Role helpers ──────────────────────────────────────────────────────
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isUser(): bool  { return $this->role === 'user'; }

    // ── Relationships ─────────────────────────────────────────────────────
    public function kpiSubmissions(): HasMany
    {
        return $this->hasMany(KpiSubmission::class);
    }
}