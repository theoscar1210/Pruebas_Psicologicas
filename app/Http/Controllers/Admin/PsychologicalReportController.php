<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Services\AiNarrativeService;
use App\Services\PsychologicalReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PsychologicalReportController extends Controller
{
    public function __construct(
        private readonly PsychologicalReportService $reportService
    ) {}

    /** Mostrar el perfil psicológico de un candidato */
    public function show(Candidate $candidate): View
    {
        $this->authorize('viewReport', $candidate);

        $candidate->load([
            'position',
            'assignments.test',
            'assignments.dimensionScores',
            'assignments.result',
            'evaluatorAssessments.evaluator',
        ]);

        $report = $candidate->psychologicalReports()
            ->with('evaluator')
            ->latest()
            ->first();

        return view('admin.profile.show', compact('candidate', 'report'));
    }

    /** Generar / actualizar el reporte automáticamente y mostrar formulario de cierre */
    public function generate(Candidate $candidate): RedirectResponse
    {
        $this->authorize('viewReport', $candidate);

        $this->reportService->generate($candidate);

        return redirect()
            ->route('admin.profile.show', $candidate)
            ->with('success', 'Perfil psicológico generado. Revisa y completa la evaluación.');
    }

    /** Guardar conclusiones finales del evaluador */
    public function complete(Request $request, Candidate $candidate): RedirectResponse
    {
        $this->authorize('viewReport', $candidate);

        $validated = $request->validate([
            'recommendation'       => 'required|in:apto,apto_con_reservas,no_apto',
            'recommendation_notes' => 'nullable|string|max:3000',
            'summary'              => 'nullable|string|max:5000',
            'adjustment_level'     => 'required|in:alto,medio,bajo',
        ]);

        $report = $candidate->psychologicalReports()->latest()->first();

        if (!$report) {
            $report = $this->reportService->generate($candidate);
        }

        $this->reportService->complete($report, $validated);

        return redirect()
            ->route('admin.profile.show', $candidate)
            ->with('success', 'Perfil completado y recomendación guardada.');
    }

    /** Generar narrativa IA para una sección del informe */
    public function generateNarrative(Request $request, Candidate $candidate): JsonResponse
    {
        $this->authorize('viewReport', $candidate);

        $section = $request->validate([
            'section' => 'required|in:personality,cognitive,competencies,projective,interview',
        ])['section'];

        $report = $candidate->psychologicalReports()->latest()->first();

        if (!$report) {
            return response()->json(['error' => 'Genera primero el perfil psicológico.'], 422);
        }

        try {
            $text = app(AiNarrativeService::class)->generate($report, $candidate, $section);

            $field = "narrative_{$section}";
            $report->update([$field => $text]);

            return response()->json(['text' => $text]);
        } catch (\Throwable $e) {
            Log::error('AI narrative generation failed', [
                'candidate_id' => $candidate->id,
                'section'      => $section,
                'error'        => $e->getMessage(),
            ]);
            return response()->json(['error' => 'No se pudo generar la narrativa. Intenta de nuevo más tarde.'], 500);
        }
    }

    /** Exportar el perfil como PDF */
    public function pdf(Candidate $candidate): \Illuminate\Http\Response
    {
        $this->authorize('viewReport', $candidate);

        $candidate->load([
            'position',
            'assignments.test',
            'assignments.dimensionScores',
            'assignments.result',
            'evaluatorAssessments.evaluator',
        ]);

        $report = $candidate->psychologicalReports()
            ->with('evaluator')
            ->latest()
            ->first();

        $pdf = Pdf::loadView('admin.profile.pdf', compact('candidate', 'report'))
            ->setPaper('a4', 'portrait');

        $filename = 'perfil-psicologico-' . str($candidate->name)->slug() . '.pdf';

        return $pdf->download($filename);
    }
}
