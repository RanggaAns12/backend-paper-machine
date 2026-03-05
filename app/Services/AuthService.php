<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(protected UserRepositoryInterface $userRepository) {}

    public function login(string $username, string $password): array
    {
        $user = $this->userRepository->findByUsername($username);

        if (!$user || !Hash::check($password, $user->password)) {
            Log::warning('Login gagal', ['username' => $username]);

            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        if (!$user->is_active) {
            Log::warning('Login akun nonaktif', [
                'user_id'  => $user->id,
                'username' => $user->username,
            ]);

            throw ValidationException::withMessages([
                'username' => ['Akun Anda telah dinonaktifkan. Hubungi administrator.'],
            ]);
        }

        $token = $user->createToken(
            'api-token',
            ['*'],
            now()->addHours(24)
        )->plainTextToken;

        Log::info('Login berhasil', [
            'user_id'  => $user->id,
            'username' => $user->username,
        ]);

        return [
            'user'  => $user, // Tidak perlu load('roles') disini
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        Log::info('Logout', [
            'user_id'  => $user->id,
            'username' => $user->username,
        ]);

        $user->currentAccessToken()->delete();
    }
}
