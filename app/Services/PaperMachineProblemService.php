<?php

namespace App\Services;

use App\Models\PaperMachineProblem;
use App\Repositories\Interfaces\PaperMachineProblemRepositoryInterface;

class PaperMachineProblemService
{
    public function __construct(protected PaperMachineProblemRepositoryInterface $problemRepository) {}

    public function addProblemToReport(int $reportId, array $data): void
    {
        $this->problemRepository->createBulk($reportId, [$data]);
    }

    public function findProblemById(int $id): ?PaperMachineProblem
    {
        return $this->problemRepository->findById($id);
    }

    public function updateProblem(PaperMachineProblem $problem, array $data): PaperMachineProblem
    {
        return $this->problemRepository->update($problem, $data);
    }

    public function deleteProblem(PaperMachineProblem $problem): bool
    {
        return $this->problemRepository->delete($problem);
    }
}
