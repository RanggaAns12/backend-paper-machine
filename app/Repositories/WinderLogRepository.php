<?php

namespace App\Repositories;

use App\Models\WinderLog;
use App\Repositories\Interfaces\WinderLogRepositoryInterface;

class WinderLogRepository implements WinderLogRepositoryInterface
{
    public function getAll()
    {
        // Mengambil semua data beserta relasinya agar tidak N+1 Query problem
        return WinderLog::with(['paperMachineRoll', 'operator'])->latest()->get();
    }

    public function findById($id)
    {
        return WinderLog::with(['paperMachineRoll', 'operator'])->findOrFail($id);
    }

    public function getByPaperMachineRollId($pmRollId)
    {
        return WinderLog::with('operator')
            ->where('paper_machine_roll_id', $pmRollId)
            ->get();
    }

    public function create(array $data)
    {
        return WinderLog::create($data);
    }

    public function update($id, array $data)
    {
        $winderLog = $this->findById($id);
        $winderLog->update($data);
        return $winderLog;
    }

    public function delete($id)
    {
        $winderLog = $this->findById($id);
        return $winderLog->delete();
    }
}