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

    // =================================================================
    // 🔥 FUNGSI BARU: GATEKEEPER UNTUK WINDER & QC
    // =================================================================
    
    /**
     * Mengambil daftar Jumbo Roll yang siap dipotong di Winder.
     * Syarat: Laporan PM sudah di-lock AND QC Status 'passed' atau 'downgrade'.
     */
    public function getAvailableRolls()
    {
        return $this->model
            ->with('report') // Optional: bawa data laporannya sekalian
            ->whereHas('report', function ($query) {
                // 1. Laporan PM harus sudah dikunci (disahkan)
                $query->where('is_locked', true);
            })
            // 2. Status QC harus Passed atau Downgrade
            ->whereIn('qc_status', ['passed', 'downgrade'])
            // 3. Urutkan dari yang terbaru
            ->orderBy('id', 'desc')
            ->get();
    }
}