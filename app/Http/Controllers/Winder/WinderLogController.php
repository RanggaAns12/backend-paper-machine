<?php

namespace App\Http\Controllers\Winder;

use App\Http\Controllers\Controller;
use App\Http\Requests\WinderLog\StoreWinderLogRequest;
use App\Http\Requests\WinderLog\UpdateWinderLogRequest;
use App\Http\Resources\WinderLogResource;
use App\Services\WinderLogService;
use Illuminate\Http\JsonResponse;

class WinderLogController extends Controller
{
    protected $winderLogService;

    // Inject Service
    public function __construct(WinderLogService $winderLogService)
    {
        $this->winderLogService = $winderLogService;
    }

    public function index(): JsonResponse
    {
        $logs = $this->winderLogService->getAllLogs();
        
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data Winder Log',
            // Gunakan Resource Collection untuk menyeragamkan format
            'data'    => WinderLogResource::collection($logs)
        ], 200);
    }

    public function store(StoreWinderLogRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $log = $this->winderLogService->createLog($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Data Winder Log berhasil disimpan',
            'data'    => new WinderLogResource($log)
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $log = $this->winderLogService->getLogById($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail data Winder Log',
            'data'    => new WinderLogResource($log)
        ], 200);
    }

    public function update(UpdateWinderLogRequest $request, $id): JsonResponse
    {
        $validatedData = $request->validated();
        $log = $this->winderLogService->updateLog($id, $validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Data Winder Log berhasil diperbarui',
            'data'    => new WinderLogResource($log)
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $this->winderLogService->deleteLog($id);

        return response()->json([
            'success' => true,
            'message' => 'Data Winder Log berhasil dihapus'
        ], 200);
    }
}