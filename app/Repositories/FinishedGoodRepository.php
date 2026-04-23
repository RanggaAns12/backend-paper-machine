<?php

namespace App\Repositories;

use App\Models\FinishedGood;
use App\Models\WinderLog;
use App\Repositories\Interfaces\FinishedGoodRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class FinishedGoodRepository implements FinishedGoodRepositoryInterface
{
    public function __construct(protected FinishedGood $model) {}

    // Fungsi getInboundQueue() sudah dihapus dari sini ❌

    // Mengambil daftar stok yang sedang ada di dalam gudang (In Stock)
    public function getInStockPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with('winderLog.paperMachineRoll:id,grade')
            ->where('status', 'in_stock')
            ->latest()
            ->paginate($perPage);
    }

    // Menyimpan data roll ke dalam gudang
    public function create(array $data): FinishedGood
    {
        return $this->model->create($data);
    }
}