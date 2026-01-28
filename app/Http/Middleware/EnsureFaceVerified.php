<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\FaceData;

class EnsureFaceVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        if (!FaceData::where('user_id', $user->id)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Wajah belum diverifikasi'
            ], 403);
        }

        return $next($request);
    }
}
