<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\EvaluatorAssessment;
use App\Models\PsychologicalReport;
use App\Models\TestAssignment;
use Illuminate\Support\Facades\Auth;

/**
 * Genera el Perfil Psicológico completo de un candidato.
 *
 * Recopila:
 *   - Big Five (DimensionScores de la prueba big_five)
 *   - 16PF    (DimensionScores de la prueba 16pf)
 *   - Raven   (DimensionScores de la prueba raven)
 *   - Assessment Center (DimensionScores de la prueba assessment_center)
 *   - Wartegg + STAR    (EvaluatorAssessments)
 *
 * Calcula:
 *   - Nivel de ajuste al cargo
 *   - Riesgos laborales
 *   - Recomendación: APTO / APTO CON RESERVAS / NO APTO
 */
class PsychologicalReportService
{
    public function generate(Candidate $candidate, array $overrides = []): PsychologicalReport
    {
        $candidate->load([
            'assignments.test',
            'assignments.dimensionScores',
            'assignments.result',
            'evaluatorAssessments',
            'position',
        ]);

        $data = $this->collectData($candidate);
        $data = array_merge($data, $overrides);

        $risks = $this->identifyRisks($data);
        $data['labor_risks'] = $risks;

        [$recommendation, $adjustmentLevel, $adjustmentScore] = $this->computeRecommendation($data, $risks);
        $data['recommendation']   = $recommendation;
        $data['adjustment_level'] = $adjustmentLevel;
        $data['adjustment_score'] = $adjustmentScore;

        $report = PsychologicalReport::updateOrCreate(
            ['candidate_id' => $candidate->id, 'status' => 'in_progress'],
            array_merge($data, [
                'candidate_id' => $candidate->id,
                'position_id'  => $candidate->position_id,
                'evaluator_id' => Auth::id(),
                'status'       => 'in_progress',
            ])
        );

        return $report;
    }

    public function complete(PsychologicalReport $report, array $input): PsychologicalReport
    {
        $report->update(array_merge($input, [
            'status'       => 'completed',
            'completed_at' => now(),
        ]));

        return $report;
    }

    // ── Recopilación de datos ──────────────────────────────────────────────

    private function collectData(Candidate $candidate): array
    {
        $data = [];

        // Big Five
        $bfAssignment = $this->latestCompletedByType($candidate, 'big_five');
        if ($bfAssignment) {
            $dims = $bfAssignment->dimensionScores->keyBy('dimension_key');
            $data['bf_openness']         = (float) ($dims['openness']?->normalized_score          ?? 0);
            $data['bf_conscientiousness']= (float) ($dims['conscientiousness']?->normalized_score ?? 0);
            $data['bf_extraversion']     = (float) ($dims['extraversion']?->normalized_score      ?? 0);
            $data['bf_agreeableness']    = (float) ($dims['agreeableness']?->normalized_score     ?? 0);
            $data['bf_neuroticism']      = (float) ($dims['neuroticism']?->normalized_score       ?? 0);
        }

        // 16PF
        $pf16Assignment = $this->latestCompletedByType($candidate, '16pf');
        if ($pf16Assignment) {
            $data['pf16_scores'] = $pf16Assignment->dimensionScores
                ->pluck('normalized_score', 'dimension_key')
                ->toArray();
        }

        // Raven
        $ravenAssignment = $this->latestCompletedByType($candidate, 'raven');
        if ($ravenAssignment) {
            $ravenTotal = $ravenAssignment->dimensionScores
                ->firstWhere('dimension_key', 'raven_total');
            if ($ravenTotal) {
                $data['cognitive_score']      = (float) $ravenTotal->normalized_score;
                $data['cognitive_percentile'] = $this->scoreToPercentile((float) $ravenTotal->normalized_score);
                $data['cognitive_level']      = $this->cognitiveLevelLabel($ravenTotal->level);
            }
        }

        // Assessment Center
        $acAssignment = $this->latestCompletedByType($candidate, 'assessment_center');
        if ($acAssignment) {
            $data['competency_scores'] = $acAssignment->dimensionScores
                ->pluck('normalized_score', 'dimension_key')
                ->toArray();
        }

        // Wartegg (evaluador)
        $wartegg = $candidate->evaluatorAssessments
            ->where('assessment_type', 'wartegg')
            ->sortByDesc('created_at')
            ->first();
        if ($wartegg) {
            $data['wartegg_score']          = (float) ($wartegg->overall_score ?? 0);
            $data['projective_observations'] = $wartegg->observations;
        }

        // Entrevista STAR
        $star = $candidate->evaluatorAssessments
            ->where('assessment_type', 'star_interview')
            ->sortByDesc('created_at')
            ->first();
        if ($star) {
            $data['interview_score']          = (float) ($star->overall_score ?? 0);
            $data['interview_competencies']   = $star->scores;
            $data['interview_observations']   = $star->observations;
        }

        return $data;
    }

