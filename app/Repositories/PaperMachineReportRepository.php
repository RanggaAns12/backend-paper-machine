<?php

namespace App\Repositories;

use App\Models\PaperMachineReport;
use App\Repositories\Interfaces\PaperMachineReportRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class PaperMachineReportRepository implements PaperMachineReportRepositoryInterface
{
    public function __construct(protected PaperMachineReport $model) {}

    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->model
            ->select([
                'id', 'machine_id', 'operator_id', 'date',
                'grup', 'working_hour', 'kg_per_shift',
                'total_pm', 'total_winder', 'created_at',
            ])
            ->with([
                'machine:id,name,type',
                'operator:id,name,username',
            ])
            ->withCount(['rolls', 'problems'])
            ->when(!empty($filters['date']), fn($q) =>
                $q->whereDate('date', $filters['date'])
            )
            ->when(!empty($filters['grup']), fn($q) =>
                $q->where('grup', $filters['grup'])
            )
            ->when(!empty($filters['machine_id']), fn($q) =>
                $q->where('machine_id', $filters['machine_id'])
            )
            ->when(!empty($filters['operator_id']), fn($q) =>
                $q->where('operator_id', $filters['operator_id'])
            )
            ->latest()
            ->paginate(min($perPage, 50));
    }

    public function findById(int $id): ?PaperMachineReport
    {
        return $this->model
            ->select([
                'id', 'machine_id', 'operator_id', 'date', 'grup',
                'working_hour', 'steam', 'water', 'kg_per_shift',
                'l_per_shift', 'power', 'temperature', 'mwh_per_shift',
                'total_pm', 'total_winder', 'remarks', 'created_at',
            ])
            ->with([
                'machine:id,name,type,status',
                'operator:id,name,username',
                'rolls',
                'problems',
            ])
            ->find($id);
    }

    public function create(array $data): PaperMachineReport
    {
        return $this->model->create($data);
    }

    public function update(PaperMachineReport $report, array $data): PaperMachineReport
    {
        $report->update($data);

        unset($data);

        return $report->fresh(['machine', 'operator', 'rolls', 'problems']);
    }

    public function delete(PaperMachineReport $report): bool
    {
        return $report->delete();
    }
}
