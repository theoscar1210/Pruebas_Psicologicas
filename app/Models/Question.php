<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'text',
        'type',
        'points',
        'order',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'points' => 'integer',
        'order' => 'integer',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function isMultipleChoice(): bool
    {
        return $this->type === 'multiple_choice';
    }

    public function isLikert(): bool
    {
        return $this->type === 'likert';
    }

    public function isOpen(): bool
    {
        return $this->type === 'open';
    }
}
