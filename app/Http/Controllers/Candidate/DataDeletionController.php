<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\DataDeletionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DataDeletionController extends Controller
{
    public function create(Request $request): View
    {
        $candidate = $request->session()->get('candidate');

        $existing = DataDeletionRequest::where('candidate_id', $candidate->id)
            ->where('status', 'pending')
            ->first();

        return view('candidate.data-deletion', compact('candidate', 'existing'));
    }

    public function store(Request $request): RedirectResponse
    {
        $candidate = $request->session()->get('candidate');

        $alreadyPending = DataDeletionRequest::where('candidate_id', $candidate->id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return redirect()->route('candidate.data-deletion')
                ->with('info', 'Ya tienes una solicitud de eliminación pendiente. Será procesada en máximo 15 días hábiles.');
        }

        $request->validate(['reason' => 'nullable|string|max:1000']);

        DataDeletionRequest::create([
            'candidate_id' => $candidate->id,
            'reason'       => $request->reason,
            'ip_address'   => $request->ip(),
            'status'       => 'pending',
        ]);

        return redirect()->route('candidate.dashboard')
            ->with('success', 'Solicitud de eliminación de datos enviada. Será procesada en máximo 15 días hábiles conforme a la Ley 1581 de 2012.');
    }
}
