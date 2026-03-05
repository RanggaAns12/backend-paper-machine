<?php

namespace App\Repositories\Interfaces;

use App\Models\PaperMachineReport;
use Illuminate\Pagination\LengthAwarePaginator;

interface PaperMachineReportRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findById(int $id): ?PaperMachineReport;
    public function create(array $data): PaperMachineReport;
    public function update(PaperMachineReport $report, array $data): PaperMachineReport;
    public function delete(PaperMachineReport $report): bool;
}
