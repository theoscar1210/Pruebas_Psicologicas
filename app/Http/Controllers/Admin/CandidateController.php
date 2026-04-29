<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Position;
use App\Models\Test;
use App\Models\TestAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CandidateController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->filter;

        $candidates = Candidate::with(['position', 'assignments.result'])
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('document_number', 'like', "%{$request->search}%"))
            ->when($request->position_id, fn ($q) => $q->where('position_id', $request->position_id))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($filter === 'evaluacion', fn ($q) =>
                $q->whereHas('assignments', fn ($q) => $q->where('status', 'completed'))
                  ->with(['evaluatorAssessments'])
            )
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $positions = Position::where('is_active', true)->orderBy('name')->get();

        return view('admin.candidates.index', compact('candidates', 'positions', 'filter'));
    }

    public function perfilesIndex(): View
    {
        $candidates = Candidate::with(['position', 'latestReport'])
            ->whereHas('psychologicalReports')
            ->orderByDesc('updated_at')
            ->paginate(25);

        return view('admin.perfiles.index', compact('candidates'));
    }

    public function create(): View
    {
        $positions = Position::where('is_active', true)->orderBy('name')->get();
        return view('admin.candidates.create', compact('positions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:20',
            'document_number' => 'nullable|string|max:20',
            'position_id' => 'nullable|exists:positions,id',
        ]);

        $candidate = Candidate::create(array_merge($validated, [
            'created_by' => auth()->id(),
        ]));

        // Si tiene cargo, asignar automáticamente las pruebas del cargo
        if ($candidate->position_id) {
            $this->assignPositionTests($candidate);
        }

        return redirect()->route('admin.candidates.show', $candidate)
            ->with('success', "Candidato '{$candidate->name}' creado. Código de acceso: {$candidate->access_code}");
    }

    public function show(Candidate $candidate): View
    {
        $candidate->load(['position', 'assignments.test', 'assignments.result', 'assignments.dimensionScores', 'createdBy', 'evaluatorAssessments', 'tscSlSessions']);
        return view('admin.candidates.show', compact('candidate'));
    }

    public function edit(Candidate $candidate): View
    {
        $positions = Position::where('is_active', true)->orderBy('name')->get();
        return view('admin.candidates.edit', compact('candidate', 'positions'));
    }

    public function update(Request $request, Candidate $candidate): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:20',
            'document_number' => 'nullable|string|max:20',
            'position_id' => 'nullable|exists:positions,id',
            'status' => 'required|in:active,inactive,completed',
        ]);

        $candidate->update($validated);

        return redirect()->route('admin.candidates.show', $candidate)
            ->with('success', 'Candidato actualizado.');
    }

    public function assignTest(Request $request, Candidate $candidate): RedirectResponse
    {
        $validated = $request->validate([
            'test_id'    => 'required|exists:tests,id',
            'expires_at' => 'nullable|date|after:yesterday',
        ], [
            'test_id.required' => 'Debes seleccionar una prueba.',
            'test_id.exists'   => 'La prueba seleccionada no existe.',
            'expires_at.date'  => 'El formato de la fecha límite no es válido.',
            'expires_at.after' => 'La fecha límite debe ser hoy o una fecha futura.',
        ]);

        $redirectTo = redirect()->route('admin.candidates.show', $candidate);

        // Evita duplicados: no asignar si ya tiene esa prueba pendiente o en progreso
        $exists = TestAssignment::where('candidate_id', $candidate->id)
            ->where('test_id', $validated['test_id'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->exists();

        if ($exists) {
            return $redirectTo->with('error', 'El candidato ya tiene esta prueba asignada y pendiente.');
        }

        TestAssignment::create([
            'candidate_id' => $candidate->id,
            'test_id'      => $validated['test_id'],
            'position_id'  => $candidate->position_id,
            'assigned_by'  => auth()->id(),
            'status'       => 'pending',
            'expires_at'   => $validated['expires_at'] ?? null,
        ]);

        $testName = Test::find($validated['test_id'])?->name ?? 'Prueba';

        return $redirectTo->with('success', "«{$testName}» asignada correctamente a {$candidate->name}.");
    }

    public function destroyAssignment(TestAssignment $assignment): RedirectResponse
    {
        $candidate = $assignment->candidate;
        $testName  = $assignment->test->name;

        $assignment->answers()->delete();
        $assignment->dimensionScores()->delete();
        $assignment->result()->delete();
        $assignment->delete();

        return redirect()->route('admin.candidates.show', $candidate)
            ->with('success', "Prueba «{$testName}» eliminada correctamente.");
    }

    private function assignPositionTests(Candidate $candidate): void
    {
        $position = $candidate->position()->with('tests')->first();

        foreach ($position->tests as $test) {
            TestAssignment::create([
                'candidate_id' => $candidate->id,
                'test_id' => $test->id,
                'position_id' => $position->id,
                'assigned_by' => auth()->id(),
                'status' => 'pending',
            ]);
        }
    }
}
