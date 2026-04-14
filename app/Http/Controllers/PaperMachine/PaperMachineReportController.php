<?php

namespace App\Http\Controllers\PaperMachine;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaperMachine\StorePaperMachineReportRequest;
use App\Http\Requests\PaperMachine\UpdatePaperMachineReportRequest;
use App\Http\Resources\PaperMachineReportResource;
use App\Services\PaperMachineReportService;
use Illuminate\Http\JsonResponse;
use Throwable;

class PaperMachineReportController extends Controller
{
    protected PaperMachineReportService $reportService;

    public function __construct(PaperMachineReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(): JsonResponse
    {
        try {
            $filters = request()->only(['date', 'grup', 'search']);
            $perPage = (int) request()->get('per_page', 15);

            // 1. Ambil data dari Service (Berupa Paginator)
            $reports = $this->reportService->getAllReports($filters, $perPage);

            // 2. ✅ PERBAIKAN UTAMA: Paksa Laravel mengambil data relasi (Eager Loading)
            // Ini memastikan data operator, roll, dan problems ikut terbawa untuk Angular
            $reports->load(['operator', 'rolls', 'problems']);

            return response()->json([
                'success' => true,
                'message' => 'Reports retrieved successfully',
                
                // 3. ✅ PERBAIKAN UTAMA: Kita gunakan $reports->items() untuk mendapatkan array aslinya.
                // Jika sebelumnya menggunakan PaperMachineReportResource::collection($reports), 
                // ada kemungkinan Resource mas "memblokir/menyembunyikan" data relasi tersebut.
                'data' => $reports->items(), 
                
                'meta' => [
                    'current_page' => $reports->currentPage(),
                    'last_page' => $reports->lastPage(),
                    'per_page' => $reports->perPage(),
                    'total' => $reports->total(),
                ],
                'errors' => null,
            ], 200);
        } catch (Throwable $e) {
            return $this->serverErrorResponse('Failed to retrieve reports', $e);
        }
    }

    public function store(StorePaperMachineReportRequest $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'data' => null,
                    'errors' => null,
                ], 401);
            }

            $report = $this->reportService->createReportWithDetails(
                $request->validated(),
                (int) $user->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Report created successfully',
                'data' => new PaperMachineReportResource($report),
                'errors' => null,
            ], 201);
        } catch (Throwable $e) {
            return $this->serverErrorResponse('Failed to create report', $e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $report = $this->reportService->getReportById($id);

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Report not found',
                    'data' => null,
                    'errors' => null,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Report retrieved successfully',
                'data' => new PaperMachineReportResource($report),
                'errors' => null,
            ], 200);
        } catch (Throwable $e) {
            return $this->serverErrorResponse('Failed to retrieve report', $e);
        }
    }

    public function update(UpdatePaperMachineReportRequest $request, int $id): JsonResponse
    {
        try {
            $report = $this->reportService->getReportById($id);

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Report not found',
                    'data' => null,
                    'errors' => null,
                ], 404);
            }

            $validated = $request->validated();
            $updatedReport = $this->reportService->updateReport($report, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Report updated successfully',
                'data' => new PaperMachineReportResource($updatedReport),
                'errors' => null,
            ], 200);
        } catch (Throwable $e) {
            return $this->serverErrorResponse('Failed to update report', $e);
        }
    }

    public function unlock(int $id): JsonResponse
    {
        try {
            $user = request()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'data' => null,
                    'errors' => null,
                ], 401);
            }

            if (($user->role ?? null) !== 'superadmin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only super admin can unlock reports.',
                    'data' => null,
                    'errors' => null,
                ], 403);
            }

            $report = $this->reportService->unlockReport($id);

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Report not found',
                    'data' => null,
                    'errors' => null,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Report unlocked successfully',
                'data' => new PaperMachineReportResource($report),
                'errors' => null,
            ], 200);
        } catch (Throwable $e) {
            return $this->serverErrorResponse('Failed to unlock report', $e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $report = $this->reportService->getReportById($id);

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Report not found',
                    'data' => null,
                    'errors' => null,
                ], 404);
            }

            if ((bool) $report->is_locked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete locked report. Unlock first.',
                    'data' => null,
                    'errors' => null,
                ], 403);
            }

            $deleted = $this->reportService->deleteReport($report);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete report',
                    'data' => null,
                    'errors' => null,
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Report deleted successfully',
                'data' => null,
                'errors' => null,
            ], 200);
        } catch (Throwable $e) {
            return $this->serverErrorResponse('Failed to delete report', $e);
        }
    }

    private function serverErrorResponse(string $message, Throwable $e): JsonResponse
    {
        report($e);

        return response()->json([
            'success' => false,
            'message' => $message . ': ' . $e->getMessage(),
            'data' => null,
            'errors' => null,
        ], 500);
    }
}