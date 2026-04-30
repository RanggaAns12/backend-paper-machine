<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Services\FinishedGoodService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FinishedGoodController extends Controller
{
    public function __construct(protected FinishedGoodService $finishedGoodService) {}

    /**
     * Mengambil daftar stok yang saat ini tersedia di gudang.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $stock = $this->finishedGoodService->getInStock($perPage);
        
        return response()->json([
            'success' => true, 
            'data'    => $stock
        ]);
    }

    /**
     * Mengecek ketersediaan barcode (roll) di gudang saat discan untuk Outbound.
     * * @param string $barcode
     * @return JsonResponse
     */
    public function checkBarcode(string $barcode): JsonResponse
    {
        try {
            $roll = $this->finishedGoodService->checkAvailableBarcode($barcode);

            // Siapkan data yang akan dikirim ke Angular
            $rollData = [
                'roll_number' => $roll->roll_number,
                'roll_weight' => $roll->roll_weight,
                'grade'       => $roll->grade
            ];

            // BROADCAST KE PERANGKAT LAIN:
            // auth()->id() memastikan data hanya dikirim ke channel milik user yang sedang login
            // toOthers() memastikan perangkat yang melakukan scan (HP) tidak menerima notifikasi ganda
            broadcast(new \App\Events\RollScannedToCart(auth()->id(), $rollData))->toOthers();

            return response()->json([
                'success' => true,
                'message' => 'Barang tersedia di gudang.',
                'data'    => $rollData // Kita kembalikan array yang rapi
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Memberi tahu perangkat lain bahwa sebuah roll dihapus dari keranjang truk.
     */
    public function cancelScan(\Illuminate\Http\Request $request): JsonResponse
    {
        $barcode = $request->input('barcode');

        // Pancarkan sinyal ke perangkat lain (Laptop/HP) untuk ikut menghapus
        broadcast(new \App\Events\RollRemovedFromCart(auth()->id(), $barcode))->toOthers();

        return response()->json(['success' => true]);
    }
}