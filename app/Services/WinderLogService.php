<?php

namespace App\Services;

use App\Repositories\Interfaces\WinderLogRepositoryInterface;

class WinderLogService
{
    protected $winderLogRepository;

    // Inject Repository melalui Constructor
    public function __construct(WinderLogRepositoryInterface $winderLogRepository)
    {
        $this->winderLogRepository = $winderLogRepository;
    }

    public function getAllLogs()
    {
        return $this->winderLogRepository->getAll();
    }

    public function getLogById(int $id)
    {
        return $this->winderLogRepository->getById($id);
    }

    public function createLog(array $data)
    {
        // Logika bisnis: Jika status "done" tapi wound_at kosong, otomatis isi dengan waktu sekarang
        if (isset($data['status']) && $data['status'] === 'done' && empty($data['wound_at'])) {
            $data['wound_at'] = now();
        }

        return $this->winderLogRepository->create($data);
    }

    public function updateLog(int $id, array $data)
    {
        $existingLog = $this->getLogById($id);
        
        // Logika bisnis saat update
        if (isset($data['status']) && $data['status'] === 'done' && $existingLog->status !== 'done' && empty($data['wound_at'])) {
            $data['wound_at'] = now();
        }

        return $this->winderLogRepository->update($id, $data);
    }

    public function deleteLog(int $id)
    {
        return $this->winderLogRepository->delete($id);
    }
}