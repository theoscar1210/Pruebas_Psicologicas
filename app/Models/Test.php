<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'instructions',
        'time_limit',
        'passing_score',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'time_limit' => 'integer',
        'passing_score' => 'integer',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'position_test')
            ->withPivot('order');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TestAssignment::class);
    }

    public function getTotalPointsAttribute(): int
    {
        return $this->questions->sum('points');
    }
}
