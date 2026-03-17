<?php

namespace App\Services;

use App\Models\Operator;
use App\Repositories\Interfaces\OperatorRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class OperatorService
{
    public function __construct(protected OperatorRepositoryInterface $operatorRepository) {}

    public function getAllOperators(array $filters = []): Collection
    {
        return $this->operatorRepository->getAll($filters);
    }

    public function findOperatorById(int $id): ?Operator
    {
        return $this->operatorRepository->findById($id);
    }

    public function createOperator(array $data): Operator
    {
        return $this->operatorRepository->create($data);
    }

    public function updateOperator(Operator $operator, array $data): Operator
    {
        return $this->operatorRepository->update($operator, $data);
    }

    public function deleteOperator(Operator $operator): bool
    {
        return $this->operatorRepository->delete($operator);
    }
}