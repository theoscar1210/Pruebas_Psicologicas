<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assignment_id')->nullable()->constrained('test_assignments')->nullOnDelete();
            $table->string('test_type', 50);
            $table->string('consent_version', 10)->default('1.0');
            $table->string('ip_address', 45);
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('consented_at');
            $table->timestamps();

            $table->index(['candidate_id', 'test_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consents');
    }
};
