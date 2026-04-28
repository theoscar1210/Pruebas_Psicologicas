<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarteggSession extends Model
{
    protected $fillable = [
        'candidate_id', 'assignment_id', 'status',
        'boxes', 'total_seconds', 'started_at', 'completed_at',
    ];

    protected $casts = [
        'boxes'        => 'array',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TestAssignment::class);
    }

    /** Retorna los datos de una caja específica (1–8) o defaults vacíos. */
    public function getBox(int $number): array
    {
        $found = collect($this->boxes ?? [])->firstWhere('number', $number);
        return $found ?? [
            'number'       => $number,
            'drawing_data' => null,
            'title'        => '',
            'order'        => null,
            'time_seconds' => 0,
        ];
    }

    /** Cuántas cajas tienen dibujo guardado. */
    public function completedBoxesCount(): int
    {
        return collect($this->boxes ?? [])->filter(fn($b) => !empty($b['drawing_data']))->count();
    }
}
