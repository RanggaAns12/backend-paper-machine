<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(protected User $model) {}

    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->model
            ->select(['id', 'name', 'username', 'is_active', 'created_at'])
            ->with('roles:id,name')
            ->when(!empty($filters['is_active']), fn($q) =>
                $q->where('is_active', $filters['is_active'])
            )
            ->when(!empty($filters['search']), fn($q) =>
                $q->where(function ($query) use ($filters) {
                    $query->where('name', 'like', '%' . $filters['search'] . '%')
                          ->orWhere('username', 'like', '%' . $filters['search'] . '%');
                })
            )
            ->latest()
            ->paginate(min($perPage, 50));
    }

    public function findById(int $id): ?User
    {
        return $this->model
            ->select(['id', 'name', 'username', 'is_active', 'created_at'])
            ->with('roles:id,name')
            ->find($id);
    }

    public function findByUsername(string $username): ?User
    {
        return $this->model
            ->select(['id', 'name', 'username', 'password', 'is_active'])
            ->where('username', $username)
            ->first();
    }

    public function create(array $data): User
    {
        $user = $this->model->create($data);

        Log::info('User created', ['user_id' => $user->id, 'username' => $user->username]);

        return $user;
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        Log::info('User updated', ['user_id' => $user->id, 'username' => $user->username]);

        return $user->fresh('roles');
    }

    public function delete(User $user): bool
    {
        Log::info('User deleted', ['user_id' => $user->id, 'username' => $user->username]);

        return $user->delete();
    }
}
