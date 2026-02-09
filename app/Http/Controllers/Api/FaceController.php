<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FaceData;

class FaceController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'embedding' => 'required|array|size:192'
        ]);

        $embedding = array_map('floatval', $request->embedding);

        FaceData::updateOrCreate(
            ['user_id' => $request->user()->id],
            ['embedding' => $embedding]
        );

        return response()->json([
            'status'  => true,
            'message' => 'Wajah berhasil didaftarkan'
        ]);
    }

    
    public function verify(Request $request)
    {
        $request->validate([
            'embedding' => 'required|array|size:192'
        ]);

        $user = $request->user()->load('profile');

        $faceData = FaceData::where('user_id', $user->id)->first();
        if (!$faceData) {
            return response()->json([
                'status'  => false,
                'message' => 'Wajah belum terdaftar'
            ], 404);
        }

       $inputEmbedding  = array_values(array_map('floatval', $request->embedding));
$storedEmbedding = array_values(array_map('floatval', $faceData->embedding));


        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < 192; $i++) {
            $a = $inputEmbedding[$i];
            $b = $storedEmbedding[$i];

            $dot   += $a * $b;
            $normA += $a * $a;
            $normB += $b * $b;
        }

        if ($normA == 0 || $normB == 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Embedding rusak'
            ], 400);
        }

        $cosine = $dot / (sqrt($normA) * sqrt($normB));
     $threshold = 0.70;

        return response()->json([
            'status'     => $cosine >= $threshold,
            'similarity' => round($cosine, 4),
            'name'       => $user->profile->nama_lengkap ?? $user->username,
            'message'    => $cosine >= $threshold
                ? 'Wajah cocok'
                : 'Wajah tidak cocok'
        ]);
    }

    
    public function status(Request $request)
    {
        return response()->json([
            'registered' => FaceData::where(
                'user_id',
                $request->user()->id
            )->exists()
        ]);
    }
}
