<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla pivot: un cargo puede tener varias pruebas asignadas en orden
        Schema::create('position_test', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('order')->default(1);
            $table->timestamps();

            $table->unique(['position_id', 'test_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('position_test');
    }
};
