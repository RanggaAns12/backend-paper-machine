<?php

namespace App\Services;

use App\Repositories\Interfaces\PreOrderRepositoryInterface;
use App\Models\FinishedGood; 

class PreOrderService
{
    protected PreOrderRepositoryInterface $preOrderRepository;

    // Inject Repository melalui Constructor
    public function __construct(PreOrderRepositoryInterface $preOrderRepository)
    {
        $this->preOrderRepository = $preOrderRepository;
    }

    // Mengambil semua PO yang masih aktif (Dilengkapi data Progres Tonase)
    public function getActiveOrders()
    {
        $orders = $this->preOrderRepository->getActiveOrders();

        // 1. Ambil koleksi datanya (Bisa dari Paginator atau Array biasa)
        $items = $orders instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator 
                 ? $orders->getCollection() 
                 : $orders;

        // 2. Gunakan EACH (bukan map) agar objek asli termodifikasi langsung
        $items->each(function ($po) {
            
            // Hitung total berat roll dari FinishedGood yang berkaitan dengan PO ini
            $terkumpul = FinishedGood::whereHas('winderLog', function($query) use ($po) {
                $query->where('po_number', $po->po_number);
            })->sum('roll_weight');

            // Hindari error pembagian dengan 0 jika target_qty kebetulan kosong
            $target = $po->target_qty > 0 ? $po->target_qty : 1;
            
            // Hitung persentase progres
            $persen = round(($terkumpul / $target) * 100);
            
            // Batasi maksimal 100% agar grafik progres bar di Angular tidak jebol
            if ($persen > 100) {
                $persen = 100;
            }

            // 🔥 3. INI KUNCINYA: Gunakan setAttribute agar Laravel wajib mengirimnya ke Angular!
            $po->setAttribute('terkumpul_kg', round($terkumpul));
            $po->setAttribute('progres_persen', $persen);
        });

        // 4. Kembalikan item yang sudah dimodifikasi ke dalam Paginator
        if ($orders instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
            $orders->setCollection($items);
        }

        return $orders;
    }

    // Menyimpan PO Baru
    public function createOrder(array $data)
    {
        // 🔥 PERBAIKAN: Gunakan 'PENDING' sesuai dengan database mas, bukan 'menunggu'
        if (!isset($data['status'])) {
            $data['status'] = 'PENDING';
        }
        
        return $this->preOrderRepository->create($data);
    }
}