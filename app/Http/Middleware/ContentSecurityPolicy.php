<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = base64_encode(random_bytes(16));

        app()->instance('csp-nonce', $nonce);
        Vite::useCspNonce($nonce);
        // Livewire 4 lee el nonce de Vite::cspNonce() automáticamente

        $response = $next($request);

        // Alpine.js evalúa expresiones x-data/x-on/x-init con new Function()
        // internamente — unsafe-eval es inevitable sin migrar a @alpinejs/csp.
        $scriptSrc  = "'self' 'nonce-{$nonce}' 'unsafe-eval'";
        $styleSrc   = "'self' 'unsafe-inline' https://fonts.bunny.net";
        $connectSrc = "'self'";

        // En desarrollo el servidor Vite corre en un puerto/origen distinto.
        if (app()->environment('local') && Vite::isRunningHot()) {
            $devServer   = trim(file_get_contents(public_path('hot')));
            $scriptSrc  .= " {$devServer}";
            $styleSrc   .= " {$devServer}";
            $connectSrc .= " {$devServer} ws: wss:";
        }

        $csp = implode('; ', [
            "default-src 'self'",
            "script-src {$scriptSrc}",
            "style-src {$styleSrc}",
            "font-src 'self' data: https://fonts.bunny.net",
            "img-src 'self' data: blob:",
            "connect-src {$connectSrc}",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "object-src 'none'",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
