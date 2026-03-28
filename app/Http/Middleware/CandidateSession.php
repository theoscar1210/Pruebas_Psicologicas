<?php

namespace App\Http\Middleware;

use App\Models\Candidate;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CandidateSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $candidateId = session('candidate_id');

        if (!$candidateId || !Candidate::where('id', $candidateId)->where('status', 'active')->exists()) {
            session()->forget('candidate_id');
            return redirect()->route('candidate.access')
                ->with('error', 'Tu sesión ha expirado. Ingresa nuevamente tu código de acceso.');
        }

        return $next($request);
    }
}
