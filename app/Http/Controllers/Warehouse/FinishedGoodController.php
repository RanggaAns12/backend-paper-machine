<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Services\FinishedGoodService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FinishedGoodController extends Controller
{
    public function __construct(protected FinishedGoodService $finishedGoodService) {}

    public function getQueue(): JsonResponse
    {
        $queue = $this->finishedGoodService->getInboundQueue();
        return response()->json(['success' => true, 'data' => $queue]);
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $stock = $this->finishedGoodService->getInStock($perPage);
        return response()->json(['success' => true, 'data' => $stock]);
    }

    public function receive(Request $request): JsonResponse
    {
        // 🔥 PERBAIKAN: Validasi input hanya Blok dan Jalur
        $validated = $request->validate([
            'winder_log_id'  => 'required|exists:winder_logs,id',
            'location_block' => 'required|string|max:10',
            'location_line'  => 'required|string|max:10',
        ]);

        try {
            $good = $this->finishedGoodService->receiveGood($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil diterima dan ditempatkan di Jalur Gudang.',
                'data'    => $good
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}