<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Services\QualityTestService;
use App\Models\PaperMachineRoll;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class QualityTestController extends Controller
{
    public function __construct(protected QualityTestService $qualityTestService) {}

    // 1. Mengambil riwayat QC untuk tabel View Reports
    public function index(Request $request): JsonResponse
    {
        $tests = $this->qualityTestService->getAllQualityTests(
            $request->get('per_page', 15),
            $request->all()
        );
        return response()->json(['success' => true, 'data' => $tests]);
    }

    // 2. 🔥 API KHUSUS DROPDOWN: Menampilkan Jumbo Roll yang belum dites
    public function getPendingRolls(): JsonResponse
    {
        $pendingRolls = PaperMachineRoll::with('report:id,date,grup,operator_name')
            ->whereHas('report', function ($query) {
                // Syarat mutlak: Laporan PM harus sudah di-lock (disahkan)
                $query->where('is_locked', true); 
            })
            // Syarat mutlak: Roll belum pernah dites (statusnya pending)
            ->where('qc_status', 'pending') 
            ->orderBy('id', 'asc')
            ->get();

        return response()->json(['success' => true, 'data' => $pendingRolls]);
    }

    // 3. Menyimpan Hasil Lab dan Memicu Efek Domino ke PM Roll
    public function store(Request $request): JsonResponse
    {
        // ✅ PERBAIKAN: Validasi disesuaikan 100% dengan form Angular dan Migration baru
        $validated = $request->validate([
            'paper_machine_roll_id' => 'required|exists:paper_machine_rolls,id',
            'shift'                 => 'required|integer',
            'thickness'             => 'required|numeric|min:0',
            'bw'                    => 'required|numeric|min:0',
            'rct'                   => 'required|numeric|min:0',
            'bursting'              => 'required|numeric|min:0',
            'moisture'              => 'required|numeric|min:0|max:100',
            'cobb_top'              => 'nullable|integer|min:0',
            'cobb_bottom'           => 'nullable|integer|min:0',
            'plybonding'            => 'required|in:BAIK,TIDAK',
            'warna'                 => 'required|in:SESUAI,TIDAK',
            'status'                => 'required|in:PASS,REJECT,DOWNGRADE', // UPPERCASE
            'notes'                 => 'nullable|string'
        ]);

        $test = $this->qualityTestService->createQualityTest($validated, Auth::id());

        return response()->json([
            'success' => true,
            'message' => 'Hasil uji Lab berhasil disimpan dan status Jumbo Roll telah diperbarui.',
            'data'    => $test
        ], 201);
    }

    // 4. Melihat detail satu pengujian Lab
    public function show($id): JsonResponse
    {
        $test = $this->qualityTestService->findQualityTestById($id);
        if (!$test) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $test]);
    }

    // 5. Mengupdate hasil uji Lab
    public function update(Request $request, $id): JsonResponse
    {
        $test = $this->qualityTestService->findQualityTestById($id);
        if (!$test) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        // ✅ PERBAIKAN: Validasi untuk proses update data
        $validated = $request->validate([
            'shift'                 => 'sometimes|integer',
            'thickness'             => 'sometimes|numeric|min:0',
            'bw'                    => 'sometimes|numeric|min:0',
            'rct'                   => 'sometimes|numeric|min:0',
            'bursting'              => 'sometimes|numeric|min:0',
            'moisture'              => 'sometimes|numeric|min:0|max:100',
            'cobb_top'              => 'nullable|integer|min:0',
            'cobb_bottom'           => 'nullable|integer|min:0',
            'plybonding'            => 'sometimes|in:BAIK,TIDAK',
            'warna'                 => 'sometimes|in:SESUAI,TIDAK',
            'status'                => 'sometimes|in:PASS,REJECT,DOWNGRADE',
            'notes'                 => 'nullable|string'
        ]);

        $updated = $this->qualityTestService->updateQualityTest($test, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Hasil uji Lab berhasil diperbarui.',
            'data'    => $updated
        ]);
    }

    // 6. Menghapus data uji Lab (Efek Domino Mundur)
    public function destroy($id): JsonResponse
    {
        $test = $this->qualityTestService->findQualityTestById($id);
        if (!$test) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $this->qualityTestService->deleteQualityTest($test);

        return response()->json([
            'success' => true,
            'message' => 'Data Lab berhasil dihapus, status Jumbo Roll dikembalikan ke Pending.'
        ]);
    }
}