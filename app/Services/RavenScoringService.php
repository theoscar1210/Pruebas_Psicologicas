<?php

namespace App\Services;

use App\Models\DimensionScore;
use App\Models\TestAssignment;

/**
 * Califica las Matrices Progresivas de Raven.
 *
 * Sets A, B, C, D (9 ítems cada uno) = 36 ítems en total.
 * Cada ítem correcto = 1 punto. Rango: 0–36.
 *
 * Tabla normativa simplificada (adultos 18–35 años):
 *   0–9   → Muy bajo  (<5p)
 *  10–18  → Bajo      (5–24p)
 *  19–24  → Promedio  (25–74p)
 *  25–30  → Alto      (75–89p)
 *  31–36  → Superior  (≥90p)
 */
class RavenScoringService
{
    private const NORMS = [
        // [umbral_mínimo, percentil, nivel, nivel_key]
        [31, 95, 'Superior',       'muy_alto'],
        [25, 75, 'Alto',           'alto'],
        [19, 50, 'Promedio',       'medio'],
        [10, 25, 'Bajo',           'bajo'],
        [0,   5, 'Muy bajo',       'muy_bajo'],
    ];

    private const SET_LABELS = [
        'set_a' => 'Set A – Completamiento',
        'set_b' => 'Set B – Analogías',
        'set_c' => 'Set C – Cambio progresivo',
        'set_d' => 'Set D – Permutaciones',
    ];

    public function calculate(TestAssignment $assignment): void
    {
        $assignment->load(['test.questions.options', 'answers.option']);

        $totalCorrect = 0;
        $totalItems   = 0;
        $setScores    = [];

        foreach ($assignment->test->questions as $question) {
            $answer = $assignment->answers->firstWhere('question_id', $question->id);
            $isCorrect = false;

            if ($answer && $answer->question_option_id) {
                $option = $question->options->firstWhere('id', $answer->question_option_id);
                $isCorrect = $option && $option->is_correct;
            }

            $set = $question->category ?? 'general';
            $setScores[$set]['correct'] = ($setScores[$set]['correct'] ?? 0) + ($isCorrect ? 1 : 0);
            $setScores[$set]['total']   = ($setScores[$set]['total']   ?? 0) + 1;

            $totalCorrect += $isCorrect ? 1 : 0;
            $totalItems++;
        }

        // Guardar por set
        foreach ($setScores as $setKey => $data) {
            $setNorm = $data['total'] > 0
                ? round($data['correct'] / $data['total'] * 100, 2)
                : 0;

            DimensionScore::updateOrCreate(
                [
                    'test_assignment_id' => $assignment->id,
                    'dimension_key'      => $setKey,
                ],
                [
                    'dimension_name'   => self::SET_LABELS[$setKey] ?? ucfirst($setKey),
                    'raw_score'        => $data['correct'],
                    'normalized_score' => $setNorm,
                    'level'            => $this->levelFromRaw($data['correct'], $data['total']),
                    'interpretation'   => null,
                ]
            );
        }

        // Guardar puntuación total
        $normalized = $totalItems > 0 ? round($totalCorrect / $totalItems * 100, 2) : 0;
        [$percentile, $levelLabel, $levelKey] = $this->normFromRaw($totalCorrect);

        DimensionScore::updateOrCreate(
            [
                'test_assignment_id' => $assignment->id,
                'dimension_key'      => 'raven_total',
            ],
            [
                'dimension_name'   => 'Raven — Puntuación Total',
                'raw_score'        => $totalCorrect,
                'normalized_score' => $normalized,
                'level'            => $levelKey,
                'interpretation'   => "Puntuación bruta: {$totalCorrect}/{$totalItems}. "
                                    . "Nivel: {$levelLabel} (percentil ≈ {$percentile}).",
            ]
        );
    }

    private function normFromRaw(int $raw): array
    {
        foreach (self::NORMS as [$min, $percentile, $label, $key]) {
            if ($raw >= $min) {
                return [$percentile, $label, $key];
            }
        }
        return [5, 'Muy bajo', 'muy_bajo'];
    }

    private function levelFromRaw(int $correct, int $total): string
    {
        if ($total === 0) {
            return 'muy_bajo';
        }
        $pct = $correct / $total * 100;
        return match (true) {
            $pct >= 85 => 'muy_alto',
            $pct >= 67 => 'alto',
            $pct >= 50 => 'medio',
            $pct >= 28 => 'bajo',
            default    => 'muy_bajo',
        };
    }
}
