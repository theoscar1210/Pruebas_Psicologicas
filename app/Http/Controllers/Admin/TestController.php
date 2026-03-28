<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestController extends Controller
{
    public function index(): View
    {
        $tests = Test::with('createdBy')
            ->withCount('questions')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.tests.index', compact('tests'));
    }

    public function create(): View
    {
        return view('admin.tests.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1|max:300',
            'passing_score' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
        ]);

        $test = Test::create(array_merge($validated, [
            'created_by' => auth()->id(),
            'is_active' => $request->boolean('is_active', true),
        ]));

        return redirect()->route('admin.tests.questions.index', $test)
            ->with('success', "Prueba '{$test->name}' creada. Ahora agrega las preguntas.");
    }

    public function show(Test $test): View
    {
        $test->load(['questions.options', 'positions', 'createdBy']);
        return view('admin.tests.show', compact('test'));
    }

    public function edit(Test $test): View
    {
        return view('admin.tests.edit', compact('test'));
    }

    public function update(Request $request, Test $test): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1|max:300',
            'passing_score' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
        ]);

        $test->update(array_merge($validated, [
            'is_active' => $request->boolean('is_active'),
        ]));

        return redirect()->route('admin.tests.index')
            ->with('success', "Prueba '{$test->name}' actualizada.");
    }

    public function destroy(Test $test): RedirectResponse
    {
        if ($test->assignments()->where('status', 'in_progress')->exists()) {
            return back()->with('error', 'No se puede eliminar una prueba con sesiones activas.');
        }

        $test->delete();
        return redirect()->route('admin.tests.index')
            ->with('success', 'Prueba eliminada.');
    }
}
