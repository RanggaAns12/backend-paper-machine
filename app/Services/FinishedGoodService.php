<?php

namespace App\Services;

use App\Repositories\FinishedGoodRepository;
use App\Models\WinderLog;
use App\Models\FinishedGood;
use Illuminate\Support\Facades\DB;
use Exception;

class FinishedGoodService
{
    public function __construct(protected FinishedGoodRepository $repository) {}

    public function getInboundQueue()
    {
        return $this->repository->getInboundQueue();
    }

    public function getInStock($perPage = 15)
    {
        return $this->repository->getInStockPaginated($perPage);
    }

    public function receiveGood(array $data)
    {
        // 🔥 ENTERPRISE STANDARD: Bungkus dalam DB Transaction
        return DB::transaction(function () use ($data) {
            
            // 1. PESSIMISTIC LOCKING: Kunci baris WinderLog ini selama proses berjalan
            // Admin lain yang mencoba men-scan roll ini di detik yang sama akan disuruh menunggu hingga proses ini selesai.
            $winderLog = WinderLog::where('id', $data['winder_log_id'])->lockForUpdate()->first();
            
            if (!$winderLog || $winderLog->status !== 'done') {
                throw new Exception("Roll belum selesai diproses di mesin Winder, tidak ditemukan, atau sudah di-scan orang lain.");
            }

            // 2. DOUBLE CHECK: Pastikan roll ini belum pernah masuk ke gudang sama sekali
            $isAlreadyInWarehouse = FinishedGood::where('winder_log_id', $winderLog->id)->exists();
            if ($isAlreadyInWarehouse) {
                throw new Exception("Roll ini sudah pernah di-scan dan sudah berada di dalam Gudang!");
            }

            // 3. Mapping Data
            $finishedGoodData = [
                'winder_log_id'  => $winderLog->id,
                'roll_number'    => $winderLog->roll_number,
                'roll_weight'    => $winderLog->roll_weight,
                'width'          => $winderLog->width,
                'core_diameter'  => $winderLog->core_diameter,
                
                // Format Blok menjadi huruf besar, Jalur jadi 2 digit
                'location_block' => strtoupper(trim($data['location_block'])),
                'location_line'  => str_pad(trim($data['location_line']), 2, '0', STR_PAD_LEFT),
                
                'status'         => 'in_stock'
            ];

            // 4. Simpan ke Gudang
            $good = $this->repository->create($finishedGoodData);

            // 5. UPDATE STATUS WINDER: Agar hilang dari antrean Inbound
            $winderLog->update(['status' => 'in_warehouse']);

            return $good;
        });
    }
}