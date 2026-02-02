<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FaceVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        if (!$user->face_embedding) {
            return response()->json([
                'status' => false,
                'code' => 'FACE_NOT_REGISTERED',
                'message' => 'Wajah belum diregistrasi'
            ], 403);
        }

        if (!$user->face_verified) {
            return response()->json([
                'status' => false,
                'code' => 'FACE_NOT_VERIFIED',
                'message' => 'Wajah belum diverifikasi'
            ], 403);
        }

        return $next($request);
    }
}
