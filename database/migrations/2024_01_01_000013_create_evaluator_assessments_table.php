<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluator_assessments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('candidate_id')
                ->constrained('candidates')
                ->cascadeOnDelete();

            // Puede estar vinculado a una asignación existente o ser independiente
            $table->foreignId('test_assignment_id')
                ->nullable()
                ->constrained('test_assignments')
                ->nullOnDelete();

            $table->foreignId('evaluator_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Tipo de evaluación clínica
            $table->string('assessment_type')
                ->comment('wartegg | star_interview | assessment_center');

            // Puntuaciones detalladas en JSON (estructura flexible por tipo)
            $table->json('scores');

            // Puntuación global resultante (0–100)
            $table->decimal('overall_score', 5, 2)->nullable();

            // Observaciones clínicas libres del evaluador
            $table->text('observations')->nullable();

            $table->string('status')->default('completed')
                ->comment('pending | in_progress | completed');

            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['candidate_id', 'assessment_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluator_assessments');
    }
};
