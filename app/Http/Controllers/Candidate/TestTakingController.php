<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Candidate;
use App\Models\Consent;
use App\Models\TestAssignment;
use App\Services\TestScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TestTakingController extends Controller
{
    public function __construct(private TestScoringService $scoringService) {}

    /**
     * Pantalla de acceso del candidato (login por código único).
     */
    public function accessForm(): View
    {
        return view('candidate.access');
    }

    private const MAX_FAILED_ATTEMPTS = 5;
    private const BLOCK_MINUTES       = 30;

    public function access(Request $request): RedirectResponse
    {
        $ip       = $request->ip();
        $blockKey = 'access_block_' . md5($ip);
        $failKey  = 'access_fails_' . md5($ip);

        // Verificar bloqueo activo
        if ($blockedUntil = Cache::get($blockKey)) {
            $remaining = (int) ceil(($blockedUntil - now()->timestamp) / 60);
            return back()->withErrors([
                'access_code' => "Demasiados intentos fallidos. Intenta de nuevo en {$remaining} minuto(s).",
            ]);
        }

        $request->validate([
            'access_code' => 'required|string|size:8',
        ]);

        $candidate = Candidate::where('access_code', strtoupper($request->access_code))
            ->where('status', 'active')
            ->first();

        if (!$candidate) {
            $fails = (int) Cache::get($failKey, 0) + 1;
            Cache::put($failKey, $fails, now()->addMinutes(self::BLOCK_MINUTES));

            Log::warning('Candidate access failed', [
                'ip'       => $ip,
                'code'     => strtoupper($request->access_code),
                'attempts' => $fails,
            ]);

            if ($fails >= self::MAX_FAILED_ATTEMPTS) {
                $until = now()->addMinutes(self::BLOCK_MINUTES)->timestamp;
                Cache::put($blockKey, $until, now()->addMinutes(self::BLOCK_MINUTES));
                Cache::forget($failKey);
                Log::warning('IP blocked for brute-force on access_code', ['ip' => $ip]);
                return back()->withErrors([
                    'access_code' => 'Demasiados intentos fallidos. IP bloqueada por ' . self::BLOCK_MINUTES . ' minutos.',
                ]);
            }

            return back()->withErrors(['access_code' => 'Código de acceso inválido o inactivo.']);
        }

        if ($candidate->access_code_expires_at !== null && $candidate->access_code_expires_at->isPast()) {
            Log::warning('Candidate access with expired code', [
                'ip'           => $ip,
                'candidate_id' => $candidate->id,
                'expired_at'   => $candidate->access_code_expires_at,
            ]);
            return back()->withErrors(['access_code' => 'Tu código de acceso ha expirado. Solicita uno nuevo al evaluador.']);
        }

        // Login exitoso: limpiar contadores de fallo
        Cache::forget($failKey);
        Cache::forget($blockKey);

        session(['candidate_id' => $candidate->id]);

        return redirect()->route('candidate.dashboard');
    }

    public function dashboard(): View|RedirectResponse
    {
        $candidate = $this->getSessionCandidate();
        if (!$candidate) {
            return redirect()->route('candidate.access');
        }

        $candidate->load(['position', 'assignments.test', 'assignments.result', 'evaluatorAssessments', 'warteggSessions', 'tscSlSessions', 'tteSlSessions']);

        return view('candidate.dashboard', compact('candidate'));
    }

    public function consentForm(TestAssignment $assignment): View|RedirectResponse
    {
        $candidate = $this->getSessionCandidate();
        if (!$candidate || $assignment->candidate_id !== $candidate->id) {
            return redirect()->route('candidate.access');
        }

        $assignment->load(['test.questions']);
        return view('candidate.consent', compact('candidate', 'assignment'));
    }

    public function storeConsent(Request $request, TestAssignment $assignment): RedirectResponse
    {
        $candidate = $this->getSessionCandidate();
        if (!$candidate || $assignment->candidate_id !== $candidate->id) {
            return redirect()->route('candidate.access');
        }

        Consent::firstOrCreate(
            ['candidate_id' => $candidate->id, 'assignment_id' => $assignment->id, 'test_type' => $assignment->test->test_type],
            [
                'consent_version' => '1.0',
                'ip_address'      => $request->ip(),
                'user_agent'      => substr($request->userAgent() ?? '', 0, 500),
                'consented_at'    => now(),
            ]
        );

        return redirect()->route('candidate.start', $assignment);
    }

    /**
     * Inicia o reanuda una prueba.
     */
    public function start(TestAssignment $assignment): View|RedirectResponse
    {
        $candidate = $this->getSessionCandidate();
        if (!$candidate || $assignment->candidate_id !== $candidate->id) {
            return redirect()->route('candidate.access');
        }

        // Pruebas administradas por el evaluador no son accesibles por el candidato
        if ($assignment->test->evaluator_scored) {
            return redirect()->route('candidate.dashboard')
                ->with('info', 'Esta prueba es administrada directamente por el evaluador.');
        }

        if ($assignment->isCompleted()) {
            return redirect()->route('candidate.result', $assignment)
                ->with('info', 'Esta prueba ya fue completada.');
        }

        if ($assignment->isExpired()) {
            $assignment->update(['status' => 'expired']);
            return redirect()->route('candidate.dashboard')
                ->with('error', 'Esta prueba ha expirado.');
        }

        // Verificar consentimiento antes de permitir iniciar
        if ($assignment->isPending()) {
            $hasConsent = Consent::where('candidate_id', $candidate->id)
                ->where('assignment_id', $assignment->id)
                ->exists();

            if (!$hasConsent) {
                return redirect()->route('candidate.consent', $assignment);
            }

            $assignment->update([
                'status' => 'in_progress',
                'started_at' => Carbon::now(),
                'time_remaining' => $assignment->test->time_limit
                    ? $assignment->test->time_limit * 60
                    : null,
            ]);
        }

        $assignment->load(['test.questions.options', 'answers']);

        return view('candidate.test', compact('assignment'));
    }

    /**
     * Guarda una respuesta individual (llamado por AJAX para autoguardado).
     */
    public function saveAnswer(Request $request, TestAssignment $assignment): JsonResponse
    {
        $candidate = $this->getSessionCandidate();
        if (!$candidate || $assignment->candidate_id !== $candidate->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'question_option_id' => 'nullable|exists:question_options,id',
            'text_answer' => 'nullable|string|max:2000',
            'time_remaining' => 'nullable|integer|min:0',
        ]);

        Answer::updateOrCreate(
            [
                'test_assignment_id' => $assignment->id,
                'question_id' => $validated['question_id'],
            ],
            [
                'question_option_id' => $validated['question_option_id'] ?? null,
                'text_answer' => $validated['text_answer'] ?? null,
                'score' => 0, // Se calcula al finalizar
            ]
        );

        // Actualizar tiempo restante
        if (isset($validated['time_remaining'])) {
            $assignment->update(['time_remaining' => $validated['time_remaining']]);
        }

        return response()->json(['saved' => true]);
    }

    /**
     * Finaliza la prueba y calcula el resultado.
     */
    public function finish(Request $request, TestAssignment $assignment): RedirectResponse
    {
        $candidate = $this->getSessionCandidate();
        if (!$candidate || $assignment->candidate_id !== $candidate->id) {
            return redirect()->route('candidate.access');
        }

        if (!$assignment->isInProgress()) {
            return redirect()->route('candidate.dashboard');
        }

        $result = $this->scoringService->calculate($assignment);

        return redirect()->route('candidate.result', $assignment)
            ->with('success', 'Prueba finalizada exitosamente.');
    }

    /**
     * Muestra el resultado al candidato.
     */
    public function result(TestAssignment $assignment): View|RedirectResponse
    {
        $candidate = $this->getSessionCandidate();
        if (!$candidate || $assignment->candidate_id !== $candidate->id) {
            return redirect()->route('candidate.access');
        }

        $assignment->load(['test', 'result']);

        return view('candidate.result', compact('assignment'));
    }

    public function logout(): RedirectResponse
    {
        session()->forget('candidate_id');
        return redirect()->route('candidate.access');
    }

    private function getSessionCandidate(): ?Candidate
    {
        $id = session('candidate_id');
        return $id ? Candidate::find($id) : null;
    }
}
