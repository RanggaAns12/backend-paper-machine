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
            // Asumsi roll_number adalah field yang dicari
            $good = FinishedGood::where('roll_number', $rollNumber)->lockForUpdate()->first();
            
            if (!$good) {
                throw new Exception("Gagal: Roll nomor {$rollNumber} tidak ditemukan di gudang.");
            }
            if ($good->status !== 'in_stock') {
                throw new Exception("Gagal: Status barang saat ini adalah " . strtoupper($good->status) . ".");
            }

            // C. VALIDASI FIFO (First-In, First-Out)
            // Cari apakah ada roll kertas dengan GSM dan Lebar yang SAMA, tapi diproduksi LEBIH LAMA dari roll yang di-scan ini.
            // Catatan: Jika Mas menyimpan GSM dan Lebar di tabel WinderLog (bukan FinishedGood),
            // maka Mas harus me-load relasi winderLog terlebih dahulu ($good->load('winderLog')).
            $olderRollExists = FinishedGood::where('status', 'in_stock')
                ->where('width', $good->width) // Bandingkan dengan spek yang sama
                // ->where('gsm', $good->gsm) // Uncomment jika kolom gsm ada di tabel ini
                ->where('created_at', '<', $good->created_at) // Cari yang lebih lama
                ->exists();

            if ($olderRollExists) {
                // Di sistem Enterprise, biasanya ini dikembalikan sebagai Warning (bukan Exception mutlak), 
                // tapi supir harus menginput "Alasan Override" (misal: "Barang tertimpa rak").
                // Namun untuk keamanan awal, kita tolak transaksinya.
                throw new Exception("PELANGGARAN FIFO: Masih ada Roll kertas dengan spesifikasi yang sama namun diproduksi lebih lama. Harap ambil stok yang lebih lama terlebih dahulu.");
            }

            // D. Eksekusi Muat Barang (Simpan ke Item DO)
            $item = $this->repository->addItemToDO($do, $good);

            // E. Update Status (Good -> Shipped, DO -> Loading)
            $good->update(['status' => 'shipped']); // Atau 'booked_for_do' jika DO belum dikunci
            
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