<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CandidatesResultsExport;
use App\Exports\RankingExport;
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Position;
use App\Models\TestAssignment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Pantalla principal de reportes.
     */
    public function index(): View
    {
        $positions = Position::where('is_active', true)
            ->withCount('candidates')
            ->orderBy('name')
            ->get();

        $stats = [
            'total_candidates' => Candidate::count(),
            'completed'        => TestAssignment::where('status', 'completed')->count(),
            'passed'           => TestAssignment::whereHas('result', fn ($q) => $q->where('passed', true))->count(),
            'failed'           => TestAssignment::whereHas('result', fn ($q) => $q->where('passed', false))->count(),
        ];

        return view('reports.index', compact('positions', 'stats'));
    }

    /**
     * PDF con el reporte individual de un candidato.
     */
    public function candidatePdf(Candidate $candidate): Response
    {
        $this->authorize('view', $candidate);

        Log::info('PDF candidato descargado', [
            'by'           => auth()->id(),
            'candidate_id' => $candidate->id,
        ]);

        $candidate->load([
            'position',
            'assignments.test',
            'assignments.result',
            'assignments.answers.question',
            'assignments.answers.option',
        ]);

        $pdf = Pdf::loadView('reports.candidate-pdf', compact('candidate'))
            ->setPaper('a4', 'portrait');

        $pdf->render();
        $cpdf = $pdf->getDomPDF()->getCanvas()->get_cpdf();
        $cpdf->addInfo('Producer', config('app.name'));
        $cpdf->addInfo('Creator', config('app.name'));

        $filename = 'reporte-' . str($candidate->name)->slug() . '.pdf';

        return response($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * PDF con el ranking de candidatos por cargo.
     */
    public function rankingPdf(Request $request): Response
    {
        $this->authorize('viewAny', Candidate::class);

        $request->validate([
            'position_id' => 'required|exists:positions,id',
        ]);

        $position = Position::with('tests')->findOrFail($request->position_id);

        $candidates = Candidate::where('position_id', $position->id)
            ->with([
                'assignments' => fn ($q) => $q->where('status', 'completed')->with('result', 'test'),
            ])
            ->get()
            ->map(function (Candidate $candidate) {
                $totalPct    = $candidate->assignments->avg(fn ($a) => $a->result?->percentage ?? 0);
                $passedCount = $candidate->assignments->filter(fn ($a) => $a->result?->passed)->count();
                $totalTests  = $candidate->assignments->count();

                return [
                    'candidate'   => $candidate,
                    'avg_pct'     => round($totalPct, 1),
                    'passed'      => $passedCount,
                    'total_tests' => $totalTests,
                    'all_passed'  => $passedCount === $totalTests && $totalTests > 0,
                ];
            })
            ->sortByDesc('avg_pct')
            ->values();

        $pdf = Pdf::loadView('reports.ranking-pdf', compact('position', 'candidates'))
            ->setPaper('a4', 'portrait');

        $pdf->render();
        $cpdf = $pdf->getDomPDF()->getCanvas()->get_cpdf();
        $cpdf->addInfo('Producer', config('app.name'));
        $cpdf->addInfo('Creator', config('app.name'));

        $filename = 'ranking-' . str($position->name)->slug() . '.pdf';

        return response($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Excel con todos los resultados de candidatos.
     */
    public function exportExcel(Request $request): BinaryFileResponse
    {
        $validated = $request->validate([
            'position_id' => 'nullable|integer|exists:positions,id',
        ]);

        Log::info('Bulk Excel export', [
            'by'          => auth()->id(),
            'position_id' => $validated['position_id'] ?? 'all',
        ]);

        return Excel::download(
            new CandidatesResultsExport($validated['position_id'] ?? null),
            'resultados-candidatos-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Excel con el ranking de un cargo específico.
     */
    public function exportRankingExcel(Request $request): BinaryFileResponse
    {
        $request->validate([
            'position_id' => 'required|exists:positions,id',
        ]);

        return Excel::download(
            new RankingExport($request->position_id),
            'ranking-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
