<?php

namespace App\Services;

use App\Models\QualityTest;
use App\Repositories\Interfaces\QualityTestRepositoryInterface;
use App\Repositories\Interfaces\PaperMachineRollRepositoryInterface; // ✅ IMPORT INI
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class QualityTestService
{
    // ✅ Suntikkan PM Roll Repository di Constructor
    public function __construct(
        protected QualityTestRepositoryInterface $qualityTestRepository,
        protected PaperMachineRollRepositoryInterface $pmRollRepository 
    ) {}

    public function getAllQualityTests(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->qualityTestRepository->getAllPaginated($perPage, $filters);
    }

    public function findQualityTestById(int $id): ?QualityTest
    {
        return $this->qualityTestRepository->findById($id);
    }

    public function createQualityTest(array $data, int $testedBy): QualityTest
    {
        // 1. Simpan Data Lab
        $test = $this->qualityTestRepository->create(
            array_merge($data, ['tested_by' => $testedBy])
        );

        // 2. 🔥 EFEK DOMINO MAJU: Update qc_status pada Jumbo Roll PM
        $pmRoll = $this->pmRollRepository->findById($data['paper_machine_roll_id']);
        if ($pmRoll) {
            $this->pmRollRepository->update($pmRoll, ['qc_status' => $data['status']]);
        }

        Log::info('Quality test created and Domino Effect triggered', ['test_id' => $test->id, 'qc_status' => $data['status']]);

        unset($data);

        return $test;
    }

    public function updateQualityTest(QualityTest $qualityTest, array $data): QualityTest
    {
        // 1. Update Data Lab
        $updated = $this->qualityTestRepository->update($qualityTest, $data);

        // 2. 🔥 EFEK DOMINO MAJU: Update qc_status jika statusnya ikut diedit
        if (isset($data['status'])) {
            $pmRoll = $this->pmRollRepository->findById($updated->paper_machine_roll_id);
            if ($pmRoll) {
                $this->pmRollRepository->update($pmRoll, ['qc_status' => $data['status']]);
            }
        }

        Log::info('Quality test updated', ['test_id' => $qualityTest->id]);

        unset($data);

        return $updated;
    }

    public function deleteQualityTest(QualityTest $qualityTest): bool
    {
        // Simpan ID roll sebelum test dihapus
        $rollId = $qualityTest->paper_machine_roll_id;
        
        $deleted = $this->qualityTestRepository->delete($qualityTest);

        // 3. 🔥 EFEK DOMINO MUNDUR: Kembalikan status roll ke 'pending' jika data QC dibatalkan/dihapus
        if ($deleted) {
            $pmRoll = $this->pmRollRepository->findById($rollId);
            if ($pmRoll) {
                $this->pmRollRepository->update($pmRoll, ['qc_status' => 'pending']);
            }
        }

        Log::info('Quality test deleted and Domino reversed', ['test_id' => $qualityTest->id]);

        return $deleted;
    }
}