<?php

namespace App\Repositories;

// ✅ IMPORT YANG BENAR: Models (bukan Repositories)
use App\Models\PaperMachineReport;
use App\Models\PaperMachineRoll;      // ← HARUS App\Models\
use App\Models\PaperMachineProblem;   // ← HARUS App\Models\

use App\Repositories\Interfaces\PaperMachineReportRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class PaperMachineReportRepository implements PaperMachineReportRepositoryInterface
{
    /**
     * Get all reports with pagination
     */
    public function getAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = PaperMachineReport::query()
            ->with(['machine', 'operator'])
            ->withCount(['rolls', 'problems'])
            ->select([
                'paper_machine_reports.id',
                'paper_machine_reports.machine_id',
                'paper_machine_reports.operator_id',
                'paper_machine_reports.operator_name',
                'paper_machine_reports.date',
                'paper_machine_reports.grup',
                'paper_machine_reports.steam_kg',
                'paper_machine_reports.water_l',
                'paper_machine_reports.power_mwh',
                'paper_machine_reports.temperature_c',
                'paper_machine_reports.total_pm',
                'paper_machine_reports.total_winder',
                'paper_machine_reports.remarks',
                'paper_machine_reports.is_locked',
                'paper_machine_reports.created_at',
            ]);

        if (!empty($filters['date'])) {
            $query->whereDate('date', $filters['date']);
        }

        if (!empty($filters['grup'])) {
            $query->where('grup', $filters['grup']);
        }

        if (!empty($filters['search'])) {
            $query->where('operator_name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('date', 'desc')->paginate($perPage);
    }

    /**
     * Find report by ID with relationships
     */
    public function findById(int $id): ?PaperMachineReport
    {
        return PaperMachineReport::with(['machine', 'operator', 'rolls', 'problems'])->find($id);
    }

    /**
     * Create new report
     */
    public function create(array $data): PaperMachineReport
    {
        return PaperMachineReport::create($data);
    }

    /**
     * Update existing report
     */
    public function update(PaperMachineReport $report, array $data): PaperMachineReport
    {
        $report->update($data);
        return $report->fresh();
    }

    /**
     * Delete report
     */
    public function delete(PaperMachineReport $report): bool
    {
        return $report->delete();
    }

    /**
     * Create bulk rolls
     * ⚠️ PENTING: Inject report_id ke setiap roll
     */
    public function createBulkRolls(int $reportId, array $rollsData): void
    {
        foreach ($rollsData as $roll) {
            // ✅ WAJIB: Inject report_id
            $roll['report_id'] = $reportId;
            
            // ✅ Hapus field yang tidak boleh di-mass assign
            unset($roll['id']);
            
            // ✅ Hapus is_saved jika kolom tidak ada di migration
            if (array_key_exists('is_saved', $roll) && !Schema::hasColumn('paper_machine_rolls', 'is_saved')) {
                unset($roll['is_saved']);
            }
            
            // ✅ Create roll dengan Model yang benar
            PaperMachineRoll::create($roll);
        }
    }

    /**
     * Create bulk problems
     * ⚠️ PENTING: Inject report_id ke setiap problem
     */
    public function createBulkProblems(int $reportId, array $problemsData): void
    {
        foreach ($problemsData as $problem) {
            // ✅ WAJIB: Inject report_id
            $problem['report_id'] = $reportId;
            
            // ✅ Hapus field yang tidak boleh di-mass assign
            unset($problem['id']);
            
            // ✅ Create problem dengan Model yang benar
            PaperMachineProblem::create($problem);
        }
    }
}