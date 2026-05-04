<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Consent;
use App\Models\TestAssignment;
use App\Models\WarteggSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarteggController extends Controller
{
    private function candidate(): ?Candidate
    {
        $id = session('candidate_id');
        return $id ? Candidate::find($id) : null;
    }

    public function start(TestAssignment $assignment): View|RedirectResponse
    {
        $candidate = $this->candidate();
        if (!$candidate || $assignment->candidate_id !== $candidate->id) {
            return redirect()->route('candidate.access');
        }
        if ($assignment->test->test_type !== 'wartegg') {
            return redirect()->route('candidate.dashboard');
        }

        $session = WarteggSession::firstOrCreate(
            ['candidate_id' => $candidate->id, 'assignment_id' => $assignment->id],
            ['status' => 'pending']
        );

        if ($session->status === 'completed') {
            return redirect()->route('candidate.wartegg.complete', $assignment);
        }

        return view('candidate.wartegg.start', compact('candidate', 'assignment', 'session'));
    }

    public function storeConsent(Request $request, TestAssignment $assignment): RedirectResponse
    {
        $candidate = $this->candidate();
        if (!$candidate || $assignment->candidate_id !== $candidate->id) {
            return redirect()->route('candidate.access');
        }

        Consent::firstOrCreate(
            ['candidate_id' => $candidate->id, 'assignment_id' => $assignment->id, 'test_type' => 'wartegg'],
            [
                'consent_version' => '1.0',
                'ip_address'      => $request->ip(),
                'user_agent'      => substr($request->userAgent() ?? '', 0, 500),
                'consented_at'    => now(),
            ]
        );

        return redirect()->route('candidate.wartegg.draw', $assignment);
    }

    public function draw(TestAssignment $assignment): View|RedirectResponse
    {
        $candidate = $this->candidate();
        if (!$candidate || $assignment->candidate_id !== $candidate->id) {
            return redirect()->route('candidate.access');
        }

        $session = WarteggSession::where('candidate_id', $candidate->id)
            ->where('assignment_id', $assignment->id)
            ->first();

        if (!$session) {
            return redirect()->route('candidate.wartegg.start', $assignment);
        }

        if ($session->status === 'completed') {
            return redirect()->route('candidate.wartegg.complete', $assignment);
        }

        if ($session->status === 'pending') {
            $session->update(['status' => 'in_progress', 'started_at' => now()]);
            $assignment->update(['status' => 'in_progress', 'started_at' => now()]);
        }

        return view('candidate.wartegg.draw', compact('candidate', 'assignment', 'session'));
    }

    public function saveBox(Request $request, TestAssignment $assignment): JsonResponse
    {
        $candidate = $this->candidate();
        if (!$candidate || $assignment->candidate_id !== $candidate->id) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $validated = $request->validate([
            'box_number'   => 'required|integer|min:1|max:8',
            'drawing_data' => 'required|string',
            'title'        => 'nullable|string|max:120',
            'order'        => 'nullable|integer|min:1|max:8',
            'time_seconds' => 'nullable|integer|min:0',
        ]);

        $session = WarteggSession::where('candidate_id', $candidate->id)
            ->where('assignment_id', $assignment->id)
            ->first();

        if (!$session || $session->status === 'completed') {
            return response()->json(['error' => 'Sesión no disponible'], 422);
        }

        $boxes = collect($session->boxes ?? []);
        $idx   = $boxes->search(fn($b) => $b['number'] === $validated['box_number']);

        $boxData = [
            'number'       => $validated['box_number'],
            'drawing_data' => $validated['drawing_data'],
            'title'        => $validated['title'] ?? '',
            'order'        => $validated['order'],
            'time_seconds' => $validated['time_seconds'] ?? 0,
        ];

        if ($idx !== false) {
            $boxes[$idx] = $boxData;
        } else {
            $boxes->push($boxData);
        }

        $session->update(['boxes' => $boxes->values()->all()]);

        return response()->json([
            'success'    => true,
            'boxes_done' => $session->fresh()->completedBoxesCount(),
        ]);
    }

    public function finish(Request $request, TestAssignment $assignment): RedirectResponse
    {
        $candidate = $this->candidate();
        if (!$candidate || $assignment->candidate_id !== $candidate->id) {
            return redirect()->route('candidate.access');
        }

        $session = WarteggSession::where('candidate_id', $candidate->id)
            ->where('assignment_id', $assignment->id)
            ->first();

        if (!$session) {
            return redirect()->route('candidate.wartegg.start', $assignment);
        }

        $totalSecs = $session->started_at
            ? max(0, (int) now()->diffInSeconds($session->started_at, true))
            : null;

        $session->update([
            'status'        => 'completed',
            'completed_at'  => now(),
            'total_seconds' => $totalSecs,
        ]);

        $assignment->update(['status' => 'completed', 'completed_at' => now()]);

        return redirect()->route('candidate.wartegg.complete', $assignment);
    }

    public function complete(TestAssignment $assignment): View|RedirectResponse
    {
        $candidate = $this->candidate();
        if (!$candidate || $assignment->candidate_id !== $candidate->id) {
            return redirect()->route('candidate.access');
        }

        $session = WarteggSession::where('candidate_id', $candidate->id)
            ->where('assignment_id', $assignment->id)
            ->first();

        return view('candidate.wartegg.complete', compact('candidate', 'assignment', 'session'));
    }
}
