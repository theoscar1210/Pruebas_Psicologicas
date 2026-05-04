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
            $table->text('narrative_personality')->nullable()->after('pf16_scores');
            $table->text('narrative_cognitive')->nullable()->after('cognitive_percentile');
            $table->text('narrative_competencies')->nullable()->after('competency_scores');
            $table->text('narrative_projective')->nullable()->after('projective_observations');
            $table->text('narrative_interview')->nullable()->after('interview_observations');
        });
    }

    public function down(): void
    {
        Schema::table('psychological_reports', function (Blueprint $table) {
            $table->dropColumn([
                'narrative_personality',
                'narrative_cognitive',
                'narrative_competencies',
                'narrative_projective',
                'narrative_interview',
            ]);
        });
    }
};
