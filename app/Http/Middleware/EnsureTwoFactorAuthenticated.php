<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->two_factor_confirmed_at && !session('two_factor_authenticated')) {
            // Guardar el ID para el challenge y hacer logout temporal
            session(['two_factor_user_id' => $user->id]);
            auth()->logout();
            return redirect()->route('two-factor.challenge');
        }

        return $next($request);
    }
}
