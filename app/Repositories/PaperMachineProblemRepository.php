<?php

namespace App\Repositories;

use App\Models\PaperMachineProblem;
use App\Repositories\Interfaces\PaperMachineProblemRepositoryInterface;

class PaperMachineProblemRepository implements PaperMachineProblemRepositoryInterface
{
    public function __construct(protected PaperMachineProblem $model) {}

    public function createBulk(int $reportId, array $problems): void
    {
        $now     = now();
        $records = [];

        foreach ($problems as $problem) {
            $records[] = array_merge($problem, [
                'report_id'  => $reportId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->model->insert($records);

        unset($records, $problems);
    }

    public function findById(int $id): ?PaperMachineProblem
    {
        return $this->model->find($id);
    }

    public function update(PaperMachineProblem $problem, array $data): PaperMachineProblem
    {
        $problem->update($data);
        unset($data);
        return $problem->fresh();
    }

    public function delete(PaperMachineProblem $problem): bool
    {
        return $problem->delete();
    }
}
