<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tests(): BelongsToMany
    {
        return $this->belongsToMany(Test::class, 'position_test')
            ->withPivot('order')
            ->orderByPivot('order');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }
}
