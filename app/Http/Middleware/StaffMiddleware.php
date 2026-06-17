<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaffMiddleware
{
    /**
     * Only allow staff (non-admin) users through. Admins are redirected back.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak. Halaman ini hanya untuk Staff.'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Halaman ini hanya untuk Staff.');
        }

        return $next($request);
    }
}
