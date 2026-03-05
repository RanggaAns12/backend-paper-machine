<?php

namespace App\Repositories;

use App\Models\WinderLog;
use App\Repositories\Interfaces\WinderLogRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class WinderLogRepository implements WinderLogRepositoryInterface
{
    public function __construct(protected WinderLog $model) {}

    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->model
            ->select(['id', 'report_id', 'operator_id', 'roll_number', 'status', 'wound_at', 'created_at'])
            ->with([
                'report:id,date,grup',
                'operator:id,name,username',
            ])
            ->when(!empty($filters['status']), fn($q) =>
                $q->where('status', $filters['status'])
            )
            ->when(!empty($filters['report_id']), fn($q) =>
                $q->where('report_id', $filters['report_id'])
            )
            ->when(!empty($filters['operator_id']), fn($q) =>
                $q->where('operator_id', $filters['operator_id'])
            )
            ->latest()
            ->paginate(min($perPage, 50));
    }

    public function findById(int $id): ?WinderLog
    {
        return $this->model
            ->select([
                'id', 'report_id', 'operator_id', 'roll_number',
                'roll_weight', 'core_diameter', 'width',
                'status', 'wound_at', 'created_at',
            ])
            ->with([
                'report:id,date,grup',
                'operator:id,name,username',
            ])
            ->find($id);
    }

    public function create(array $data): WinderLog
    {
        return $this->model->create($data);
    }

    public function update(WinderLog $winderLog, array $data): WinderLog
    {
        $winderLog->update($data);
        unset($data);
        return $winderLog->fresh(['report', 'operator']);
    }

    public function delete(WinderLog $winderLog): bool
    {
        return $winderLog->delete();
    }
}
