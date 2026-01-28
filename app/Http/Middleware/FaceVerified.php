<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FaceVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()?->face_verified_at) {
            return response()->json([
                'status' => false,
                'message' => 'Verifikasi wajah dulu'
            ], 403);
        }

        return $next($request);
    }
}
