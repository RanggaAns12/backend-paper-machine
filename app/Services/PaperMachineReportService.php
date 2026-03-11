<?php

namespace App\Services;

use App\Models\PaperMachineReport;
use App\Repositories\Interfaces\PaperMachineReportRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PaperMachineReportService
{
    protected PaperMachineReportRepositoryInterface $reportRepository;

    public function __construct(PaperMachineReportRepositoryInterface $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    /**
     * Create report with details (rolls & problems)
     */
    public function createReportWithDetails(array $data, int $operatorId): PaperMachineReport
    {
        return DB::transaction(function () use ($data, $operatorId) {
            $headerData = collect($data)->except(['rolls', 'problems'])->toArray();
            $headerData['operator_id'] = $operatorId;

            $report = $this->reportRepository->create($headerData);

            \Log::info('Report created with ID: ' . $report->id);

            if (!empty($data['rolls']) && is_array($data['rolls'])) {
                \Log::info('Creating rolls for report ID: ' . $report->id, [
                    'rolls_count' => count($data['rolls'])
                ]);
                $this->reportRepository->createBulkRolls($report->id, $data['rolls']);
            }

            if (!empty($data['problems']) && is_array($data['problems'])) {
                \Log::info('Creating problems for report ID: ' . $report->id, [
                    'problems_count' => count($data['problems'])
                ]);
                $this->reportRepository->createBulkProblems($report->id, $data['problems']);
            }

            return $report->fresh(['machine', 'operator', 'rolls', 'problems']);
        });
    }

    /**
     * Update report header and problems only.
     * Rolls are handled by row-level endpoints/controller.
     */
    public function updateReport(PaperMachineReport $report, array $data): PaperMachineReport
    {
        return DB::transaction(function () use ($report, $data) {
            $headerData = collect($data)->except(['rolls', 'problems'])->toArray();

            \Log::info('Updating report header', [
                'report_id' => $report->id,
                'header_keys' => array_keys($headerData),
                'has_rolls_key' => array_key_exists('rolls', $data),
                'has_problems_key' => array_key_exists('problems', $data),
            ]);

            $updatedReport = $this->reportRepository->update($report, $headerData);

            /**
             * IMPORTANT:
             * Do not delete/recreate rolls here.
             * Row save/update is handled by PaperMachineRollController / PaperMachineRollService.
             * This prevents previously saved rolls from disappearing on refresh.
             */

            if (array_key_exists('problems', $data) && is_array($data['problems'])) {
                \Log::info('Refreshing problems for report', [
                    'report_id' => $report->id,
                    'problems_count' => count($data['problems'])
                ]);

                $report->problems()->delete();

                if (!empty($data['problems'])) {
                    $this->reportRepository->createBulkProblems($report->id, $data['problems']);
                }
            }

            return $updatedReport->fresh(['machine', 'operator', 'rolls', 'problems']);
        });
    }

    /**
     * Get report by ID
     */
    public function getReportById(int $id): ?PaperMachineReport
    {
        return $this->reportRepository->findById($id);
    }

    /**
     * Alias for compatibility with controller
     */
    public function findReportById(int $id): ?PaperMachineReport
    {
        return $this->reportRepository->findById($id);
    }

    /**
     * Get all reports with pagination
     */
    public function getAllReports(array $filters = [], int $perPage = 15)
    {
        return $this->reportRepository->getAllPaginated($filters, $perPage);
    }

    /**
     * Unlock report (super admin only)
     */
    public function unlockReport(int $id): ?PaperMachineReport
    {
        $report = $this->reportRepository->findById($id);

        if (!$report) {
            return null;
        }

        return $this->reportRepository
            ->update($report, ['is_locked' => false])
            ->fresh(['machine', 'operator', 'rolls', 'problems']);
    }

    /**
     * Delete report
     */
    public function deleteReport(PaperMachineReport $report): bool
    {
        return $this->reportRepository->delete($report);
    }
}