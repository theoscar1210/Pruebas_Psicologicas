<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('psychological_reports', function (Blueprint $table) {
            $table->text('ai_full_report')->nullable()->after('narrative_interview');
            $table->string('ai_full_report_recommendation')->nullable()->after('ai_full_report');
            $table->timestamp('ai_full_report_at')->nullable()->after('ai_full_report_recommendation');
        });
    }

    public function down(): void
    {
        Schema::table('psychological_reports', function (Blueprint $table) {
            $table->dropColumn(['ai_full_report', 'ai_full_report_recommendation', 'ai_full_report_at']);
        });
    }
};
