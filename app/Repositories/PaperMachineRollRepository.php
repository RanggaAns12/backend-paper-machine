<?php

namespace App\Repositories;

use App\Models\PaperMachineRoll;
use App\Repositories\Interfaces\PaperMachineRollRepositoryInterface;

class PaperMachineRollRepository implements PaperMachineRollRepositoryInterface
{
    public function __construct(protected PaperMachineRoll $model) {}

    public function createBulk(int $reportId, array $rolls): void
    {
        $now     = now();
        $records = [];

        foreach ($rolls as $roll) {
            $records[] = array_merge($roll, [
                'report_id'  => $reportId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->model->insert($records);

        unset($records, $rolls);
    }

    public function findById(int $id): ?PaperMachineRoll
    {
        return $this->model->find($id);
    }

    public function update(PaperMachineRoll $roll, array $data): PaperMachineRoll
    {
        $roll->update($data);
        unset($data);
        return $roll->fresh();
    }

    public function delete(PaperMachineRoll $roll): bool
    {
        return $roll->delete();
    }
}
