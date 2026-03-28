<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Asignación de una prueba específica a un candidato
        Schema::create('test_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'expired'])->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedSmallInteger('time_remaining')->nullable()->comment('Segundos restantes al pausar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_assignments');
    }
};
