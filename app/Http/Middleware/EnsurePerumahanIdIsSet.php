<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EnsurePerumahanIdIsSet
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah session perumahan_id sudah ada
        if (!Session::has('perumahan_id')) {
            // Jika tidak ada, kembalikan respons JSON
            return response()->json([
                'status' => 'error',
                'message' => 'Perumahan ID is not set. Please select a perumahan.',
            ], 403); // Status HTTP 403 Forbidden
        }

        return $next($request);
    }
}
