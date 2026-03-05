<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function __construct(protected UserRepositoryInterface $userRepository) {}

    public function getAllUsers(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->userRepository->getAllPaginated($perPage, $filters);
    }

    public function findUserById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $user = $this->userRepository->create($data);

        if (!empty($data['role'])) {
            $user->assignRole($data['role']);
        }

        unset($data);

        return $user->load('roles');
    }

    public function updateUser(User $user, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $updated = $this->userRepository->update($user, $data);

        if (!empty($data['role'])) {
            $updated->syncRoles([$data['role']]);
        }

        unset($data);

        return $updated->load('roles');
    }

    public function deleteUser(User $user): bool
    {
        Log::info('User deleted', ['user_id' => $user->id, 'username' => $user->username]);

        return $this->userRepository->delete($user);
    }
}
