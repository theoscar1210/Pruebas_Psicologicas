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
        'access_code',
        'position_id',
        'status',
        'created_by',
    ];

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

    public function psychologicalReports(): HasMany
    {
        return $this->hasMany(PsychologicalReport::class);
    }

    public function latestReport(): HasOne
    {
        return $this->hasOne(PsychologicalReport::class)->latestOfMany();
    }
}
