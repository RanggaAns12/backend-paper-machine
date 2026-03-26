<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // ✅ Wajib ada

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->validated('username'),
            $request->validated('password')
        );

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'user'  => new UserResource($result['user']),
                'token' => $result['token'],
            ],
            'errors'  => null,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
            'data'    => null,
            'errors'  => null,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        // Ambil user dari request (sudah ada dari Sanctum)
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Data user yang sedang login.',
            'data'    => [
                'id'        => $user->id,
                'name'      => $user->name,
                'username'  => $user->username,
                'is_active' => $user->is_active,
                'role'      => $user->getRoleNames()->first(), 
            ],
            'errors'  => null,
        ]);
    }

    // ✅ FITUR GANTI PASSWORD
    public function changePassword(Request $request): JsonResponse
    {
        // 1. Validasi Input
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password'     => ['required', 'string', 'min:8', 'confirmed'], 
        ]);

        $user = $request->user();

        // 2. Cek apakah password lama yang dimasukkan sesuai dengan di database
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini tidak sesuai.',
                'data'    => null,
                'errors'  => ['current_password' => ['Password saat ini salah.']]
            ], 422); // 422 agar ditangkap dengan baik oleh Angular Mas
        }

        // 3. Update password baru (✅ FIX: Wajib dibungkus Hash::make agar 100% aman)
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // 4. Return response sukses
        return response()->json([
            'success' => true,
            'message' => 'Password Anda berhasil diperbarui.',
            'data'    => null,
            'errors'  => null
        ]);
    }
}