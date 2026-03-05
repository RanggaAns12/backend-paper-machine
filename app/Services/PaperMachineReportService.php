<?php

namespace App\Services;

use App\Models\PaperMachineReport;
use App\Repositories\Interfaces\PaperMachineReportRepositoryInterface;
use App\Repositories\Interfaces\PaperMachineRollRepositoryInterface;
use App\Repositories\Interfaces\PaperMachineProblemRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaperMachineReportService
{
    public function __construct(
        protected PaperMachineReportRepositoryInterface $reportRepository,
        protected PaperMachineRollRepositoryInterface $rollRepository,
        protected PaperMachineProblemRepositoryInterface $problemRepository
    ) {}

    public function getAllReports(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->reportRepository->getAllPaginated($perPage, $filters);
    }

    public function findReportById(int $id): ?PaperMachineReport
    {
        return $this->reportRepository->findById($id);
    }

    public function createReportWithDetails(array $data, int $operatorId): PaperMachineReport
    {
        return DB::transaction(function () use ($data, $operatorId) {
            $reportData = collect($data)->except(['rolls', 'problems'])->toArray();
            $reportData['operator_id'] = $operatorId;

            $report = $this->reportRepository->create($reportData);
            unset($reportData);

            if (!empty($data['rolls'])) {
                $this->rollRepository->createBulk($report->id, $data['rolls']);
            }

            if (!empty($data['problems'])) {
                $this->problemRepository->createBulk($report->id, $data['problems']);
            }

            unset($data);

            Log::info('Report created', ['report_id' => $report->id, 'operator_id' => $operatorId]);

            return $this->reportRepository->findById($report->id);
        });
    }

    public function updateReport(PaperMachineReport $report, array $data): PaperMachineReport
    {
        $updated = $this->reportRepository->update($report, $data);

        Log::info('Report updated', ['report_id' => $report->id]);

        unset($data);

        return $updated;
    }

    public function deleteReport(PaperMachineReport $report): bool
    {
        Log::info('Report deleted', ['report_id' => $report->id]);

        return $this->reportRepository->delete($report);
    }
}
