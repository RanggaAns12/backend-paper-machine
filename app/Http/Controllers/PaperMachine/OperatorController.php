<?php

namespace App\Http\Controllers\PaperMachine;

use App\Http\Controllers\Controller;
use App\Http\Resources\OperatorResource;
use App\Services\OperatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OperatorController extends Controller
{
    public function __construct(protected OperatorService $operatorService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search']);
        $operators = $this->operatorService->getAllOperators($filters);

        return response()->json([
            'success' => true,
            'message' => 'Data operator berhasil diambil.',
            'data'    => OperatorResource::collection($operators),
            'errors'  => null,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $operator = $this->operatorService->createOperator($validated);

        return response()->json([
            'success' => true,
            'message' => 'Operator berhasil ditambahkan.',
            'data'    => new OperatorResource($operator),
            'errors'  => null,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $operator = $this->operatorService->findOperatorById($id);

        if (!$operator) {
            return response()->json(['success' => false, 'message' => 'Operator tidak ditemukan.', 'data' => null, 'errors' => null], 404);
        }

        return response()->json(['success' => true, 'message' => 'Data operator ditemukan.', 'data' => new OperatorResource($operator), 'errors' => null]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $operator = $this->operatorService->findOperatorById($id);

        if (!$operator) {
            return response()->json(['success' => false, 'message' => 'Operator tidak ditemukan.', 'data' => null, 'errors' => null], 404);
        }

        $validated = $request->validate([
            'name'      => ['sometimes', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $updated = $this->operatorService->updateOperator($operator, $validated);

        return response()->json(['success' => true, 'message' => 'Operator berhasil diupdate.', 'data' => new OperatorResource($updated), 'errors' => null]);
    }

    public function destroy(int $id): JsonResponse
    {
        $operator = $this->operatorService->findOperatorById($id);

        if (!$operator) {
            return response()->json(['success' => false, 'message' => 'Operator tidak ditemukan.', 'data' => null, 'errors' => null], 404);
        }

        $this->operatorService->deleteOperator($operator);

        return response()->json(['success' => true, 'message' => 'Operator berhasil dihapus.', 'data' => null, 'errors' => null]);
    }
}