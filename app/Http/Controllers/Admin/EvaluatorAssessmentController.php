<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\EvaluatorAssessment;
use App\Models\WarteggSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EvaluatorAssessmentController extends Controller
{
    /** Selección del tipo de evaluación clínica a realizar */
    public function select(Candidate $candidate): View
    {
        $candidate->load(['position', 'evaluatorAssessments']);

        $assessments = [
            'wartegg'           => $candidate->evaluatorAssessments->firstWhere('assessment_type', 'wartegg'),
            'star_interview'    => $candidate->evaluatorAssessments->firstWhere('assessment_type', 'star_interview'),
            'assessment_center' => $candidate->evaluatorAssessments->firstWhere('assessment_type', 'assessment_center'),
        ];

        return view('admin.assessments.select', compact('candidate', 'assessments'));
    }

    /** Formulario para crear/editar evaluación clínica */
    public function create(Request $request, Candidate $candidate): View
    {
        $type = $request->query('type', 'wartegg');

        $existing = EvaluatorAssessment::where('candidate_id', $candidate->id)
            ->where('assessment_type', $type)
            ->latest()
            ->first();

        $warteggSession = $type === 'wartegg'
            ? WarteggSession::where('candidate_id', $candidate->id)
                ->where('status', 'completed')
                ->latest()
                ->first()
            : null;

        $candidateContext = $type === 'wartegg'
            ? $this->loadCandidateContext($candidate)
            : null;

        return view('admin.assessments.form', compact('candidate', 'type', 'existing', 'warteggSession', 'candidateContext'));
    }

    /** Guardar evaluación */
    public function store(Request $request, Candidate $candidate): RedirectResponse
    {
        $type = $request->input('assessment_type');

        $validated = $request->validate([
            'assessment_type' => 'required|in:wartegg,star_interview,assessment_center',
            'scores'          => 'required|array',
            'observations'    => 'nullable|string|max:5000',
        ]);

        $overall = $this->computeOverall($type, $validated['scores']);

        EvaluatorAssessment::create([
            'candidate_id'    => $candidate->id,
            'evaluator_id'    => Auth::id(),
            'assessment_type' => $validated['assessment_type'],
            'scores'          => $validated['scores'],
            'overall_score'   => $overall,
            'observations'    => $validated['observations'],
            'status'          => 'completed',
            'completed_at'    => now(),
        ]);

        $back = $request->query('back') === 'select'
            ? redirect()->route('admin.assessments.select', $candidate)
            : redirect()->route('admin.candidates.show', $candidate);

        return $back->with('success', 'Evaluación clínica guardada correctamente.');
    }

    /** Editar evaluación existente */
    public function edit(EvaluatorAssessment $assessment): View
    {
        $candidate = $assessment->candidate;
        $type      = $assessment->assessment_type;
        $existing  = $assessment;
        $existing->load('evaluator');

        $warteggSession = $type === 'wartegg'
            ? WarteggSession::where('candidate_id', $candidate->id)
                ->where('status', 'completed')
                ->latest()
                ->first()
            : null;

        $candidateContext = $type === 'wartegg'
            ? $this->loadCandidateContext($candidate)
            : null;

        return view('admin.assessments.form', compact('candidate', 'type', 'existing', 'warteggSession', 'candidateContext'));
    }

    /** Actualizar evaluación */
    public function update(Request $request, EvaluatorAssessment $assessment): RedirectResponse
    {
        $validated = $request->validate([
            'scores'       => 'required|array',
            'observations' => 'nullable|string|max:5000',
        ]);

        $overall = $this->computeOverall($assessment->assessment_type, $validated['scores']);

        $assessment->update([
            'scores'        => $validated['scores'],
            'overall_score' => $overall,
            'observations'  => $validated['observations'],
            'completed_at'  => now(),
        ]);

        $back = $request->query('back') === 'select'
            ? redirect()->route('admin.assessments.select', $assessment->candidate)
            : redirect()->route('admin.candidates.show', $assessment->candidate);

        return $back->with('success', 'Evaluación actualizada correctamente.');
    }

    /** Carga el contexto de otras pruebas del candidato para el panel de integración Wartegg. */
    private function loadCandidateContext(Candidate $candidate): array
    {
        $candidate->load([
            'assignments.test',
            'assignments.result',
            'evaluatorAssessments' => fn($q) => $q->where('status', 'completed'),
        ]);

        $completedTests = $candidate->assignments
            ->filter(fn($a) => $a->status === 'completed' && $a->result && !$a->test->evaluator_scored)
            ->map(fn($a) => [
                'name'         => $a->test->name,
                'test_type'    => $a->test->test_type,
                'score'        => $a->result->total_score,
                'max_score'    => $a->result->max_score,
                'percentage'   => $a->result->percentage,
                'passed'       => $a->result->passed,
                'completed_at' => $a->completed_at,
            ])->values();

        $otherAssessments = $candidate->evaluatorAssessments
            ->where('assessment_type', '!=', 'wartegg')
            ->map(fn($ea) => [
                'type'          => $ea->assessment_type,
                'type_label'    => $ea->typeLabel(),
                'overall_score' => $ea->overall_score,
                'completed_at'  => $ea->completed_at,
            ])->values();

        return compact('completedTests', 'otherAssessments');
    }

    /**
     * Calcula overall_score (0–100) según el tipo de evaluación.
     * Wartegg: promedio de las 8 dimensiones organizacionales (org_*).
     * Otros:   promedio de todos los valores numéricos.
     */
    private function computeOverall(string $type, array $scores): ?float
    {
        if ($type === 'wartegg') {
            $orgKeys = ['org_autoconcepto','org_gestion_emocional','org_logro','org_autoridad',
                        'org_energia','org_analitico','org_social','org_adaptabilidad'];
            $orgScores = array_filter(
                array_intersect_key($scores, array_flip($orgKeys)),
                'is_numeric'
            );
            if (count($orgScores) > 0) {
                return round(array_sum($orgScores) / count($orgScores) * 20, 2);
            }
            // fallback: promedio de scores de caja si no hay dims org
        }

        if ($type === 'star_interview') {
            $starKeys = ['L1','L2','R1','R2','R3','D1','D2','D3','P1','P2'];
            $starScores = array_filter(
                array_intersect_key($scores, array_flip($starKeys)),
                'is_numeric'
            );
            if (count($starScores) > 0) {
                return round(array_sum($starScores) / count($starScores) * 20, 2);
            }
        }

        $numeric = array_filter($scores, 'is_numeric');
        return count($numeric) > 0
            ? round(array_sum($numeric) / count($numeric) * 20, 2)
            : null;
    }
}
