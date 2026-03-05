<?php

namespace App\Repositories\Interfaces;

use App\Models\QualityTest;
use Illuminate\Pagination\LengthAwarePaginator;

interface QualityTestRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findById(int $id): ?QualityTest;
    public function create(array $data): QualityTest;
    public function update(QualityTest $qualityTest, array $data): QualityTest;
    public function delete(QualityTest $qualityTest): bool;
}
