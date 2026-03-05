<?php

namespace App\Repositories\Interfaces;

use App\Models\Machine;
use Illuminate\Pagination\LengthAwarePaginator;

interface MachineRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findById(int $id): ?Machine;
    public function create(array $data): Machine;
    public function update(Machine $machine, array $data): Machine;
    public function delete(Machine $machine): bool;
}
