<?php

namespace App\Http\Controllers\PaperMachine;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaperMachine\StorePaperMachineProblemRequest;
use App\Http\Resources\PaperMachineProblemResource;
use App\Services\PaperMachineProblemService;
use App\Services\PaperMachineReportService;
use Illuminate\Http\JsonResponse;

class PaperMachineProblemController extends Controller
{
    public function __construct(
        protected PaperMachineProblemService $problemService,
        protected PaperMachineReportService $reportService
    ) {}

    public function store(StorePaperMachineProblemRequest $request, int $reportId): JsonResponse
    {
        $report = $this->reportService->findReportById($reportId);
        if (!$report) {
            return response()->json(['success' => false, 'message' => 'Laporan tidak ditemukan.', 'data' => null, 'errors' => null], 404);
        }

        $this->problemService->addProblemToReport($reportId, $request->validated());
        return response()->json(['success' => true, 'message' => 'Problem berhasil ditambahkan.', 'data' => null, 'errors' => null], 201);
    }

    public function update(StorePaperMachineProblemRequest $request, int $id): JsonResponse
    {
        $problem = $this->problemService->findProblemById($id);
        if (!$problem) {
            return response()->json(['success' => false, 'message' => 'Problem tidak ditemukan.', 'data' => null, 'errors' => null], 404);
        }

        $updated = $this->problemService->updateProblem($problem, $request->validated());
        return response()->json(['success' => true, 'message' => 'Problem berhasil diupdate.', 'data' => new PaperMachineProblemResource($updated), 'errors' => null]);
    }

    public function destroy(int $id): JsonResponse
    {
        $problem = $this->problemService->findProblemById($id);
        if (!$problem) {
            return response()->json(['success' => false, 'message' => 'Problem tidak ditemukan.', 'data' => null, 'errors' => null], 404);
        }

        $this->problemService->deleteProblem($problem);
        return response()->json(['success' => true, 'message' => 'Problem berhasil dihapus.', 'data' => null, 'errors' => null]);
    }
}
