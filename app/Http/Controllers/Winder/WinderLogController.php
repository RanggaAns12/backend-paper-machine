<?php

namespace App\Http\Controllers\Winder;

use App\Http\Controllers\Controller;
use App\Models\WinderLog;
use App\Models\FinishedGood;
use App\Models\PaperMachineRoll;
use App\Models\PreOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class WinderLogController extends Controller
{
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

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'paper_machine_roll_id' => 'required|exists:paper_machine_rolls,id',
            'operator_id'           => 'required|exists:operators,id',
            'roll_number'           => 'required|string|unique:winder_logs,roll_number', 
            'roll_weight'           => 'required|numeric|min:0',
            'core_diameter'         => 'required|numeric|min:0', 
            'width'                 => 'required|numeric|min:0', 
        ]);

        DB::beginTransaction();

        try {
            $pmRoll = PaperMachineRoll::findOrFail($validated['paper_machine_roll_id']);

            if ($pmRoll->tonase_roll < $validated['roll_weight']) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Gagal! Berat potongan melebihi sisa Jumbo Roll.'
                ], 400);
            }

            // 🔥 PERBAIKAN: Kita PAKSA ambil po_number langsung dari $request
            $log = WinderLog::create([
                'paper_machine_roll_id' => $request->paper_machine_roll_id,
                'operator_id'           => $request->operator_id,
                'roll_number'           => $request->roll_number,
                'roll_weight'           => $request->roll_weight,
                'core_diameter'         => $request->core_diameter,
                'width'                 => $request->width,
                'po_number'             => $request->po_number, // <--- MASUK PAKSA!
                'wound_at'              => now(),
            ]);

            $pmRoll->tonase_roll -= $validated['roll_weight'];
            $pmRoll->save();

            FinishedGood::create([
                'winder_log_id' => $log->id,
                'roll_number'   => $log->roll_number,
                'roll_weight'   => $log->roll_weight,
                'width'         => $log->width,
                'core_diameter' => $log->core_diameter,
                'grade'         => $pmRoll->grade ?? 'N/A', 
                'status'        => 'in_stock' 
            ]);

            // 🔥 PERBAIKAN STATUS PO: Memakai ENUM bawaan database mas
            if (!empty($request->po_number)) {
                $po = PreOrder::where('po_number', $request->po_number)->first();
                if ($po) {
                    $totalProduced = FinishedGood::whereHas('winderLog', function($query) use ($po) {
                        $query->where('po_number', $po->po_number);
                    })->sum('roll_weight');

                    $newStatus = 'ON_PROGRESS'; // Bahasa Inggris sesuai database
                    if ($totalProduced >= $po->target_qty) {
                        $newStatus = 'COMPLETED';
                    }
                    
                    $po->update(['status' => $newStatus]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Roll Winder selesai dipotong dan otomatis masuk Gudang.',
                'data' => $log->load('paperMachineRoll', 'operator')
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $log = WinderLog::findOrFail($id);
            
            $finishedGood = FinishedGood::where('winder_log_id', $log->id)->first();
            if ($finishedGood && $finishedGood->status !== 'in_stock') {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal Void! Barang sudah keluar (Surat Jalan/DO).'
                ], 403);
            }

            $poNumber = $log->po_number;

            $pmRoll = PaperMachineRoll::find($log->paper_machine_roll_id);
            if ($pmRoll) {
                $pmRoll->tonase_roll += $log->roll_weight;
                $pmRoll->save();
            }

            if ($finishedGood) {
                $finishedGood->delete();
            }

            $log->delete();

            // 🔥 PERBAIKAN STATUS PO SAAT VOID
            if (!empty($poNumber)) {
                $po = PreOrder::where('po_number', $poNumber)->first();
                if ($po) {
                    $totalProduced = FinishedGood::whereHas('winderLog', function($query) use ($po) {
                        $query->where('po_number', $po->po_number);
                    })->sum('roll_weight');

                    $newStatus = ($totalProduced == 0) ? 'PENDING' : 'ON_PROGRESS';
                    if ($totalProduced >= $po->target_qty) {
                        $newStatus = 'COMPLETED';
                    }
                    $po->update(['status' => $newStatus]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Void Berhasil! Stok ditarik dan kalkulasi PO disesuaikan.'
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Fitur Edit dinonaktifkan demi keamanan stok.'
        ], 403);
    }
}