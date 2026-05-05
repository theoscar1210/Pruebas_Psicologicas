<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\DataDeletionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DataDeletionController extends Controller
{
    public function index(): View
    {
        $pending = DataDeletionRequest::with('candidate.position')
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        $processed = DataDeletionRequest::with(['candidate', 'processedBy'])
            ->whereIn('status', ['approved', 'rejected'])
            ->orderByDesc('processed_at')
            ->limit(30)
            ->get();

        return view('admin.data-deletion.index', compact('pending', 'processed'));
    }

    public function approve(Request $request, DataDeletionRequest $deletion): RedirectResponse
    {
        $request->validate(['admin_notes' => 'nullable|string|max:500']);

        $candidateName = $deletion->candidate->name;

        $deletion->update([
            'status'       => 'approved',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
            'admin_notes'  => $request->admin_notes,
        ]);

        // Eliminar al candidato — las cascadas de BD borran todos sus datos
        $deletion->candidate->delete();

        return redirect()->route('admin.data-deletion.index')
            ->with('success', "Datos de «{$candidateName}» eliminados permanentemente.");
    }

    public function reject(Request $request, DataDeletionRequest $deletion): RedirectResponse
    {
        $request->validate(['admin_notes' => 'required|string|max:500']);

        $deletion->update([
            'status'       => 'rejected',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
            'admin_notes'  => $request->admin_notes,
        ]);

        return redirect()->route('admin.data-deletion.index')
            ->with('success', "Solicitud de «{$deletion->candidate->name}» rechazada.");
    }
}
