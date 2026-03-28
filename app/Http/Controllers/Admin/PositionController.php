<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Test;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PositionController extends Controller
{
    public function index(): View
    {
        $positions = Position::withCount('candidates')
            ->with('tests')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.positions.index', compact('positions'));
    }

    public function create(): View
    {
        $tests = Test::where('is_active', true)->orderBy('name')->get();
        return view('admin.positions.create', compact('tests'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:positions,name',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'tests' => 'nullable|array',
            'tests.*' => 'exists:tests,id',
        ]);

        $position = Position::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if (!empty($validated['tests'])) {
            $syncData = [];
            foreach ($validated['tests'] as $order => $testId) {
                $syncData[$testId] = ['order' => $order + 1];
            }
            $position->tests()->sync($syncData);
        }

        return redirect()->route('admin.positions.index')
            ->with('success', "Cargo '{$position->name}' creado exitosamente.");
    }

    public function edit(Position $position): View
    {
        $tests = Test::where('is_active', true)->orderBy('name')->get();
        $position->load('tests');
        return view('admin.positions.edit', compact('position', 'tests'));
    }

    public function update(Request $request, Position $position): RedirectResponse
    {
        $validated = $request->validate([
            'name' => "required|string|max:100|unique:positions,name,{$position->id}",
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'tests' => 'nullable|array',
            'tests.*' => 'exists:tests,id',
        ]);

        $position->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $syncData = [];
        foreach (($validated['tests'] ?? []) as $order => $testId) {
            $syncData[$testId] = ['order' => $order + 1];
        }
        $position->tests()->sync($syncData);

        return redirect()->route('admin.positions.index')
            ->with('success', "Cargo '{$position->name}' actualizado.");
    }

    public function destroy(Position $position): RedirectResponse
    {
        if ($position->candidates()->exists()) {
            return back()->with('error', 'No se puede eliminar un cargo con candidatos asociados.');
        }

        $position->delete();
        return redirect()->route('admin.positions.index')
            ->with('success', "Cargo eliminado.");
    }
}
