<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Resultado calculado y almacenado al finalizar la prueba
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_assignment_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('total_score', 8, 2)->default(0);
            $table->decimal('max_score', 8, 2)->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->boolean('passed')->default(false);
            $table->text('notes')->nullable()->comment('Observaciones del evaluador');
            $table->timestamp('calculated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_results');
    }
};
