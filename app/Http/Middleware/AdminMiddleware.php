<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // cek apakah user sudah login
        if (!auth()->check()) {
            return redirect('/login');
        }

        // cek apakah nama user sesuai dengan admin
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized. Hanya admin yang boleh masuk.');
        }


        return $next($request);
    }
}
