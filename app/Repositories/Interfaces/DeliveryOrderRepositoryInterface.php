<?php

namespace App\Repositories\Interfaces;

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\FinishedGood;
use Illuminate\Pagination\LengthAwarePaginator;

interface DeliveryOrderRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;
    public function createDO(array $data): DeliveryOrder;
    public function findDOById($id): ?DeliveryOrder;
    public function findGoodByRollNumber(string $rollNumber): ?FinishedGood;
    public function addItemToDO(DeliveryOrder $do, FinishedGood $good): DeliveryOrderItem;
}