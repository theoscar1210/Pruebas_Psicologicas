<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\TestAssignment;
use App\Models\TestResult;
use Illuminate\Support\Carbon;

class TestScoringService
{
    /**
     * Calcula y persiste el resultado de una asignación de prueba.
     * Se llama cuando el candidato termina la prueba.
     */
    public function calculate(TestAssignment $assignment): TestResult
    {
        $assignment->load(['test.questions.options', 'answers.option']);

        $totalScore = 0;
        $maxScore = 0;

        foreach ($assignment->test->questions as $question) {
            $maxScore += $question->points;

            $answer = $assignment->answers->firstWhere('question_id', $question->id);

            if (!$answer) {
                continue;
            }

            $score = match ($question->type) {
                'multiple_choice', 'likert' => $this->scoreOptionAnswer($answer, $question),
                'open' => 0, // Las preguntas abiertas requieren evaluación manual
                default => 0,
            };

            $answer->update(['score' => $score]);
            $totalScore += $score;
        }

        $percentage = $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0;
        $passed = $percentage >= $assignment->test->passing_score;

        $result = TestResult::updateOrCreate(
            ['test_assignment_id' => $assignment->id],
            [
                'total_score' => $totalScore,
                'max_score' => $maxScore,
                'percentage' => $percentage,
                'passed' => $passed,
                'calculated_at' => Carbon::now(),
            ]
        );

        $assignment->update([
            'status' => 'completed',
            'completed_at' => Carbon::now(),
        ]);

        return $result;
    }

    private function scoreOptionAnswer(Answer $answer, $question): float
    {
        if (!$answer->question_option_id) {
            return 0;
        }

        $option = $question->options->firstWhere('id', $answer->question_option_id);
        return $option ? (float) $option->value : 0;
    }
}
