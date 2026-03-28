<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_assignment_id',
        'total_score',
        'max_score',
        'percentage',
        'passed',
        'notes',
        'calculated_at',
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'passed' => 'boolean',
        'calculated_at' => 'datetime',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TestAssignment::class, 'test_assignment_id');
    }

    public function getScoreLabel(): string
    {
        return "{$this->total_score} / {$this->max_score} ({$this->percentage}%)";
    }
}
