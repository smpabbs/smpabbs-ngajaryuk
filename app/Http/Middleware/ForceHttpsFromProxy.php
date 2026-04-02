<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceHttpsFromProxy
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Kalau request datang via proxy HTTPS (Cloudflare Tunnel)
        if ($request->header('X-Forwarded-Proto') === 'https') {
            \URL::forceScheme('https');
        }

        return $next($request);
    }
}
