<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psychological_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('candidate_id')
                ->constrained('candidates')
                ->cascadeOnDelete();

            $table->foreignId('position_id')
                ->nullable()
                ->constrained('positions')
                ->nullOnDelete();

            $table->foreignId('evaluator_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // ── Módulo Personalidad ─────────────────────────────────────────
            // Big Five (puntuaciones 0–100)
            $table->decimal('bf_openness', 5, 2)->nullable();
            $table->decimal('bf_conscientiousness', 5, 2)->nullable();
            $table->decimal('bf_extraversion', 5, 2)->nullable();
            $table->decimal('bf_agreeableness', 5, 2)->nullable();
            $table->decimal('bf_neuroticism', 5, 2)->nullable();

            // 16PF factores (JSON: {A: score, B: score, ...})
            $table->json('pf16_scores')->nullable();

            // ── Módulo Cognitivo ────────────────────────────────────────────
            $table->decimal('cognitive_score', 5, 2)->nullable();
            $table->string('cognitive_level')->nullable()
                ->comment('deficiente | bajo | promedio | alto | superior');
            $table->integer('cognitive_percentile')->nullable();

            // ── Módulo Competencias ─────────────────────────────────────────
            // Assessment Center (JSON: {liderazgo: score, trabajo_equipo: score, ...})
            $table->json('competency_scores')->nullable();

            // ── Proyectivo (Wartegg) ────────────────────────────────────────
            $table->decimal('wartegg_score', 5, 2)->nullable();
            $table->text('projective_observations')->nullable();

            // ── Entrevista STAR ─────────────────────────────────────────────
            $table->decimal('interview_score', 5, 2)->nullable();
            $table->json('interview_competencies')->nullable();
            $table->text('interview_observations')->nullable();

            // ── Resultado final ─────────────────────────────────────────────
            // Nivel de ajuste al cargo (0–100)
            $table->decimal('adjustment_score', 5, 2)->nullable();
            $table->string('adjustment_level')->nullable()
                ->comment('alto | medio | bajo');

            // Riesgos laborales identificados (array de strings)
            $table->json('labor_risks')->nullable();

            // Recomendación final
            $table->string('recommendation')->nullable()
                ->comment('apto | apto_con_reservas | no_apto');
            $table->text('recommendation_notes')->nullable();

            // Resumen narrativo del evaluador
            $table->text('summary')->nullable();

            $table->string('status')->default('in_progress')
                ->comment('in_progress | completed');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['candidate_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psychological_reports');
    }
};
