<?php

namespace App\Repositories\Interfaces;

use App\Models\PaperMachineProblem;

interface PaperMachineProblemRepositoryInterface
{
    public function createBulk(int $reportId, array $problems): void;
    public function findById(int $id): ?PaperMachineProblem;
    public function update(PaperMachineProblem $problem, array $data): PaperMachineProblem;
    public function delete(PaperMachineProblem $problem): bool;
}
