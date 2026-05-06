<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'document_number',
        'position_id',
        'status',
        'created_by',
    ];

    protected $casts = [];

    protected static function boot(): void
    {
        parent::boot();

        // Genera código único automáticamente al crear un candidato
        static::creating(function (Candidate $candidate) {
            if (empty($candidate->access_code)) {
                $candidate->access_code = strtoupper(Str::random(8));
            }
        });
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TestAssignment::class);
    }

    public function completedAssignments(): HasMany
    {
        return $this->hasMany(TestAssignment::class)->where('status', 'completed');
    }

    public function evaluatorAssessments(): HasMany
    {
        return $this->hasMany(EvaluatorAssessment::class);
    }

    public function warteggSessions(): HasMany
    {
        return $this->hasMany(WarteggSession::class);
    }

    public function tscSlSessions(): HasMany
    {
        return $this->hasMany(\App\Models\TscSlSession::class);
    }

    public function tteSlSessions(): HasMany
    {
        return $this->hasMany(\App\Models\TteSlSession::class);
    }

    public function consents(): HasMany
    {
        return $this->hasMany(\App\Models\Consent::class)->orderByDesc('consented_at');
    }

    public function psychologicalReports(): HasMany
    {
        return $this->hasMany(PsychologicalReport::class);
    }

    public function latestReport(): HasOne
    {
        return $this->hasOne(PsychologicalReport::class)->latestOfMany();
    }
}
