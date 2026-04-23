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
}