<?php

namespace App\Http\Controllers\PaperMachine;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaperMachine\StorePaperMachineRollRequest;
use App\Http\Resources\PaperMachineRollResource;
use App\Services\PaperMachineReportService;
use App\Services\PaperMachineRollService;
use Illuminate\Http\JsonResponse;
use Throwable;

class PaperMachineRollController extends Controller
{
    public function __construct(
        protected PaperMachineRollService $rollService,
        protected PaperMachineReportService $reportService
    ) {}

    public function store(StorePaperMachineRollRequest $request, int $reportId): JsonResponse
    {
        try {
            $report = $this->reportService->getReportById($reportId);

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan tidak ditemukan.',
                    'data' => null,
                    'errors' => null
                ], 404);
            }

            if ((bool) $report->is_locked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan sudah dikunci. Tidak dapat menambah roll.',
                    'data' => null,
                    'errors' => null
                ], 403);
            }

            $createdRoll = $this->rollService->addRollToReport($reportId, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Roll berhasil ditambahkan.',
                'data' => new PaperMachineRollResource($createdRoll),
                'errors' => null
            ], 201);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan roll: ' . $e->getMessage(),
                'data' => null,
                'errors' => null
            ], 500);
        }
    }

    public function update(StorePaperMachineRollRequest $request, int $id): JsonResponse
    {
        try {
            $roll = $this->rollService->findRollById($id);

            if (!$roll) {
                return response()->json([
                    'success' => false,
                    'message' => 'Roll tidak ditemukan.',
                    'data' => null,
                    'errors' => null
                ], 404);
            }

            $report = $roll->report ?? null;
            if ($report && (bool) $report->is_locked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan sudah dikunci. Tidak dapat mengubah roll.',
                    'data' => null,
                    'errors' => null
                ], 403);
            }

            $updated = $this->rollService->updateRoll($roll, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Roll berhasil diupdate.',
                'data' => new PaperMachineRollResource($updated),
                'errors' => null
            ], 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate roll: ' . $e->getMessage(),
                'data' => null,
                'errors' => null
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $roll = $this->rollService->findRollById($id);

            if (!$roll) {
                return response()->json([
                    'success' => false,
                    'message' => 'Roll tidak ditemukan.',
                    'data' => null,
                    'errors' => null
                ], 404);
            }

            $report = $roll->report ?? null;
            if ($report && (bool) $report->is_locked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan sudah dikunci. Tidak dapat menghapus roll.',
                    'data' => null,
                    'errors' => null
                ], 403);
            }

            $this->rollService->deleteRoll($roll);

            return response()->json([
                'success' => true,
                'message' => 'Roll berhasil dihapus.',
                'data' => null,
                'errors' => null
            ], 200);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus roll: ' . $e->getMessage(),
                'data' => null,
                'errors' => null
            ], 500);
        }
    }
}