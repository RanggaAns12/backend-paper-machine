<?php

namespace App\Http\Controllers\Winder;

use App\Http\Controllers\Controller;
use App\Models\WinderLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Throwable;

class WinderLogController extends Controller
{
    /**
     * Menampilkan semua data View Report Winder
     */
    public function index(): JsonResponse
    {
        try {
            // ✅ MENGAMBIL DATA BESERTA RELASINYA
            $logs = WinderLog::with(['operator', 'paperMachineRoll'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data riwayat Winder berhasil diambil.',
                'data' => $logs
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan data baru dari Form Winder Log Sheet
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'paper_machine_roll_id' => 'required|exists:paper_machine_rolls,id',
                'operator_id'           => 'required|exists:operators,id',
                'roll_number'           => 'required|string',
                'roll_weight'           => 'required|numeric',
                'core_diameter'         => 'nullable|numeric',
                'width'                 => 'nullable|numeric',
                'status'                => 'required|in:pending,done',
            ]);

            $log = WinderLog::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data Winder berhasil disimpan.',
                'data' => $log
            ], 201);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus data Log (Bisa ditambahkan validasi role nantinya)
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $log = WinderLog::findOrFail($id);
            $log->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data Winder berhasil dihapus.'
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}