    // ── Lógica de riesgos ─────────────────────────────────────────────────

    private function identifyRisks(array $data): array
    {
        $risks = [];

        if (isset($data['bf_neuroticism']) && $data['bf_neuroticism'] > 72) {
            $risks[] = 'Alta inestabilidad emocional (Neuroticismo elevado)';
        }
        if (isset($data['bf_conscientiousness']) && $data['bf_conscientiousness'] < 30) {
            $risks[] = 'Baja responsabilidad y autodisciplina';
        }
        if (isset($data['bf_agreeableness']) && $data['bf_agreeableness'] < 28) {
            $risks[] = 'Dificultades en relaciones interpersonales (baja Amabilidad)';
        }
        if (isset($data['cognitive_score']) && $data['cognitive_score'] < 35) {
            $risks[] = 'Capacidad cognitiva por debajo del promedio para el cargo';
        }
        if (isset($data['interview_score']) && $data['interview_score'] < 40) {
            $risks[] = 'Deficiencias en competencias conductuales (entrevista STAR)';
        }
        if (isset($data['wartegg_score']) && $data['wartegg_score'] < 35) {
            $risks[] = 'Indicadores proyectivos de atención clínica (Wartegg)';
        }

        return $risks;
    }

    // ── Recomendación ─────────────────────────────────────────────────────

    private function computeRecommendation(array $data, array $risks): array
    {
        $scores = array_filter([
            $data['cognitive_score']       ?? null,
            $data['bf_conscientiousness']  ?? null,
            $data['bf_agreeableness']      ?? null,
            isset($data['bf_neuroticism'])  ? (100 - $data['bf_neuroticism']) : null,
            $data['interview_score']       ?? null,
            $data['wartegg_score']         ?? null,
        ], fn ($v) => $v !== null);

        $avgScore = count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : 50;

        // Factores bloqueantes para NO APTO
        $isNoApto = (
            (isset($data['cognitive_score'])      && $data['cognitive_score']      < 25) ||
            (isset($data['bf_conscientiousness']) && $data['bf_conscientiousness'] < 20) ||
            count($risks) >= 4
        );

        if ($isNoApto) {
            return ['no_apto', 'bajo', $avgScore];
        }

        if (count($risks) >= 2 || $avgScore < 50) {
            return ['apto_con_reservas', 'medio', $avgScore];
        }

        $level = $avgScore >= 70 ? 'alto' : 'medio';
        return ['apto', $level, $avgScore];
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function latestCompletedByType(Candidate $candidate, string $testType): ?TestAssignment
    {
        return $candidate->assignments
            ->filter(fn ($a) => $a->test?->test_type === $testType && $a->status === 'completed')
            ->sortByDesc('completed_at')
            ->first();
    }

    private function scoreToPercentile(float $score): int
    {
        return match (true) {
            $score >= 90 => 95,
            $score >= 70 => 75,
            $score >= 50 => 50,
            $score >= 28 => 25,
            default      => 5,
        };
    }

    private function cognitiveLevelLabel(string $level): string
    {
        return match ($level) {
            'muy_alto' => 'Superior',
            'alto'     => 'Alto',
            'medio'    => 'Promedio',
            'bajo'     => 'Bajo',
            'muy_bajo' => 'Deficiente',
            default    => 'No evaluado',
        };
    }
}
