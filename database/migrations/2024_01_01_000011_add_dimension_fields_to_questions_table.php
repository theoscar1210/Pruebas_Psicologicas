<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Dimensión psicológica que mide (p.ej. openness, raven_set_a)
            $table->string('dimension')->nullable()->after('is_required');

            // Para Big Five/16PF: indica si la puntuación se invierte
            $table->boolean('reverse_scored')->default(false)->after('dimension');

            // Para Raven: ruta a la imagen de la matriz
            $table->string('image_path')->nullable()->after('reverse_scored');

            // Categoría o subgrupo (p.ej. set_a, set_b para Raven)
            $table->string('category')->nullable()->after('image_path');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['dimension', 'reverse_scored', 'image_path', 'category']);
        });
    }
};
