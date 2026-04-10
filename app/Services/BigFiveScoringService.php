<?php

namespace App\Services;

use App\Models\DimensionScore;
use App\Models\TestAssignment;

/**
 * Calcula los cinco factores OCEAN del test Big Five (IPIP-50).
 *
 * Dimensiones:
 *   openness          → Apertura a la experiencia
 *   conscientiousness → Responsabilidad / Escrupulosidad
 *   extraversion      → Extraversión
 *   agreeableness     → Amabilidad
 *   neuroticism       → Neuroticismo
 *
 * Escala Likert 1–5. Ítems con reverse_scored=true se invierten: score = 6 − raw.
 * Rango bruto por dimensión (10 ítems × 1–5): 10–50.
 * Normalización a 0–100: (raw − 10) / 40 × 100.
 */
class BigFiveScoringService
{
    private const DIMENSIONS = [
        'openness'          => 'Apertura a la experiencia',
        'conscientiousness' => 'Responsabilidad',
        'extraversion'      => 'Extraversión',
        'agreeableness'     => 'Amabilidad',
        'neuroticism'       => 'Neuroticismo',
    ];

    /** Interpretaciones narrativas por dimensión y nivel */
    private const INTERPRETATIONS = [
        'openness' => [
            'muy_alto' => 'Alta curiosidad intelectual, creatividad y apertura a nuevas experiencias.',
            'alto'     => 'Buena disposición hacia ideas novedosas y experiencias diversas.',
            'medio'    => 'Equilibrio entre lo convencional y lo innovador.',
            'bajo'     => 'Preferencia por lo concreto y rutinario sobre lo abstracto.',
            'muy_bajo' => 'Marcada preferencia por rutinas establecidas y resistencia al cambio.',
        ],
        'conscientiousness' => [
            'muy_alto' => 'Alta organización, autodisciplina y orientación al logro.',
            'alto'     => 'Buena planificación y cumplimiento de compromisos.',
            'medio'    => 'Nivel adecuado de organización y responsabilidad.',
            'bajo'     => 'Tendencia a la desorganización y postergación.',
            'muy_bajo' => 'Dificultades significativas para cumplir compromisos y mantener orden.',
        ],
        'extraversion' => [
            'muy_alto' => 'Persona muy sociable, enérgica y asertiva en grupos.',
            'alto'     => 'Facilidad para relacionarse y dinamismo social.',
            'medio'    => 'Comodidad tanto en contextos sociales como en la soledad.',
            'bajo'     => 'Preferencia por ambientes tranquilos y pocos estímulos sociales.',
            'muy_bajo' => 'Marcada introversión y reticencia a la interacción social.',
        ],
        'agreeableness' => [
            'muy_alto' => 'Alta empatía, cooperación y orientación prosocial.',
            'alto'     => 'Buena disposición para el trabajo en equipo y el apoyo mutuo.',
            'medio'    => 'Balance entre asertividad propia y consideración hacia los demás.',
            'bajo'     => 'Tendencia a anteponer intereses personales; puede generar fricciones.',
            'muy_bajo' => 'Baja empatía y alta conflictividad interpersonal potencial.',
        ],
        'neuroticism' => [
            'muy_alto' => 'Alta reactividad emocional; propenso/a a ansiedad y estrés frecuente.',
            'alto'     => 'Mayor sensibilidad a situaciones de presión y cambios inesperados.',
            'medio'    => 'Reactividad emocional dentro de rangos esperados.',
            'bajo'     => 'Buena estabilidad emocional ante situaciones adversas.',
            'muy_bajo' => 'Alta resiliencia y ecuanimidad emocional frente al estrés.',
        ],
    ];

    public function calculate(TestAssignment $assignment): void
    {
        $assignment->load(['test.questions.options', 'answers.option']);

        // Agrupar respuestas por dimensión
        $dimensionRaws = [];

        foreach ($assignment->test->questions as $question) {
            $dimension = $question->dimension;
            if (!$dimension || !isset(self::DIMENSIONS[$dimension])) {
                continue;
            }

            $answer = $assignment->answers->firstWhere('question_id', $question->id);
            if (!$answer || !$answer->question_option_id) {
                continue;
            }

            // El valor de la opción Likert (1–5)
            $option = $question->options->firstWhere('id', $answer->question_option_id);
            if (!$option) {
                continue;
            }

            $raw = (float) $option->value;

            // Invertir si corresponde
            if ($question->reverse_scored) {
                $raw = 6 - $raw;
            }

            $dimensionRaws[$dimension][] = $raw;
        }

        // Calcular y guardar puntuación por dimensión
        foreach (self::DIMENSIONS as $key => $name) {
            $values = $dimensionRaws[$key] ?? [];
            $rawScore = array_sum($values);
            $itemCount = count($values) ?: 1;

            // Normalizar: rango teórico mínimo = items×1, máximo = items×5
            $min = $itemCount;
            $max = $itemCount * 5;
            $normalized = $max > $min
                ? round(($rawScore - $min) / ($max - $min) * 100, 2)
                : 0;

            $level = $this->levelFromScore($normalized);

            DimensionScore::updateOrCreate(
                [
                    'test_assignment_id' => $assignment->id,
                    'dimension_key'      => $key,
                ],
                [
                    'dimension_name'   => $name,
                    'raw_score'        => $rawScore,
                    'normalized_score' => $normalized,
                    'level'            => $level,
                    'interpretation'   => self::INTERPRETATIONS[$key][$level] ?? null,
                ]
            );
        }
    }

    private function levelFromScore(float $score): string
    {
        return match (true) {
            $score >= 80 => 'muy_alto',
            $score >= 60 => 'alto',
            $score >= 40 => 'medio',
            $score >= 20 => 'bajo',
            default      => 'muy_bajo',
        };
    }
}
