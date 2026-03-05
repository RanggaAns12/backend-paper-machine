<?php

namespace App\Repositories;

use App\Models\QualityTest;
use App\Repositories\Interfaces\QualityTestRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class QualityTestRepository implements QualityTestRepositoryInterface
{
    public function __construct(protected QualityTest $model) {}

    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->model
            ->select(['id', 'report_id', 'tested_by', 'result', 'tested_at', 'created_at'])
            ->with([
                'report:id,date,grup',
                'tester:id,name,username',
            ])
            ->when(!empty($filters['result']), fn($q) =>
                $q->where('result', $filters['result'])
            )
            ->when(!empty($filters['report_id']), fn($q) =>
                $q->where('report_id', $filters['report_id'])
            )
            ->when(!empty($filters['tested_by']), fn($q) =>
                $q->where('tested_by', $filters['tested_by'])
            )
            ->latest()
            ->paginate(min($perPage, 50));
    }

    public function findById(int $id): ?QualityTest
    {
        return $this->model
            ->select([
                'id', 'report_id', 'tested_by', 'moisture',
                'tensile_strength', 'brightness', 'smoothness',
                'result', 'notes', 'tested_at', 'created_at',
            ])
            ->with([
                'report:id,date,grup,machine_id',
                'tester:id,name,username',
            ])
            ->find($id);
    }

    public function create(array $data): QualityTest
    {
        return $this->model->create($data);
    }

    public function update(QualityTest $qualityTest, array $data): QualityTest
    {
        $qualityTest->update($data);
        unset($data);
        return $qualityTest->fresh(['report', 'tester']);
    }

    public function delete(QualityTest $qualityTest): bool
    {
        return $qualityTest->delete();
    }
}
