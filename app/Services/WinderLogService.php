<?php

namespace App\Services;

use App\Repositories\Interfaces\WinderLogRepositoryInterface;
use App\Repositories\Interfaces\PaperMachineRollRepositoryInterface;
use App\Repositories\Interfaces\FinishedGoodRepositoryInterface;
use App\Events\WinderRollProduced;
use App\Models\PreOrder; // ✅ Import Model PreOrder
use App\Models\FinishedGood; // ✅ Import Model FinishedGood
use Exception;
use Illuminate\Support\Facades\DB;

class WinderLogService
{
    protected $winderLogRepo;
    protected $pmRollRepo;
    protected $finishedGoodRepo;

    public function __construct(
        WinderLogRepositoryInterface $winderLogRepo,
        PaperMachineRollRepositoryInterface $pmRollRepo,
        FinishedGoodRepositoryInterface $finishedGoodRepo
    ) {
        $this->winderLogRepo = $winderLogRepo;
        $this->pmRollRepo = $pmRollRepo;
        $this->finishedGoodRepo = $finishedGoodRepo;
    }

    public function getAllWinderLogs()
    {
        return $this->winderLogRepo->getAll();
    }

    public function getWinderLogById($id)
    {
        return $this->winderLogRepo->findById($id);
    }

    public function createWinderLog(array $data)
    {
        $pmRoll = $this->pmRollRepo->findById($data['paper_machine_roll_id']);
        if (!$pmRoll) {
            throw new Exception("Data Paper Machine Roll tidak ditemukan.");
        }

        if (isset($data['status']) && $data['status'] === 'done' && empty($data['wound_at'])) {
            $data['wound_at'] = now();
        }

        return DB::transaction(function () use ($data, $pmRoll) {
            $winderLog = $this->winderLogRepo->create($data);

            // TRIGGER OTOMATIS: Jika langsung selesai, masukkan ke Gudang & Update PO
            if ($winderLog->status === 'done') {
                $this->autoMoveToInventory($winderLog, $pmRoll->grade);
            }

            return $winderLog;
        });
    }

    public function updateWinderLog($id, array $data)
    {
        $winderLog = $this->winderLogRepo->findById($id);
        $wasAlreadyDone = $winderLog->status === 'done';

        if (isset($data['status']) && $data['status'] === 'done' && !$wasAlreadyDone) {
            $data['wound_at'] = $data['wound_at'] ?? now();
        }

        return DB::transaction(function () use ($id, $data, $wasAlreadyDone, $winderLog) {
            $updatedLog = $this->winderLogRepo->update($id, $data);

            // TRIGGER OTOMATIS: Jika operator mengubah status jadi selesai
            if ($updatedLog->status === 'done' && !$wasAlreadyDone) {
                $pmRoll = $this->pmRollRepo->findById($updatedLog->paper_machine_roll_id);
                $grade = $pmRoll ? $pmRoll->grade : null;
                
                $this->autoMoveToInventory($updatedLog, $grade);
            }

            return $updatedLog;
        });
    }

    public function deleteWinderLog($id)
    {
        return DB::transaction(function () use ($id) {
            return $this->winderLogRepo->delete($id);
        });
    }

    /**
     * Fungsi Internal: Memindahkan hasil potongan Winder langsung ke Gudang (Otomatis)
     * DAN MENGUPDATE STATUS PRE-ORDER! 🔥
     */
    protected function autoMoveToInventory($winderLog, $grade)
    {
        // 1. Simpan ke tabel FinishedGood (Inventori)
        $this->finishedGoodRepo->create([
            'winder_log_id'  => $winderLog->id,
            'roll_number'    => $winderLog->roll_number,
            'roll_weight'    => $winderLog->roll_weight,
            'width'          => $winderLog->width,
            'core_diameter'  => $winderLog->core_diameter,
            'grade'          => $grade, 
            'status'         => 'in_stock'
        ]);

        // ==========================================================
        // 🔥 2. LOGIKA OTOMATIS UPDATE STATUS PRE-ORDER
        // ==========================================================
        if (!empty($winderLog->po_number)) {
            // Cari data Pre-Order berdasarkan po_number yang sedang dikerjakan Winder
            $po = PreOrder::where('po_number', $winderLog->po_number)->first();

            if ($po) {
                // Hitung total berat roll (dari FinishedGood) yang nyangkut di PO ini
                $totalProduced = FinishedGood::whereHas('winderLog', function($query) use ($po) {
                    $query->where('po_number', $po->po_number);
                })->sum('roll_weight');

                // Tentukan status baru
                $newStatus = 'proses_pm'; // Default sedang jalan
                
                // Jika total yang diproduksi sudah mencapai atau melebihi target qty PO
                if ($totalProduced >= $po->target_qty) {
                    $newStatus = 'selesai_pm';
                }

                // Update status PO-nya!
                $po->update(['status' => $newStatus]);
            }
        }
        // ==========================================================

        // 3. Broadcast event agar Frontend Gudang bisa memunculkan notifikasi
        event(new WinderRollProduced($winderLog));
    }
}