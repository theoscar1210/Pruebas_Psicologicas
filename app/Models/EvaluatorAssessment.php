<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluatorAssessment extends Model
{
    protected $fillable = [
        'candidate_id',
        'test_assignment_id',
        'evaluator_id',
        'assessment_type',
        'scores',
        'overall_score',
        'observations',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'scores'        => 'array',
        'overall_score' => 'decimal:2',
        'completed_at'  => 'datetime',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TestAssignment::class, 'test_assignment_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function typeLabel(): string
    {
        return match ($this->assessment_type) {
            'wartegg'           => 'Wartegg',
            'star_interview'    => 'Entrevista STAR',
            'assessment_center' => 'AC-SL Assessment Center',
            default             => ucfirst($this->assessment_type),
        };
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
