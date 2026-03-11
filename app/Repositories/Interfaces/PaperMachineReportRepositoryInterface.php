<?php

namespace App\Repositories\Interfaces;

use App\Models\PaperMachineReport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PaperMachineReportRepositoryInterface
{
    public function getAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    
    public function findById(int $id): ?PaperMachineReport;
    
    public function create(array $data): PaperMachineReport;
    
    public function update(PaperMachineReport $report, array $data): PaperMachineReport;
    
    public function delete(PaperMachineReport $report): bool;
    
    public function createBulkRolls(int $reportId, array $rollsData): void;
    
    public function createBulkProblems(int $reportId, array $problemsData): void;
}