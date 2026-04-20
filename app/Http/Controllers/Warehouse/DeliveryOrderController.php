<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Services\DeliveryOrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DeliveryOrderController extends Controller
{
    public function __construct(protected DeliveryOrderService $doService) {}

    /**
     * 1. Menampilkan Semua Surat Jalan (Untuk Tabel Riwayat Outbound)
     */
    public function index(Request $request): JsonResponse
    {
        // Mengambil data dengan pagination (default 15 per halaman)
        $dos = $this->doService->getAllDO($request->get('per_page', 15));
        
        return response()->json([
            'success' => true, 
            'data' => $dos
        ]);
    }

    /**
     * 2. Membuat Draft Surat Jalan Baru (Header)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date'          => 'required|date',
            'customer_name' => 'required|string|max:255',
            'truck_plate'   => 'nullable|string|max:20',
            'driver_name'   => 'nullable|string|max:100',
        ]);

        // Menggunakan Auth::id() untuk mencatat siapa admin yang bertugas
        // Jika sedang testing tanpa login, bisa gunakan fallback ID 1
        $adminId = Auth::id() ?? 1;

        $do = $this->doService->createDeliveryOrder($validated, $adminId);

        return response()->json([
            'success' => true,
            'message' => 'Draft Surat Jalan berhasil dibuat.',
            'data'    => $do
        ], 201);
    }

    /**
     * 3. API SCANNER: Memuat Roll Kertas ke Truk
     * Ini dipanggil setiap kali scanner menembak Barcode Roll
     */
    public function scanOut(Request $request, $doId): JsonResponse
    {
        $request->validate([
            'roll_number' => 'required|string'
        ]);

        try {
            // Memanggil logika validasi ketat di Service
            $item = $this->doService->scanOutItem($doId, $request->roll_number);
            
            return response()->json([
                'success' => true,
                'message' => 'Roll berhasil dimuat ke truk.',
                'data'    => $item
            ]);
            
        } catch (\Exception $e) {
            // Menangkap error (Misal: Roll tidak ada, atau sudah terkirim)
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * 4. Melihat Detail Surat Jalan (Isi Muatan Truk)
     */
    public function show($id): JsonResponse
    {
        $do = $this->doService->getDODetails($id);
        
        if (!$do) {
            return response()->json([
                'success' => false, 
                'message' => 'Surat Jalan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true, 
            'data' => $do
        ]);
    }
}