<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DimensionScore extends Model
{
    protected $fillable = [
        'test_assignment_id',
        'dimension_key',
        'dimension_name',
        'raw_score',
        'normalized_score',
        'level',
        'interpretation',
    ];

    protected $casts = [
        'raw_score'        => 'decimal:2',
        'normalized_score' => 'decimal:2',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TestAssignment::class, 'test_assignment_id');
    }

    /** Badge CSS class based on level */
    public function levelBadgeClass(): string
    {
        return match ($this->level) {
            'muy_alto' => 'badge-success',
            'alto'     => 'badge-info',
            'medio'    => 'badge-neutral',
            'bajo'     => 'badge-warning',
            'muy_bajo' => 'badge-danger',
            default    => 'badge-neutral',
        };
    }

    /** Human-readable level label */
    public function levelLabel(): string
    {
        return match ($this->level) {
            'muy_alto' => 'Muy alto',
            'alto'     => 'Alto',
            'medio'    => 'Medio',
            'bajo'     => 'Bajo',
            'muy_bajo' => 'Muy bajo',
            default    => '—',
        };
    }
}
