<?php

namespace App\Repositories;

use App\Models\WinderLog;
use App\Repositories\Interfaces\WinderLogRepositoryInterface;

class WinderLogRepository implements WinderLogRepositoryInterface
{
    public function getAll()
    {
        // Memuat relasi report dan operator untuk mencegah N+1 Query
        return WinderLog::with(['report', 'operator'])->latest()->get();
    }

    public function getById(int $id)
    {
        return WinderLog::with(['report', 'operator'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return WinderLog::create($data);
    }

    public function update(int $id, array $data)
    {
        $log = $this->getById($id);
        $log->update($data);
        return $log;
    }

    public function delete(int $id)
    {
        $log = $this->getById($id);
        return $log->delete();
    }
}