<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consent extends Model
{
    protected $fillable = [
        'candidate_id',
        'assignment_id',
        'test_type',
        'consent_version',
        'ip_address',
        'user_agent',
        'consented_at',
    ];

    protected $casts = [
        'consented_at' => 'datetime',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TestAssignment::class);
    }

    public function testTypeLabel(): string
    {
        return match ($this->test_type) {
            'tsc_sl'   => 'TSC-SL — Test de Servicio al Cliente',
            'tsc_sl_h' => 'TSC-SL Hospitalidad — Servicio de Mesa y F&B',
            'tte_sl'   => 'TTE-SL — Test de Trabajo en Equipo',
            'wartegg' => 'Test de Wartegg',
            default  => strtoupper($this->test_type),
        };
    }
}
