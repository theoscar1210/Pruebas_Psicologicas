<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
// DimensionScore is autoloaded via namespace

class TestAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'test_id',
        'position_id',
        'assigned_by',
        'status',
        'expires_at',
        'started_at',
        'completed_at',
        'time_remaining',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'time_remaining' => 'integer',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function result(): HasOne
    {
        return $this->hasOne(TestResult::class);
    }

    public function dimensionScores(): HasMany
    {
        return $this->hasMany(DimensionScore::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' ||
            ($this->expires_at && $this->expires_at->isPast() && !$this->isCompleted());
    }
}
