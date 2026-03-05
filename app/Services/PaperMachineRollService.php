<?php

namespace App\Services;

use App\Models\PaperMachineRoll;
use App\Repositories\Interfaces\PaperMachineRollRepositoryInterface;

class PaperMachineRollService
{
    public function __construct(protected PaperMachineRollRepositoryInterface $rollRepository) {}

    public function addRollToReport(int $reportId, array $data): void
    {
        $this->rollRepository->createBulk($reportId, [$data]);
    }

    public function findRollById(int $id): ?PaperMachineRoll
    {
        return $this->rollRepository->findById($id);
    }

    public function updateRoll(PaperMachineRoll $roll, array $data): PaperMachineRoll
    {
        return $this->rollRepository->update($roll, $data);
    }

    public function deleteRoll(PaperMachineRoll $roll): bool
    {
        return $this->rollRepository->delete($roll);
    }
}
