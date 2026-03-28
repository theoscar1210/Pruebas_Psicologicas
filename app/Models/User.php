<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isHr(): bool
    {
        return in_array($this->role, ['admin', 'hr']);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'created_by');
    }

    public function tests(): HasMany
    {
        return $this->hasMany(Test::class, 'created_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TestAssignment::class, 'assigned_by');
    }
}
