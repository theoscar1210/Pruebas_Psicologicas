<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\PsychologicalReport;
use App\Services\PsychologicalReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PsychologicalReportController extends Controller
{
    public function __construct(
        private readonly PsychologicalReportService $reportService
    ) {}

    /** Mostrar el perfil psicológico de un candidato */
    public function show(Candidate $candidate): View
    {
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
        $report = $this->reportService->generate($candidate);

        return redirect()
            ->route('admin.profile.show', $candidate)
            ->with('success', 'Perfil psicológico generado. Revisa y completa la evaluación.');
    }

    /** Guardar conclusiones finales del evaluador */
    public function complete(Request $request, Candidate $candidate): RedirectResponse
    {
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

    /** Exportar el perfil como PDF */
    public function pdf(Candidate $candidate): \Illuminate\Http\Response
    {
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
