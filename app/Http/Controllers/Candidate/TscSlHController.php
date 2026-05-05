<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Consent;
use App\Models\TestAssignment;
use App\Models\TscSlSession;
use App\Services\TscSlHScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TscSlHController extends Controller
{
    public function __construct(private TscSlHScoringService $scorer) {}

    /** Instrucciones + consentimiento */
    public function start(TestAssignment $assignment): View|RedirectResponse
    {
        $candidate = $this->resolveCandidate($assignment);
        if (!$candidate) return redirect()->route('candidate.access');

        $session = TscSlSession::firstOrCreate(
            ['candidate_id' => $candidate->id, 'assignment_id' => $assignment->id],
            ['status' => 'pending', 'started_at' => now()]
        );

        if (in_array($session->status, ['completed','m3_submitted'])) {
            return redirect()->route('candidate.tsc-sl-h.complete', $assignment);
        }
        if ($session->status === 'm2_done') {
            return redirect()->route('candidate.tsc-sl-h.module3', $assignment);
        }
        if ($session->status === 'm1_done') {
            return redirect()->route('candidate.tsc-sl-h.module2', $assignment);
        }

        return view('candidate.tsc-sl-h.start', compact('candidate', 'assignment', 'session'));
    }

    /** Módulo 1 — SJT (20 ítems) */
    public function module1(TestAssignment $assignment): View|RedirectResponse
    {
        $candidate = $this->resolveCandidate($assignment);
        if (!$candidate) return redirect()->route('candidate.access');

        $session = $this->getSession($candidate, $assignment);
        if (!$session || in_array($session->status, ['completed','m3_submitted'])) {
            return redirect()->route('candidate.tsc-sl-h.complete', $assignment);
        }
        if ($session->status === 'm2_done') {
            return redirect()->route('candidate.tsc-sl-h.module3', $assignment);
        }
        if ($session->status === 'm1_done') {
            return redirect()->route('candidate.tsc-sl-h.module2', $assignment);
        }

        return view('candidate.tsc-sl-h.module1', compact('candidate', 'assignment', 'session'));
    }

    /** Guardar Módulo 1 y auto-calificar */
    public function storeModule1(Request $request, TestAssignment $assignment): RedirectResponse
    {
        $candidate = $this->resolveCandidate($assignment);
        if (!$candidate) return redirect()->route('candidate.access');

        $session = $this->getSession($candidate, $assignment);
        if (!$session || $session->status !== 'pending') {
            return redirect()->route('candidate.tsc-sl-h.start', $assignment);
        }

        $validated = $request->validate([
            'm1'    => 'required|array|min:20',
            'm1.*'  => 'required|in:A,B,C,D',
        ]);

        $answers = [];
        foreach (range(1, 20) as $i) {
            $answers[(string)$i] = strtoupper($validated['m1'][(string)$i] ?? 'A');
        }

        $scored = $this->scorer->scoreM1($answers);

        $session->update([
            'status'          => 'm1_done',
            'm1_answers'      => $answers,
            'm1_score'        => $scored['total'],
            'm1_completed_at' => now(),
        ]);

        return redirect()->route('candidate.tsc-sl-h.module2', $assignment);
    }

    /** Módulo 2 — Likert (40 ítems) */
    public function module2(TestAssignment $assignment): View|RedirectResponse
    {
        $candidate = $this->resolveCandidate($assignment);
        if (!$candidate) return redirect()->route('candidate.access');

        $session = $this->getSession($candidate, $assignment);
        if (!$session) return redirect()->route('candidate.tsc-sl-h.start', $assignment);
        if (in_array($session->status, ['completed','m3_submitted'])) {
            return redirect()->route('candidate.tsc-sl-h.complete', $assignment);
        }
        if ($session->status === 'm2_done') {
            return redirect()->route('candidate.tsc-sl-h.module3', $assignment);
        }
        if ($session->status === 'pending') {
            return redirect()->route('candidate.tsc-sl-h.module1', $assignment);
        }

        return view('candidate.tsc-sl-h.module2', compact('candidate', 'assignment', 'session'));
    }

    /** Guardar Módulo 2 */
    public function storeModule2(Request $request, TestAssignment $assignment): RedirectResponse
    {
        $candidate = $this->resolveCandidate($assignment);
        if (!$candidate) return redirect()->route('candidate.access');

        $session = $this->getSession($candidate, $assignment);
        if (!$session || $session->status !== 'm1_done') {
            return redirect()->route('candidate.tsc-sl-h.start', $assignment);
        }

        $validated = $request->validate([
            'm2'   => 'required|array|min:40',
            'm2.*' => 'required|integer|between:1,5',
        ]);

        $answers = [];
        foreach (range(21, 60) as $i) {
            $answers[(string)$i] = (int) ($validated['m2'][(string)$i] ?? 3);
        }

        $scored = $this->scorer->scoreM2($answers);

        $session->update([
            'status'          => 'm2_done',
            'm2_answers'      => $answers,
            'm2_score'        => $scored['total'],
            'm2_completed_at' => now(),
        ]);

        return redirect()->route('candidate.tsc-sl-h.module3', $assignment);
    }

    /** Módulo 3 — Escenarios abiertos */
    public function module3(TestAssignment $assignment): View|RedirectResponse
    {
        $candidate = $this->resolveCandidate($assignment);
        if (!$candidate) return redirect()->route('candidate.access');

        $session = $this->getSession($candidate, $assignment);
        if (!$session) return redirect()->route('candidate.tsc-sl-h.start', $assignment);
        if (in_array($session->status, ['completed','m3_submitted'])) {
            return redirect()->route('candidate.tsc-sl-h.complete', $assignment);
        }
        if ($session->status === 'm1_done') {
            return redirect()->route('candidate.tsc-sl-h.module2', $assignment);
        }
        if ($session->status === 'pending') {
            return redirect()->route('candidate.tsc-sl-h.module1', $assignment);
        }

        return view('candidate.tsc-sl-h.module3', compact('candidate', 'assignment', 'session'));
    }

    /** Guardar Módulo 3 */
    public function storeModule3(Request $request, TestAssignment $assignment): RedirectResponse
    {
        $candidate = $this->resolveCandidate($assignment);
        if (!$candidate) return redirect()->route('candidate.access');

        $session = $this->getSession($candidate, $assignment);
        if (!$session || $session->status !== 'm2_done') {
            return redirect()->route('candidate.tsc-sl-h.start', $assignment);
        }

        $validated = $request->validate([
            'm3'   => 'required|array|size:3',
            'm3.1' => 'required|string|min:50|max:3000',
            'm3.2' => 'required|string|min:50|max:3000',
            'm3.3' => 'required|string|min:50|max:3000',
        ], [
            'm3.1.required' => 'La respuesta al Escenario 1 es obligatoria.',
            'm3.1.min'      => 'La respuesta al Escenario 1 debe tener al menos 50 caracteres.',
            'm3.1.max'      => 'La respuesta al Escenario 1 no puede superar los 3000 caracteres.',
            'm3.2.required' => 'La respuesta al Escenario 2 es obligatoria.',
            'm3.2.min'      => 'La respuesta al Escenario 2 debe tener al menos 50 caracteres.',
            'm3.2.max'      => 'La respuesta al Escenario 2 no puede superar los 3000 caracteres.',
            'm3.3.required' => 'La respuesta al Escenario 3 es obligatoria.',
            'm3.3.min'      => 'La respuesta al Escenario 3 debe tener al menos 50 caracteres.',
            'm3.3.max'      => 'La respuesta al Escenario 3 no puede superar los 3000 caracteres.',
            'm3.required'   => 'Debe responder los tres escenarios.',
            'm3.size'       => 'Debe responder los tres escenarios.',
        ]);

        $session->update([
            'status'          => 'm3_submitted',
            'm3_responses'    => $validated['m3'],
            'm3_submitted_at' => now(),
        ]);

        $assignment->update(['status' => 'completed', 'completed_at' => now()]);

        return redirect()->route('candidate.tsc-sl-h.complete', $assignment);
    }

    /** Pantalla de confirmación */
    public function complete(TestAssignment $assignment): View|RedirectResponse
    {
        $candidate = $this->resolveCandidate($assignment);
        if (!$candidate) return redirect()->route('candidate.access');

        $session = $this->getSession($candidate, $assignment);
        if (!$session || $session->status === 'pending') {
            return redirect()->route('candidate.tsc-sl-h.start', $assignment);
        }

        return view('candidate.tsc-sl-h.complete', compact('candidate', 'assignment', 'session'));
    }

    /** Registrar consentimiento y redirigir al Módulo 1 */
    public function storeConsent(Request $request, TestAssignment $assignment): RedirectResponse
    {
        $candidate = $this->resolveCandidate($assignment);
        if (!$candidate) return redirect()->route('candidate.access');

        Consent::firstOrCreate(
            ['candidate_id' => $candidate->id, 'assignment_id' => $assignment->id, 'test_type' => 'tsc_sl_h'],
            [
                'consent_version' => '1.0',
                'ip_address'      => $request->ip(),
                'user_agent'      => substr($request->userAgent() ?? '', 0, 500),
                'consented_at'    => now(),
            ]
        );

        return redirect()->route('candidate.tsc-sl-h.module1', $assignment);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function resolveCandidate(TestAssignment $assignment): ?Candidate
    {
        $candidateId = session('candidate_id');
        if (!$candidateId) return null;

        $candidate = Candidate::find($candidateId);
        if (!$candidate || $candidate->id !== $assignment->candidate_id) return null;

        return $candidate;
    }

    private function getSession(Candidate $candidate, TestAssignment $assignment): ?TscSlSession
    {
        return TscSlSession::where('candidate_id', $candidate->id)
            ->where('assignment_id', $assignment->id)
            ->first();
    }
}
