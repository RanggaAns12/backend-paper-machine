<?php

namespace App\Repositories\Interfaces;

use App\Models\PaperMachineRoll;

interface PaperMachineRollRepositoryInterface
{
    public function createBulk(int $reportId, array $rolls): void;
    public function findById(int $id): ?PaperMachineRoll;
    public function update(PaperMachineRoll $roll, array $data): PaperMachineRoll;
    public function delete(PaperMachineRoll $roll): bool;
    public function getAvailableRolls();
}
