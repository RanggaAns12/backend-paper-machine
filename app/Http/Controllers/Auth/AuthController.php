<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        // Jangan load ulang semua relasi berat
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Data user yang sedang login.',
            'data'    => [
                'id'        => $user->id,
                'name'      => $user->name,
                'username'  => $user->username,
                'is_active' => $user->is_active,
                'role'      => $user->getRoleNames()->first(), // Ambil nama role saja
            ],
            'errors'  => null,
        ]);
    }
}
