<?php

namespace App\Http\Controllers\PaperMachine;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaperMachine\StorePaperMachineRollRequest;
use App\Http\Resources\PaperMachineRollResource;
use App\Services\PaperMachineReportService;
use App\Services\PaperMachineRollService;
use Illuminate\Http\JsonResponse;

class PaperMachineRollController extends Controller
{
    public function __construct(
        protected PaperMachineRollService $rollService,
        protected PaperMachineReportService $reportService
    ) {}

    public function store(StorePaperMachineRollRequest $request, int $reportId): JsonResponse
    {
        $report = $this->reportService->findReportById($reportId);
        if (!$report) {
            return response()->json(['success' => false, 'message' => 'Laporan tidak ditemukan.', 'data' => null, 'errors' => null], 404);
        }

        $this->rollService->addRollToReport($reportId, $request->validated());
        return response()->json(['success' => true, 'message' => 'Roll berhasil ditambahkan.', 'data' => null, 'errors' => null], 201);
    }

    public function update(StorePaperMachineRollRequest $request, int $id): JsonResponse
    {
        $roll = $this->rollService->findRollById($id);
        if (!$roll) {
            return response()->json(['success' => false, 'message' => 'Roll tidak ditemukan.', 'data' => null, 'errors' => null], 404);
        }

        $updated = $this->rollService->updateRoll($roll, $request->validated());
        return response()->json(['success' => true, 'message' => 'Roll berhasil diupdate.', 'data' => new PaperMachineRollResource($updated), 'errors' => null]);
    }

    public function destroy(int $id): JsonResponse
    {
        $roll = $this->rollService->findRollById($id);
        if (!$roll) {
            return response()->json(['success' => false, 'message' => 'Roll tidak ditemukan.', 'data' => null, 'errors' => null], 404);
        }

        $this->rollService->deleteRoll($roll);
        return response()->json(['success' => true, 'message' => 'Roll berhasil dihapus.', 'data' => null, 'errors' => null]);
    }
}
