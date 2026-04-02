<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogSlowRequests
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);
        $duration = (microtime(true) - $start) * 1000;

        if ($duration > 1000) {
            Log::warning('Slow Request', [
                'url' => $request->fullUrl(),
                'route' => optional($request->route())->getName(),
                'method' => $request->method(),
                'time_ms' => round($duration, 2),
            ]);
        }

        return $response;
    }
}
