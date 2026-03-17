<?php

namespace App\Http\Controllers\PaperMachine;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaperMachine\StorePaperMachineProblemRequest;
use App\Http\Resources\PaperMachineProblemResource;
use App\Services\PaperMachineProblemService;
use App\Services\PaperMachineReportService;
use Illuminate\Http\JsonResponse;
use Throwable;

class PaperMachineProblemController extends Controller
{
    public function __construct(
        protected PaperMachineProblemService $problemService,
        protected PaperMachineReportService $reportService
    ) {}

    public function store(StorePaperMachineProblemRequest $request, int $reportId): JsonResponse
    {
        try {
            $report = $this->reportService->findReportById($reportId); // Sesuai nama fungsi di service Mas
            
            if (!$report) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Laporan tidak ditemukan.', 
                    'data' => null, 
                    'errors' => null
                ], 404);
            }

            // 🛡️ PROTEKSI LAPIS BAJA: CEK STATUS LOCK
            if ((bool) $report->is_locked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan sudah dikunci. Tidak dapat menambah kendala (downtime).',
                    'data' => null,
                    'errors' => null
                ], 403);
            }

            $createdProblem = $this->problemService->addProblemToReport($reportId, $request->validated());

            // ✅ PERBAIKAN: Harus kembalikan data (terutama ID-nya) agar Angular bisa membacanya
            return response()->json([
                'success' => true, 
                'message' => 'Kendala berhasil ditambahkan.', 
                'data' => new PaperMachineProblemResource($createdProblem), 
                'errors' => null
            ], 201);

        } catch (Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kendala: ' . $e->getMessage(),
                'data' => null,
                'errors' => null
            ], 500);
        }
    }

    public function update(StorePaperMachineProblemRequest $request, int $id): JsonResponse
    {
        try {
            $problem = $this->problemService->findProblemById($id);
            
            if (!$problem) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data kendala tidak ditemukan.', 
                    'data' => null, 
                    'errors' => null
                ], 404);
            }

            // 🛡️ PROTEKSI LAPIS BAJA: CEK STATUS LOCK DARI RELASI REPORT
            $report = $problem->report ?? null;
            if ($report && (bool) $report->is_locked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan sudah dikunci. Tidak dapat mengubah kendala.',
                    'data' => null,
                    'errors' => null
                ], 403);
            }

            $updated = $this->problemService->updateProblem($problem, $request->validated());

            return response()->json([
                'success' => true, 
                'message' => 'Kendala berhasil diupdate.', 
                'data' => new PaperMachineProblemResource($updated), 
                'errors' => null
            ], 200);

        } catch (Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate kendala: ' . $e->getMessage(),
                'data' => null,
                'errors' => null
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $problem = $this->problemService->findProblemById($id);
            
            if (!$problem) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data kendala tidak ditemukan.', 
                    'data' => null, 
                    'errors' => null
                ], 404);
            }

            // 🛡️ PROTEKSI LAPIS BAJA: CEK STATUS LOCK SEBELUM HAPUS
            $report = $problem->report ?? null;
            if ($report && (bool) $report->is_locked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan sudah dikunci. Tidak dapat menghapus kendala.',
                    'data' => null,
                    'errors' => null
                ], 403);
            }

            $this->problemService->deleteProblem($problem);

            return response()->json([
                'success' => true, 
                'message' => 'Kendala berhasil dihapus permanen.', 
                'data' => null, 
                'errors' => null
            ], 200);

        } catch (Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kendala: ' . $e->getMessage(),
                'data' => null,
                'errors' => null
            ], 500);
        }
    }
}