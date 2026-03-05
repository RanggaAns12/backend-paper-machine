<?php

namespace App\Repositories\Interfaces;

use App\Models\WinderLog;
use Illuminate\Pagination\LengthAwarePaginator;

interface WinderLogRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findById(int $id): ?WinderLog;
    public function create(array $data): WinderLog;
    public function update(WinderLog $winderLog, array $data): WinderLog;
    public function delete(WinderLog $winderLog): bool;
}
