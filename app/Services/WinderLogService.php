<?php

namespace App\Services;

use App\Repositories\Interfaces\WinderLogRepositoryInterface;
use App\Repositories\Interfaces\PaperMachineRollRepositoryInterface;
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
        // Pastikan Jumbo Roll (Paper Machine Roll) valid dan ada di database
        $pmRoll = $this->pmRollRepo->findById($data['paper_machine_roll_id']);
        if (!$pmRoll) {
            throw new Exception("Data Paper Machine Roll tidak ditemukan.");
        }

        // Jika statusnya 'done' saat di-create, otomatis catat waktu wound_at
        if (isset($data['status']) && $data['status'] === 'done' && empty($data['wound_at'])) {
            $data['wound_at'] = now();
        }

        return DB::transaction(function () use ($data) {
            return $this->winderLogRepo->create($data);
        });
    }

    public function updateWinderLog($id, array $data)
    {
        $winderLog = $this->winderLogRepo->findById($id);

        // Auto-fill wound_at jika status berubah menjadi done
        if (isset($data['status']) && $data['status'] === 'done' && $winderLog->status !== 'done') {
            $data['wound_at'] = $data['wound_at'] ?? now();
        }

        return DB::transaction(function () use ($id, $data) {
            return $this->winderLogRepo->update($id, $data);
        });
    }

    public function deleteWinderLog($id)
    {
        return DB::transaction(function () use ($id) {
            return $this->winderLogRepo->delete($id);
        });
    }
}