<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\EvaluatorAssessment;
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

        return view('admin.assessments.form', compact('candidate', 'type', 'existing'));
    }

    /** Guardar evaluación */
    public function store(Request $request, Candidate $candidate): RedirectResponse
    {
        $type = $request->input('assessment_type');

        $validated = $request->validate([
            'assessment_type' => 'required|in:wartegg,star_interview,assessment_center',
            'scores'          => 'required|array',
            'observations'    => 'nullable|string|max:3000',
        ]);

        // Calcular puntuación global como promedio de los scores numéricos
        $numericScores = array_filter($validated['scores'], 'is_numeric');
        $overall = count($numericScores) > 0
            ? round(array_sum($numericScores) / count($numericScores) * 20, 2) // Escala 1–5 → 0–100
            : null;

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

        return redirect()
            ->route('admin.candidates.show', $candidate)
            ->with('success', 'Evaluación clínica guardada correctamente.');
    }

    /** Editar evaluación existente */
    public function edit(EvaluatorAssessment $assessment): View
    {
        $candidate = $assessment->candidate;
        $type      = $assessment->assessment_type;
        $existing  = $assessment;

        return view('admin.assessments.form', compact('candidate', 'type', 'existing'));
    }

    /** Actualizar evaluación */
    public function update(Request $request, EvaluatorAssessment $assessment): RedirectResponse
    {
        $validated = $request->validate([
            'scores'       => 'required|array',
            'observations' => 'nullable|string|max:3000',
        ]);

        $numericScores = array_filter($validated['scores'], 'is_numeric');
        $overall = count($numericScores) > 0
            ? round(array_sum($numericScores) / count($numericScores) * 20, 2)
            : null;

        $assessment->update([
            'scores'        => $validated['scores'],
            'overall_score' => $overall,
            'observations'  => $validated['observations'],
            'completed_at'  => now(),
        ]);

        return redirect()
            ->route('admin.candidates.show', $assessment->candidate)
            ->with('success', 'Evaluación actualizada correctamente.');
    }
}
