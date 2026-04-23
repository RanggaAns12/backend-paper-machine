<?php

namespace App\Services;

use App\Repositories\Interfaces\DeliveryOrderRepositoryInterface;
use App\Models\FinishedGood;
use App\Models\DeliveryOrder;
use Illuminate\Support\Facades\DB;
use Exception;

class DeliveryOrderService
{
    public function __construct(protected DeliveryOrderRepositoryInterface $repository) {}

    public function getAllDO($perPage = 15)
    {
        return $this->repository->getAllPaginated($perPage);
    }

    public function createDeliveryOrder(array $data, int $userId)
    {
        // Generate Nomor DO Otomatis: Lebih aman pakai microtime untuk hindari duplicate di detik yang sama
        $data['do_number']  = 'DO-' . date('Ymd') . '-' . substr(microtime(false), 2, 4);
        $data['created_by'] = $userId;
        $data['status']     = 'draft';

        return $this->repository->createDO($data);
    }

    // 🔥 FITUR UTAMA: Scan Barcode dan Muat ke Truk (Enterprise Edition)
    public function scanOutItem($doId, string $rollNumber)
    {
        return DB::transaction(function () use ($doId, $rollNumber) {
            
            // A. Kunci DO agar tidak ada yang mengubah status DO ini saat proses muat
            $do = DeliveryOrder::where('id', $doId)->lockForUpdate()->first();
            
            if (!$do) {
                throw new Exception("Surat Jalan tidak ditemukan.");
            }
            if ($do->status === 'shipped') {
                throw new Exception("Gagal: Surat Jalan ini sudah ditutup dan truk sudah berangkat.");
            }

            // B. Kunci Barang (Roll) agar tidak di-scan oleh Admin/DO lain
            $good = FinishedGood::where('roll_number', $rollNumber)->lockForUpdate()->first();
            
            if (!$good) {
                throw new Exception("Gagal: Roll nomor {$rollNumber} tidak ditemukan di gudang.");
            }
            if ($good->status !== 'in_stock') {
                throw new Exception("Gagal: Status barang saat ini adalah " . strtoupper($good->status) . ".");
            }

            // C. VALIDASI FIFO (First-In, First-Out) 🔥 SUDAH DIPERBAIKI
            // Cari roll kertas dengan Lebar dan GRADE yang SAMA, tapi diproduksi LEBIH LAMA
            $olderRollExists = FinishedGood::where('status', 'in_stock')
                ->where('width', $good->width) 
                ->where('grade', $good->grade) // ✅ Tambahkan filter Grade
                ->where('created_at', '<', $good->created_at) // Cari yang masuk gudang lebih dulu
                ->exists();

            if ($olderRollExists) {
                throw new Exception("PELANGGARAN FIFO: Masih ada Roll kertas ({$good->grade}, Lebar {$good->width}) yang masuk gudang lebih dulu. Harap ambil stok yang lebih lama terlebih dahulu.");
            }

            // D. Eksekusi Muat Barang (Simpan ke Item DO)
            $item = $this->repository->addItemToDO($do, $good);

            // E. Update Status
            // Karena barang sudah di-scan masuk truk, statusnya jadi shipped, otomatis hilang dari inventori
            $good->update(['status' => 'shipped']); 
            
            if ($do->status === 'draft') {
                $do->update(['status' => 'loading']);
            }

            // F. Update Total Tonase di DO Header
            $do->increment('total_tonase', $good->roll_weight);

            return $item;
        });
    }

    public function getDODetails($doId)
    {
        return $this->repository->findDOById($doId);
    }
}