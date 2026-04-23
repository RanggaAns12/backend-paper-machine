<?php

namespace App\Services;

use App\Repositories\Interfaces\WinderLogRepositoryInterface;
use App\Repositories\Interfaces\PaperMachineRollRepositoryInterface;
use App\Repositories\Interfaces\FinishedGoodRepositoryInterface; //Import Repo Gudang
use App\Events\WinderRollProduced;
use Exception;
use Illuminate\Support\Facades\DB;

class WinderLogService
{
    protected $winderLogRepo;
    protected $pmRollRepo;
    protected $finishedGoodRepo; // Tambahkan property untuk Repo Gudang

    public function __construct(
        WinderLogRepositoryInterface $winderLogRepo,
        PaperMachineRollRepositoryInterface $pmRollRepo,
        FinishedGoodRepositoryInterface $finishedGoodRepo //Inject di sini
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

            // TRIGGER OTOMATIS: Jika langsung selesai, masukkan ke Gudang
            if ($winderLog->status === 'done') {
                $this->autoMoveToInventory($winderLog, $pmRoll->grade);
            }

            return $winderLog;
        });
    }

    public function updateWinderLog($id, array $data)
    {
        $winderLog = $this->winderLogRepo->findById($id);
        $wasAlreadyDone = $winderLog->status === 'done'; // Cek status sebelumnya

        if (isset($data['status']) && $data['status'] === 'done' && !$wasAlreadyDone) {
            $data['wound_at'] = $data['wound_at'] ?? now();
        }

        return DB::transaction(function () use ($id, $data, $wasAlreadyDone, $winderLog) {
            $updatedLog = $this->winderLogRepo->update($id, $data);

            // TRIGGER OTOMATIS: Jika operator mengubah status jadi selesai
            if ($updatedLog->status === 'done' && !$wasAlreadyDone) {
                // Ambil data Paper Machine Roll untuk mendapatkan Grade-nya
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
            'grade'          => $grade, // ✅ Grade dibawa dari Paper Machine Roll!
            'status'         => 'in_stock'
        ]);

        // 2. Broadcast event agar Frontend Gudang bisa memunculkan notifikasi "Ada Kertas Masuk"
        event(new WinderRollProduced($winderLog));
    }
}