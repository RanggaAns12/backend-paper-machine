<?php

namespace App\Services;

use App\Models\WinderLog;
use App\Repositories\Interfaces\WinderLogRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class WinderLogService
{
    public function __construct(protected WinderLogRepositoryInterface $winderLogRepository) {}

    public function getAllWinderLogs(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->winderLogRepository->getAllPaginated($perPage, $filters);
    }

    public function findWinderLogById(int $id): ?WinderLog
    {
        return $this->winderLogRepository->findById($id);
    }

    public function createWinderLog(array $data, int $operatorId): WinderLog
    {
        $log = $this->winderLogRepository->create(
            array_merge($data, ['operator_id' => $operatorId])
        );

        Log::info('Winder log created', ['log_id' => $log->id, 'operator_id' => $operatorId]);

        unset($data);

        return $log;
    }

    public function updateWinderLog(WinderLog $winderLog, array $data): WinderLog
    {
        $updated = $this->winderLogRepository->update($winderLog, $data);

        Log::info('Winder log updated', ['log_id' => $winderLog->id]);

        unset($data);

        return $updated;
    }

    public function deleteWinderLog(WinderLog $winderLog): bool
    {
        Log::info('Winder log deleted', ['log_id' => $winderLog->id]);

        return $this->winderLogRepository->delete($winderLog);
    }
}
