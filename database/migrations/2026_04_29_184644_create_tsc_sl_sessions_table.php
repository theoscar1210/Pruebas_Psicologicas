<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tsc_sl_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assignment_id')->nullable()->constrained('test_assignments')->nullOnDelete();
            // pending → m1_done → m2_done → m3_submitted → completed
            $table->string('status', 20)->default('pending');

            // Módulo 1 — SJT: {"1":"B","2":"C",...}
            $table->json('m1_answers')->nullable();
            $table->unsignedSmallInteger('m1_score')->nullable(); // 0–60

            // Módulo 2 — Likert: {"21":4,"22":3,...}
            $table->json('m2_answers')->nullable();
            $table->unsignedSmallInteger('m2_score')->nullable(); // 0–150 (items 21–50)

            // Módulo 3 — Escenarios: texto abierto {"1":"...","2":"...","3":"..."}
            $table->json('m3_responses')->nullable();

            // Calificación M3 por evaluador: {"1":4,"just_1":"...","2":3,"just_2":"...","3":5,"just_3":"..."}
            $table->json('m3_scores')->nullable();
            $table->unsignedTinyInteger('m3_score')->nullable(); // 0–15
            $table->foreignId('m3_evaluator_id')->nullable()->constrained('users')->nullOnDelete();

            // Resultados finales
            $table->unsignedSmallInteger('total_score')->nullable(); // 0–225
            $table->json('dimension_scores')->nullable();            // {"E1":xx,"E2":xx,...}
            $table->string('performance_level', 20)->nullable();

            // Timestamps de progreso
            $table->timestamp('started_at')->nullable();
            $table->timestamp('m1_completed_at')->nullable();
            $table->timestamp('m2_completed_at')->nullable();
            $table->timestamp('m3_submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tsc_sl_sessions');
    }
};
