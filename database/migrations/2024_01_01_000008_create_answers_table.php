<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_option_id')->nullable()->constrained()->nullOnDelete();
            $table->text('text_answer')->nullable()->comment('Para preguntas abiertas');
            $table->decimal('score', 5, 2)->default(0)->comment('Puntaje obtenido en esta respuesta');
            $table->timestamps();

            // Un candidato responde cada pregunta una sola vez por asignación
            $table->unique(['test_assignment_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
