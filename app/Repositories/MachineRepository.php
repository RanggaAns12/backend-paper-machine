<?php

namespace App\Repositories;

use App\Models\Machine;
use App\Repositories\Interfaces\MachineRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class MachineRepository implements MachineRepositoryInterface
{
    public function __construct(protected Machine $model) {}

    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->model
            ->select(['id', 'name', 'type', 'status', 'description', 'created_at'])
            ->when(!empty($filters['type']), fn($q) =>
                $q->where('type', $filters['type'])
            )
            ->when(!empty($filters['status']), fn($q) =>
                $q->where('status', $filters['status'])
            )
            ->when(!empty($filters['search']), fn($q) =>
                $q->where('name', 'like', '%' . $filters['search'] . '%')
            )
            ->latest()
            ->paginate(min($perPage, 50));
    }

    public function findById(int $id): ?Machine
    {
        return $this->model
            ->select(['id', 'name', 'type', 'status', 'description', 'created_at'])
            ->find($id);
    }

    public function create(array $data): Machine
    {
        return $this->model->create($data);
    }

    public function update(Machine $machine, array $data): Machine
    {
        $machine->update($data);
        return $machine->fresh();
    }

    public function delete(Machine $machine): bool
    {
        return $machine->delete();
    }
}
