<?php

namespace App\Repositories;

use App\Models\PreOrder;
use App\Repositories\Interfaces\PreOrderRepositoryInterface;

class PreOrderRepository implements PreOrderRepositoryInterface
{
    public function getActiveOrders()
    {
        return PreOrder::whereIn('status', ['PENDING', 'ON_PROGRESS'])
                       ->orderBy('target_delivery_date', 'asc')
                       ->get();
    }

    public function create(array $data)
    {
        $data['status'] = 'PENDING'; // Default status
        return PreOrder::create($data);
    }
}