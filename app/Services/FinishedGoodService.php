<?php

namespace App\Services;

use App\Repositories\FinishedGoodRepository;

class FinishedGoodService
{
    public function __construct(protected FinishedGoodRepository $repository) {}

    // Mengambil daftar stok yang tersedia di gudang
    public function getInStock($perPage = 15)
    {
        return $this->repository->getInStockPaginated($perPage);
    }

    // 🔥 Fungsi getInboundQueue() dan receiveGood() sudah dihapus 
    // karena barang otomatis masuk dari mesin Winder.
}