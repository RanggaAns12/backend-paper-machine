<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek jika user ada dan tidak aktif
        if ($request->user() && !$request->user()->is_active) {
            // ✅ Revoke token user tidak aktif
            $request->user()->tokens()->delete();
            
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak aktif. Hubungi administrator.',
                'data' => null,
                'errors' => null,
            ], 403);
        }

        return $next($request);
    }
}