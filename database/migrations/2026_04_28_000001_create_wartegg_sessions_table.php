<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wartegg_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assignment_id')->nullable()->constrained('test_assignments')->nullOnDelete();
            $table->string('status', 20)->default('pending'); // pending | in_progress | completed
            $table->json('boxes')->nullable();                 // array de 8 cajas con drawing_data, title, order, time_seconds
            $table->unsignedInteger('total_seconds')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wartegg_sessions');
    }
};
