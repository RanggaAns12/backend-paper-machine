<?php

namespace App\Services;

use App\Repositories\FinishedGoodRepository;
use Exception;

class FinishedGoodService
{
    public function __construct(protected FinishedGoodRepository $repository) {}

    // Mengambil daftar stok yang tersedia di gudang
    public function getInStock($perPage = 15)
    {
        return $this->repository->getInStockPaginated($perPage);
    }

    /**
     * Mengecek apakah barcode (roll) tersedia di gudang untuk Outbound.
     *
     * @param string $barcode
     * @return mixed
     * @throws Exception
     */
    public function checkAvailableBarcode(string $barcode)
    {
        // 1. Ambil data roll dari database melalui Repository
        // Nanti pastikan method findByRollNumber ini ada di FinishedGoodRepository ya mas
        $roll = $this->repository->findByRollNumber($barcode);

        // 2. Validasi 1: Apakah barang sama sekali tidak terdaftar?
        if (!$roll) {
            throw new Exception("Roll dengan barcode {$barcode} tidak ditemukan di sistem database.");
        }

        // 3. Validasi 2: Apakah status barang benar-benar ada di gudang?
        // Catatan: Tolong sesuaikan 'in_warehouse' dengan value status yang mas pakai di database 
        // (misal: 'in_stock', 'gudang', dll)
        if ($roll->status !== 'in_stock') {
            throw new Exception("Roll {$barcode} tidak bisa dimuat. Status saat ini: {$roll->status}");
        }

        // Jika semua validasi lolos, kembalikan data asli roll ke Controller
        return $roll;
    }
}