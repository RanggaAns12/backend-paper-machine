<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\PreOrder\StorePreOrderRequest;
use App\Services\PreOrderService;
use Illuminate\Http\JsonResponse;

class PreOrderController extends Controller
{
    protected PreOrderService $preOrderService;

    public function __construct(PreOrderService $preOrderService)
    {
        $this->preOrderService = $preOrderService;
    }

    public function index(): JsonResponse
    {
        $orders = $this->preOrderService->getActiveOrders();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Data PO berhasil diambil',
            'data' => $orders
        ]);
    }

    public function store(StorePreOrderRequest $request): JsonResponse
    {
        $order = $this->preOrderService->createOrder($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Pre-Order berhasil dibuat',
            'data' => $order
        ], 201);
    }
}