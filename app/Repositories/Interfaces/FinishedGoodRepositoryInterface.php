<?php

namespace App\Repositories\Interfaces;

use App\Models\FinishedGood;
use Illuminate\Pagination\LengthAwarePaginator;

interface FinishedGoodRepositoryInterface
{
    public function getInboundQueue();
    public function getInStockPaginated(int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): FinishedGood;
}