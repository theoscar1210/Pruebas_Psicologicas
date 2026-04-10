<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsychologicalReport extends Model
{
    protected $fillable = [
        'candidate_id', 'position_id', 'evaluator_id',
        // Personalidad
        'bf_openness', 'bf_conscientiousness', 'bf_extraversion',
        'bf_agreeableness', 'bf_neuroticism', 'pf16_scores',
        // Cognitivo
        'cognitive_score', 'cognitive_level', 'cognitive_percentile',
        // Competencias
        'competency_scores',
        // Proyectivo
        'wartegg_score', 'projective_observations',
        // Entrevista
        'interview_score', 'interview_competencies', 'interview_observations',
        // Resultado
        'adjustment_score', 'adjustment_level',
        'labor_risks', 'recommendation', 'recommendation_notes', 'summary',
        'status', 'completed_at',
    ];

    protected $casts = [
        'bf_openness'           => 'decimal:2',
        'bf_conscientiousness'  => 'decimal:2',
        'bf_extraversion'       => 'decimal:2',
        'bf_agreeableness'      => 'decimal:2',
        'bf_neuroticism'        => 'decimal:2',
        'pf16_scores'           => 'array',
        'cognitive_score'       => 'decimal:2',
        'cognitive_percentile'  => 'integer',
        'competency_scores'     => 'array',
        'wartegg_score'         => 'decimal:2',
        'interview_score'       => 'decimal:2',
        'interview_competencies'=> 'array',
        'adjustment_score'      => 'decimal:2',
        'labor_risks'           => 'array',
        'completed_at'          => 'datetime',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    /** Badge CSS class for recommendation */
    public function recommendationBadgeClass(): string
    {
        return match ($this->recommendation) {
            'apto'              => 'badge-success',
            'apto_con_reservas' => 'badge-warning',
            'no_apto'           => 'badge-danger',
            default             => 'badge-neutral',
        };
    }

    /** Human-readable recommendation */
    public function recommendationLabel(): string
    {
        return match ($this->recommendation) {
            'apto'              => 'APTO',
            'apto_con_reservas' => 'APTO CON RESERVAS',
            'no_apto'           => 'NO APTO',
            default             => 'Pendiente',
        };
    }

    /** Badge for adjustment level */
    public function adjustmentBadgeClass(): string
    {
        return match ($this->adjustment_level) {
            'alto'  => 'badge-success',
            'medio' => 'badge-warning',
            'bajo'  => 'badge-danger',
            default => 'badge-neutral',
        };
    }

    /** Big Five dimensions as a keyed array for easy rendering */
    public function bigFiveDimensions(): array
    {
        return [
            'Apertura'          => (float) $this->bf_openness,
            'Responsabilidad'   => (float) $this->bf_conscientiousness,
            'Extraversión'      => (float) $this->bf_extraversion,
            'Amabilidad'        => (float) $this->bf_agreeableness,
            'Neuroticismo'      => (float) $this->bf_neuroticism,
        ];
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
