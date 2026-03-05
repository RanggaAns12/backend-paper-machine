<?php

namespace App\Services;

use App\Models\Machine;
use App\Repositories\Interfaces\MachineRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class MachineService
{
    public function __construct(protected MachineRepositoryInterface $machineRepository) {}

    public function getAllMachines(): LengthAwarePaginator
    {
        return $this->machineRepository->getAllPaginated();
    }

    public function findMachineById(int $id): ?Machine
    {
        return $this->machineRepository->findById($id);
    }

    public function createMachine(array $data): Machine
    {
        return $this->machineRepository->create($data);
    }

    public function updateMachine(Machine $machine, array $data): Machine
    {
        return $this->machineRepository->update($machine, $data);
    }

    public function deleteMachine(Machine $machine): bool
    {
        return $this->machineRepository->delete($machine);
    }
}
