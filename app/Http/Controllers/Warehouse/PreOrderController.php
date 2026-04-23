<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\PreOrder\StorePreOrderRequest;
use App\Services\PreOrderService;
use Illuminate\Http\JsonResponse;

class PreOrderController extends Controller
{
    protected PreOrderService $preOrderService;

    // ✅ Inject Service ke dalam Controller (Sesuai dengan Arsitektur Mas)
    public function __construct(PreOrderService $preOrderService)
    {
        $this->preOrderService = $preOrderService;
    }

    /**
     * Menampilkan daftar semua Pre-Order (PO) yang sedang aktif.
     * Digunakan untuk Tabel Laporan PO di Gudang & Dropdown di Winder.
     */
    public function index(): JsonResponse
    {
        // Memanggil logika dari Service -> Repository
        $orders = $this->preOrderService->getActiveOrders();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Data Pre-Order berhasil diambil',
            'data' => $orders
        ]);
    }

    /**
     * Menyimpan data Pre-Order (PO) baru dari form Frontend.
     */
    public function store(StorePreOrderRequest $request): JsonResponse
    {
        // Data sudah otomatis divalidasi oleh StorePreOrderRequest sebelum masuk ke sini
        $order = $this->preOrderService->createOrder($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Pre-Order berhasil dibuat dan masuk antrean produksi!',
            'data' => $order
        ], 201);
    }
    
    /**
     * Nanti jika Mas butuh fitur Cancel PO atau Lihat Detail PO, 
     * kita bisa tambahkan function show(), update(), atau destroy() di sini.
     * Untuk sekarang, index dan store sudah cukup untuk menjalankan alur utamanya.
     */
}