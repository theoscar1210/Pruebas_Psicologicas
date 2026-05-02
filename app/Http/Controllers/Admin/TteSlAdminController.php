<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TteSlSession;
use App\Services\TteSlScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TteSlAdminController extends Controller
{
    public function __construct(private TteSlScoringService $scorer) {}

    public function score(TteSlSession $session): View|RedirectResponse
    {
        if ($session->status === 'completed') {
            return back()->with('info', 'Esta sesión ya está calificada.');
        }

        if ($session->status !== 'm3_submitted') {
            return back()->with('error', 'El candidato aún no ha enviado el Módulo 3.');
        }

        $session->load(['candidate.position', 'm3Evaluator']);

        return view('admin.tte-sl.score', compact('session'));
    }

    public function storeScore(Request $request, TteSlSession $session): RedirectResponse
    {
        if ($session->status === 'completed') {
            return back()->with('error', 'Esta sesión ya está calificada.');
        }

        $validated = $request->validate([
            'scores'       => 'required|array|size:3',
            'scores.1'     => 'required|integer|between:1,5',
            'scores.2'     => 'required|integer|between:1,5',
            'scores.3'     => 'required|integer|between:1,5',
            'just.1'       => 'required|string|max:1000',
            'just.2'       => 'required|string|max:1000',
            'just.3'       => 'required|string|max:1000',
            'observations' => 'nullable|string|max:3000',
        ]);

        $m3ScoresRaw = [
            '1'      => (int) $validated['scores']['1'],
            '2'      => (int) $validated['scores']['2'],
            '3'      => (int) $validated['scores']['3'],
            'just_1' => $validated['just']['1'],
            'just_2' => $validated['just']['2'],
            'just_3' => $validated['just']['3'],
        ];

        $final = $this->scorer->computeFinal(
            $session->m1_answers ?? [],
            $session->m2_answers ?? [],
            $m3ScoresRaw
        );

        $session->update([
            'status'            => 'completed',
            'm3_scores'         => $m3ScoresRaw,
            'm3_score'          => $final['m3_score'],
            'm3_evaluator_id'   => Auth::id(),
            'total_score'       => $final['total_score'],
            'dimension_scores'  => $final['dimension_scores'],
            'performance_level' => $final['performance_level'],
            'completed_at'      => now(),
        ]);

        return redirect()
            ->route('admin.candidates.show', $session->candidate_id)
            ->with('success', 'Calificación TTE-SL guardada correctamente.');
    }

    public function results(TteSlSession $session): View
    {
        $session->load(['candidate.position', 'm3Evaluator']);
        return view('admin.tte-sl.results', compact('session'));
    }
}
