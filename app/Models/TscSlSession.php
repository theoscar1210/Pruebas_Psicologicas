<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TscSlSession extends Model
{
    protected $fillable = [
        'candidate_id', 'assignment_id', 'status',
        'm1_answers', 'm1_score',
        'm2_answers', 'm2_score',
        'm3_responses', 'm3_scores', 'm3_score', 'm3_evaluator_id',
        'total_score', 'dimension_scores', 'performance_level',
        'started_at', 'm1_completed_at', 'm2_completed_at',
        'm3_submitted_at', 'completed_at',
    ];

    protected $casts = [
        'm1_answers'       => 'array',
        'm2_answers'       => 'array',
        'm3_responses'     => 'array',
        'm3_scores'        => 'array',
        'dimension_scores' => 'array',
        'started_at'       => 'datetime',
        'm1_completed_at'  => 'datetime',
        'm2_completed_at'  => 'datetime',
        'm3_submitted_at'  => 'datetime',
        'completed_at'     => 'datetime',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TestAssignment::class);
    }

    public function m3Evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'm3_evaluator_id');
    }

    public function performanceLevelLabel(): string
    {
        return match ($this->performance_level) {
            'sobresaliente'    => 'Sobresaliente',
            'alto'             => 'Alto',
            'adecuado'         => 'Adecuado',
            'en_desarrollo'    => 'En desarrollo',
            'por_debajo'       => 'Por debajo del perfil',
            default            => '—',
        };
    }

    public function performanceLevelColor(): string
    {
        return match ($this->performance_level) {
            'sobresaliente' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
            'alto'          => 'text-brand-700 bg-brand-50 border-brand-200',
            'adecuado'      => 'text-amber-700 bg-amber-50 border-amber-200',
            'en_desarrollo' => 'text-orange-700 bg-orange-50 border-orange-200',
            'por_debajo'    => 'text-red-700 bg-red-50 border-red-200',
            default         => 'text-slate-600 bg-slate-50 border-slate-200',
        };
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function needsM3Scoring(): bool
    {
        return $this->status === 'm3_submitted';
    }
}
