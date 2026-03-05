<?php

namespace App\Http\Controllers\PaperMachine;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaperMachine\StorePaperMachineReportRequest;
use App\Http\Requests\PaperMachine\UpdatePaperMachineReportRequest;
use App\Http\Resources\PaperMachineReportResource;
use App\Services\PaperMachineReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaperMachineReportController extends Controller
{
    public function __construct(protected PaperMachineReportService $reportService) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['date', 'grup', 'machine_id', 'operator_id']);

        $reports = $this->reportService->getAllReports($perPage, $filters);

        return response()->json([
            'success' => true,
            'message' => 'Data laporan berhasil diambil.',
            'data'    => PaperMachineReportResource::collection($reports)->response()->getData(true),
            'errors'  => null,
        ]);
    }


    public function store(StorePaperMachineReportRequest $request): JsonResponse
    {
        $report = $this->reportService->createReportWithDetails($request->validated(), $request->user()->id);
        return response()->json(['success' => true, 'message' => 'Laporan berhasil dibuat.', 'data' => new PaperMachineReportResource($report), 'errors' => null], 201);
    }

    public function show(int $id): JsonResponse
    {
        $report = $this->reportService->findReportById($id);
        if (!$report) {
            return response()->json(['success' => false, 'message' => 'Laporan tidak ditemukan.', 'data' => null, 'errors' => null], 404);
        }
        return response()->json(['success' => true, 'message' => 'Data laporan ditemukan.', 'data' => new PaperMachineReportResource($report), 'errors' => null]);
    }

    public function update(UpdatePaperMachineReportRequest $request, int $id): JsonResponse
    {
        $report = $this->reportService->findReportById($id);
        if (!$report) {
            return response()->json(['success' => false, 'message' => 'Laporan tidak ditemukan.', 'data' => null, 'errors' => null], 404);
        }
        $this->authorize('update', $report);
        $updated = $this->reportService->updateReport($report, $request->validated());
        return response()->json(['success' => true, 'message' => 'Laporan berhasil diupdate.', 'data' => new PaperMachineReportResource($updated), 'errors' => null]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $report = $this->reportService->findReportById($id);
        if (!$report) {
            return response()->json(['success' => false, 'message' => 'Laporan tidak ditemukan.', 'data' => null, 'errors' => null], 404);
        }
        $this->authorize('delete', $report);
        $this->reportService->deleteReport($report);
        return response()->json(['success' => true, 'message' => 'Laporan berhasil dihapus.', 'data' => null, 'errors' => null]);
    }
}
