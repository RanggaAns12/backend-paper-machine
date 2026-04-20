<?php

namespace App\Services;

use App\Repositories\Interfaces\WinderLogRepositoryInterface;
use App\Repositories\Interfaces\PaperMachineRollRepositoryInterface;
use App\Events\WinderRollProduced; // ✅ Import Event yang baru kita buat
use Exception;
use Illuminate\Support\Facades\DB;

class WinderLogService
{
    protected $winderLogRepo;
    protected $pmRollRepo;

    public function __construct(
        WinderLogRepositoryInterface $winderLogRepo,
        PaperMachineRollRepositoryInterface $pmRollRepo
    ) {
        $this->winderLogRepo = $winderLogRepo;
        $this->pmRollRepo = $pmRollRepo;
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

        return DB::transaction(function () use ($data) {
            $winderLog = $this->winderLogRepo->create($data);

            // 🔥 REAL-TIME TRIGGER: Jika roll Winder langsung selesai, tembak sinyal ke Gudang!
            if ($winderLog->status === 'done') {
                event(new WinderRollProduced($winderLog));
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

        return DB::transaction(function () use ($id, $data, $wasAlreadyDone) {
            $updatedLog = $this->winderLogRepo->update($id, $data);

            // 🔥 REAL-TIME TRIGGER: Jika operator mengupdate status menjadi 'done', tembak sinyal ke Gudang!
            // Syaratnya: Sebelumnya belum 'done', supaya tidak nyepam/dobel sinyal.
            if ($updatedLog->status === 'done' && !$wasAlreadyDone) {
                event(new WinderRollProduced($updatedLog));
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
}