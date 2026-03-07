<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user){
            return response()->json([
                'status' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        if(!$user->is_active){
                return response()->json([
                    'status' => false,
                    'message' => 'Akun dinonaktifkan, hubungi admin'
                ], 403);
            }
        

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        // hapus token lama (optional)
        $user->tokens()->delete();

        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
                'profile' => $user->profile,
            ]
        ]);
    }

    
    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    // PROFILE
    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => $request->user()->load('profile')
        ]);
    }
}
