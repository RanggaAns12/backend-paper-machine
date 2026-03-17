<?php

namespace App\Repositories\Interfaces;

use App\Models\Operator;
use Illuminate\Database\Eloquent\Collection;

interface OperatorRepositoryInterface
{
    public function getAll(array $filters = []): Collection;
    public function findById(int $id): ?Operator;
    public function create(array $data): Operator;
    public function update(Operator $operator, array $data): Operator;
    public function delete(Operator $operator): bool;
}