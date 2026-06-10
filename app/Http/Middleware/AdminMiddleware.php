<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Only allow admin users through. Staff are redirected back.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak. Hanya Admin yang diizinkan.'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Hanya Admin yang diizinkan.');
        }

        return $next($request);
    }
}
