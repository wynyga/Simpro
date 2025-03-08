<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsManager
{
    public function handle(Request $request, Closure $next)
    {
        // Periksa jika user login dan memiliki role manager
        if (Auth::check() && Auth::user()->role === 'Manager') {
            return $next($request);
        }

        // Jika bukan manager, kirim response error
        return response()->json(['error' => 'Unauthorized, only managers can perform this action'], 403);
    }
}
