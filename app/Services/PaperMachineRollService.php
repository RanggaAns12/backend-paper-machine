<?php

namespace App\Services;

use App\Models\PaperMachineRoll;
use App\Repositories\Interfaces\PaperMachineRollRepositoryInterface;

class PaperMachineRollService
{
    public function __construct(
        protected PaperMachineRollRepositoryInterface $rollRepository
    ) {}

    public function addRollToReport(int $reportId, array $data): PaperMachineRoll
    {
        $payload = [
            'report_id'                 => $reportId,
            'no'                        => $data['no'] ?? null,
            'working_hour'              => $data['working_hour'] ?? null,
            'jrk_instruction'           => $data['jrk_instruction'] ?? null,
            'grade'                     => $data['grade'] ?? null,
            'roll_number'               => $data['roll_number'] ?? null,
            'speed_reel'                => $data['speed_reel'] ?? null,
            'tonase_roll'               => $data['tonase_roll'] ?? null,
            'width_cm'                  => $data['width_cm'] ?? null,
            'solid_starch_percent'      => $data['solid_starch_percent'] ?? null,
            'dry_strength_kg'           => $data['dry_strength_kg'] ?? null,
            'floc_l_per_h'              => $data['floc_l_per_h'] ?? null,
            'coag_l_per_h'              => $data['coag_l_per_h'] ?? null,
            'brown_ppm'                 => $data['brown_ppm'] ?? null,
            'brown_l_per_h'             => $data['brown_l_per_h'] ?? null,
            'yellow_ppm'                => $data['yellow_ppm'] ?? null,
            'yellow_l_per_h'            => $data['yellow_l_per_h'] ?? null,
            'red_ppm'                   => $data['red_ppm'] ?? null,
            'red_l_per_h'               => $data['red_l_per_h'] ?? null,
            
            'external_sizing_kg_per_tp' => $data['external_sizing_kg_per_tp'] ?? null,
            'pac_ml_per_m'              => $data['pac_ml_per_m'] ?? null,
        ];

        return PaperMachineRoll::create($payload)->fresh();
    }

    public function findRollById(int $id): ?PaperMachineRoll
    {
        return $this->rollRepository->findById($id);
    }

    public function updateRoll(PaperMachineRoll $roll, array $data): PaperMachineRoll
    {
        return $this->rollRepository->update($roll, $data)->fresh();
    }

    public function deleteRoll(PaperMachineRoll $roll): bool
    {
        return $this->rollRepository->delete($roll);
    }
}