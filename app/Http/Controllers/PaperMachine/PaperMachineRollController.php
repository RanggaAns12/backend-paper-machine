<?php

namespace App\Http\Controllers\PaperMachine;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaperMachine\StorePaperMachineRollRequest;
use App\Http\Resources\PaperMachineRollResource;
use App\Models\PaperMachineRoll; // Ditambahkan untuk fungsi index
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

    /**
     * ✅ FUNGSI BARU: Mengambil daftar Jumbo Roll 
     * (Sangat dibutuhkan oleh modul Winder untuk isi Dropdown)
     */
    public function index(): JsonResponse
    {
        try {
            // Mengambil semua data PM Roll (diurutkan dari yang terbaru).
            // Jika mas nanti punya fungsi khusus di Service (misal: getAvailableRolls),
            // mas bisa mengganti baris ini menggunakan fungsi dari service tersebut.
            $rolls = PaperMachineRoll::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar Jumbo Roll berhasil diambil',
                'data' => PaperMachineRollResource::collection($rolls),
                'errors' => null
            ], 200);

        } catch (Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data Jumbo Roll: ' . $e->getMessage(),
                'data' => null,
                'errors' => null
            ], 500);
        }
    }

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
                    'message' => 'Laporan sudah dikunci. Tidak dapat menambah/merubah roll.',
                    'data' => null,
                    'errors' => null
                ], 403);
            }

            $data = $request->validated();

            // ✅ AUTO-UPSERT: Jika Frontend mengirimkan ID, berati ini adalah proses UPDATE yang "nyasar" ke Store.
            if (!empty($data['id'])) {
                $existingRoll = $this->rollService->findRollById($data['id']);
                
                if ($existingRoll) {
                    $updated = $this->rollService->updateRoll($existingRoll, $data);
                    return response()->json([
                        'success' => true,
                        'message' => 'Roll berhasil diupdate.',
                        'data' => new PaperMachineRollResource($updated),
                        'errors' => null
                    ], 200);
                }
            }

            // Jika tidak ada ID, eksekusi proses pembuatan Roll Baru
            $createdRoll = $this->rollService->addRollToReport($reportId, $data);

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