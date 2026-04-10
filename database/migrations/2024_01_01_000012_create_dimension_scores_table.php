<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dimension_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_assignment_id')
                ->constrained('test_assignments')
                ->cascadeOnDelete();

            // Clave interna (openness, conscientiousness, raven_set_a, factor_A, etc.)
            $table->string('dimension_key');

            // Nombre para mostrar
            $table->string('dimension_name');

            // Puntuación bruta (suma de ítems)
            $table->decimal('raw_score', 8, 2)->default(0);

            // Puntuación normalizada 0–100
            $table->decimal('normalized_score', 5, 2)->default(0);

            // Nivel interpretativo
            $table->string('level')->nullable()
                ->comment('muy_bajo | bajo | medio | alto | muy_alto');

            // Texto de interpretación automática
            $table->text('interpretation')->nullable();

            $table->timestamps();

            $table->index(['test_assignment_id', 'dimension_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dimension_scores');
    }
};
