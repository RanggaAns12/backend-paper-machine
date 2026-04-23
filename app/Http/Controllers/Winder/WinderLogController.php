<?php

namespace App\Http\Controllers\Winder;

use App\Http\Controllers\Controller;
use App\Models\WinderLog;
use App\Models\FinishedGood;
use App\Models\PaperMachineRoll;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class WinderLogController extends Controller
{
    /**
     * Menampilkan semua data View Report Winder
     */
    public function index(): JsonResponse
    {
        try {
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
     * Menyimpan data baru dari Form Winder Log Sheet & Otomatisasi ke Gudang
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Validasi Input dari Angular
        $validated = $request->validate([
            'paper_machine_roll_id' => 'required|exists:paper_machine_rolls,id',
            'operator_id'           => 'required|exists:operators,id',
            'roll_number'           => 'required|string|unique:winder_logs,roll_number', // Cegah barcode ganda
            'roll_weight'           => 'required|numeric|min:0',
            'core_diameter'         => 'required|numeric|min:0', // Gudang butuh ini
            'width'                 => 'required|numeric|min:0', // Gudang butuh ini
            'status'                => 'required|in:pending,done',
        ]);

        // 2. Mulai Transaksi Database (Aman dari kegagalan server)
        DB::beginTransaction();

        try {
            // Tentukan status log winder (Jika 'done', kita ubah jadi 'in_warehouse' agar sinkron)
            $winderStatus = $request->status === 'done' ? 'done' : 'pending'; // <-- Kembalikan jadi 'done'

            $log = WinderLog::create(array_merge($validated, [
                'status' => $winderStatus
            ]));

            $message = 'Data Winder berhasil disimpan sebagai draft.';

            // 🔥 LOGIKA OTOMATISASI KE GUDANG 🔥
            if ($request->status === 'done') {
                // Ambil data Jumbo Roll (PM) untuk mendapatkan spesifikasi "Grade"
                $pmRoll = PaperMachineRoll::findOrFail($validated['paper_machine_roll_id']);

                // Buat stok baru di Gudang (Finished Good)
                FinishedGood::create([
                    'winder_log_id' => $log->id,
                    'roll_number'   => $log->roll_number,
                    'roll_weight'   => $log->roll_weight,
                    'width'         => $log->width,
                    'core_diameter' => $log->core_diameter,
                    'grade'         => $pmRoll->grade ?? 'N/A', // Tarik Grade dari PM
                    'status'        => 'in_stock' // Status default saat barang baru masuk
                ]);

                $message = 'Data Winder berhasil disimpan dan Roll otomatis masuk ke Inventori Gudang!';
            }

            // Jika semua lancar, simpan permanen
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $log->load('paperMachineRoll', 'operator')
            ], 201);

        } catch (Throwable $e) {
            // Jika ada error (misal gagal save ke gudang), batalkan juga save di Winder
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'paper_machine_roll_id' => 'required|exists:paper_machine_rolls,id',
            'operator_id'           => 'required|exists:operators,id',
            'roll_number'           => 'required|string',
            'roll_weight'           => 'required|numeric|min:0',
            'core_diameter'         => 'required|numeric|min:0',
            'width'                 => 'required|numeric|min:0',
            'status'                => 'required|in:pending,done',
        ]);

        DB::beginTransaction();
        try {
            $log = WinderLog::findOrFail($id);

            // Proteksi: Jika sudah di gudang, tidak boleh di-update sembarangan
            if ($log->status === 'done') { // <-- Ganti 'in_warehouse' jadi 'done'
                return response()->json(['success' => false, 'message' => 'Data sudah terkunci di gudang.'], 403);
            }

            if ($request->status === 'done') {
                $pmRoll = PaperMachineRoll::findOrFail($validated['paper_machine_roll_id']);

                // 1. Update status di Winder Log jadi 'done' (Bukan 'in_warehouse')
                $log->update(array_merge($validated, ['status' => 'done']));

                // 2. Tembak ke tabel Finished Goods
                FinishedGood::create([
                    'winder_log_id' => $log->id,
                    'roll_number'   => $log->roll_number,
                    'roll_weight'   => $log->roll_weight,
                    'width'         => $log->width,
                    'core_diameter' => $log->core_diameter,
                    'grade'         => $pmRoll->grade ?? 'N/A',
                    'status'        => 'in_stock'
                ]);

                $message = 'Draft berhasil diterbitkan ke Gudang!';
            } else {
                // Jika hanya update data draft biasa tetap sebagai pending
                $log->update($validated);
                $message = 'Draft berhasil diperbarui.';
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => $message, 'data' => $log], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Menghapus data Log
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $log = WinderLog::findOrFail($id);
            
            // Opsional: Cek jika barang sudah di gudang, apakah boleh dihapus dari Winder?
            if ($log->status === 'in_warehouse') {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak bisa dihapus karena barang sudah terdaftar di Gudang.'
                ], 403);
            }

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