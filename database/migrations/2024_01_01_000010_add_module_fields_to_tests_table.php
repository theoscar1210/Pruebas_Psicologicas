<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            // Módulo al que pertenece la prueba
            $table->string('module')->nullable()->after('created_by')
                ->comment('personalidad | cognitivo | competencias | proyectivo | entrevista');

            // Tipo específico de prueba
            $table->string('test_type')->nullable()->after('module')
                ->comment('big_five | 16pf | raven | assessment_center | wartegg | star_interview | technical | custom');

            // Indica si la calificación la hace un evaluador (no el sistema)
            $table->boolean('evaluator_scored')->default(false)->after('test_type');

            // Método de puntuación
            $table->string('scoring_method')->default('percentage')->after('evaluator_scored')
                ->comment('percentage | dimensional | evaluator');
        });
    }

    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn(['module', 'test_type', 'evaluator_scored', 'scoring_method']);
        });
    }
};
