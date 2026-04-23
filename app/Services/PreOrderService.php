<?php

namespace App\Services;

use App\Repositories\Interfaces\PreOrderRepositoryInterface;

class PreOrderService
{
    protected PreOrderRepositoryInterface $preOrderRepository;

    // Inject Repository melalui Constructor
    public function __construct(PreOrderRepositoryInterface $preOrderRepository)
    {
        $this->preOrderRepository = $preOrderRepository;
    }

    // Mengambil semua PO yang masih aktif
    public function getActiveOrders()
    {
        return $this->preOrderRepository->getActiveOrders();
    }

    // Menyimpan PO Baru
    public function createOrder(array $data)
    {
        return $this->preOrderRepository->create($data);
    }
}