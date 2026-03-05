<?php

namespace App\Services;

use App\Models\QualityTest;
use App\Repositories\Interfaces\QualityTestRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class QualityTestService
{
    public function __construct(protected QualityTestRepositoryInterface $qualityTestRepository) {}

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
        $test = $this->qualityTestRepository->create(
            array_merge($data, ['tested_by' => $testedBy])
        );

        Log::info('Quality test created', ['test_id' => $test->id, 'tested_by' => $testedBy]);

        unset($data);

        return $test;
    }

    public function updateQualityTest(QualityTest $qualityTest, array $data): QualityTest
    {
        $updated = $this->qualityTestRepository->update($qualityTest, $data);

        Log::info('Quality test updated', ['test_id' => $qualityTest->id]);

        unset($data);

        return $updated;
    }

    public function deleteQualityTest(QualityTest $qualityTest): bool
    {
        Log::info('Quality test deleted', ['test_id' => $qualityTest->id]);

        return $this->qualityTestRepository->delete($qualityTest);
    }
}
