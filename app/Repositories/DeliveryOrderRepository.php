<?php

namespace App\Repositories;

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\FinishedGood;
use App\Repositories\Interfaces\DeliveryOrderRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class DeliveryOrderRepository implements DeliveryOrderRepositoryInterface
{
    public function __construct(
        protected DeliveryOrder $doModel,
        protected DeliveryOrderItem $doItemModel,
        protected FinishedGood $finishedGoodModel
    ) {}

    // 1. Mengambil riwayat Surat Jalan untuk tabel di halaman Outbound
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->doModel
            ->with('creator:id,name') // Ambil nama admin yang membuat
            ->latest()
            ->paginate($perPage);
    }

    // 2. Membuat header/draft Surat Jalan baru
    public function createDO(array $data): DeliveryOrder
    {
        return $this->doModel->create($data);
    }

    // 3. Melihat isi lengkap muatan truk berdasarkan ID Surat Jalan
    public function findDOById($id): ?DeliveryOrder
    {
        return $this->doModel->with([
            'items.finishedGood', // Ambil item beserta detail roll fisik kertasnya
            'creator:id,name'
        ])->find($id);
    }

    // 4. API UNTUK SCANNER BARCODE: Cari roll kertas berdasarkan nomor barcode-nya
    public function findGoodByRollNumber(string $rollNumber): ?FinishedGood
    {
        return $this->finishedGoodModel->where('roll_number', $rollNumber)->first();
    }

    // 5. EKSEKUSI MUAT BARANG: Memasukkan roll ke truk dan mengubah status di gudang
    public function addItemToDO(DeliveryOrder $do, FinishedGood $good): DeliveryOrderItem
    {
        // A. Catat roll ini masuk ke Surat Jalan yang mana
        $item = $this->doItemModel->create([
            'delivery_order_id' => $do->id,
            'finished_good_id'  => $good->id,
            'shipped_weight'    => $good->roll_weight, // Rekam berat real saat naik truk
        ]);

        // B. Tambahkan berat roll ke Total Tonase truk
        $do->increment('total_tonase', $good->roll_weight);

        // C. Hapus barang dari tampilan stok gudang (Ubah status jadi shipped)
        // Lokasi lantai (Block/Line) juga kita kosongkan karena barangnya sudah pergi
        $good->update([
            'status'         => 'shipped',
            'location_block' => null, 
            'location_line'  => null
        ]);

        return $item;
    }
}