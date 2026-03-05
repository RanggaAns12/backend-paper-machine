<?php

namespace App\Http\Controllers\Winder;

use App\Http\Controllers\Controller;
use App\Http\Requests\WinderLog\StoreWinderLogRequest;
use App\Http\Requests\WinderLog\UpdateWinderLogRequest;
use App\Http\Resources\WinderLogResource;
use App\Services\WinderLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WinderLogController extends Controller
{
    public function __construct(protected WinderLogService $winderLogService) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['status', 'report_id', 'operator_id']);

        $logs = $this->winderLogService->getAllWinderLogs($perPage, $filters);

        unset($filters);

        return response()->json([
            'success' => true,
            'message' => 'Data winder log berhasil diambil.',
            'data'    => WinderLogResource::collection($logs)->response()->getData(true),
            'errors'  => null,
        ]);
    }

    public function store(StoreWinderLogRequest $request): JsonResponse
    {
        $log = $this->winderLogService->createWinderLog(
            $request->validated(),
            $request->user()->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Winder log berhasil dibuat.',
            'data'    => new WinderLogResource($log),
            'errors'  => null,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $log = $this->winderLogService->findWinderLogById($id);

        if (!$log) {
            return response()->json([
                'success' => false,
                'message' => 'Winder log tidak ditemukan.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data winder log ditemukan.',
            'data'    => new WinderLogResource($log),
            'errors'  => null,
        ]);
    }

    public function update(UpdateWinderLogRequest $request, int $id): JsonResponse
    {
        $log = $this->winderLogService->findWinderLogById($id);

        if (!$log) {
            return response()->json([
                'success' => false,
                'message' => 'Winder log tidak ditemukan.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        $this->authorize('update', $log);

        $updated = $this->winderLogService->updateWinderLog($log, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Winder log berhasil diupdate.',
            'data'    => new WinderLogResource($updated),
            'errors'  => null,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $log = $this->winderLogService->findWinderLogById($id);

        if (!$log) {
            return response()->json([
                'success' => false,
                'message' => 'Winder log tidak ditemukan.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        $this->authorize('delete', $log);
        $this->winderLogService->deleteWinderLog($log);

        return response()->json([
            'success' => true,
            'message' => 'Winder log berhasil dihapus.',
            'data'    => null,
            'errors'  => null,
        ]);
    }
}
