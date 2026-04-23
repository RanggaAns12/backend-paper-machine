<?php

use Illuminate\Support\Facades\Route;

// Import Auth & Superadmin Controllers
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Superadmin\UserController;
use App\Http\Controllers\Superadmin\MachineController;

// Import Lab Controller
use App\Http\Controllers\Lab\QualityTestController;

// Import Winder Controller
use App\Http\Controllers\Winder\WinderLogController;

// Import Paper Machine Controllers
use App\Http\Controllers\PaperMachine\PaperMachineReportController;
use App\Http\Controllers\PaperMachine\PaperMachineRollController;
use App\Http\Controllers\PaperMachine\PaperMachineProblemController;
use App\Http\Controllers\PaperMachine\OperatorController;

// Import Warehouse Controllers (Gudang Barang Jadi & Pre-Order)
use App\Http\Controllers\Warehouse\FinishedGoodController;
use App\Http\Controllers\Warehouse\DeliveryOrderController;
use App\Http\Controllers\Warehouse\PreOrderController; // ✅ Pindah ke sini

// Health Check
Route::get('/test', fn() => response()->json(['ok' => true]));

// Force JSON Response Middleware Group
Route::middleware(['json.force'])->group(function () {

    // ── AUTH ROUTES ─────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:5,1')
            ->name('auth.login');

        Route::middleware(['auth:sanctum', 'active'])->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
            Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
            
            // Fitur Keamanan Akun (Ganti Password) otomatis pakai route ini
            Route::post('/change-password', [AuthController::class, 'changePassword'])->name('auth.change-password');
        });
    });

    // ── PROTECTED ROUTES ───────────────────────────────────
    Route::middleware(['auth:sanctum', 'active'])->group(function () {

        // ██████ SUPERADMIN ONLY ██████
        Route::prefix('superadmin')
            ->middleware('role:superadmin')
            ->group(function () {
                Route::apiResource('users', UserController::class);
                Route::apiResource('machines', MachineController::class);
            });

        // ██████ GLOBAL DATA (LINTAS DIVISI) ██████
        Route::middleware('role:superadmin|admin_paper_machine|admin_winder')->group(function () {
            Route::get('admin-paper-machine/rolls/available', [PaperMachineRollController::class, 'index'])
                ->name('paper-machine.rolls.available');
        });

        // ██████ PAPER MACHINE MODULE ██████
        Route::prefix('admin-paper-machine')->group(function () {
            
            // AKSES BERSAMA (PM & WINDER) 
            Route::middleware('role:superadmin|admin_paper_machine|admin_winder')->group(function () {
                // 1. Winder butuh GET Reports untuk daftar Dropdown Scan Barcode
                Route::get('reports', [PaperMachineReportController::class, 'index']);
                Route::get('reports/{report}', [PaperMachineReportController::class, 'show']);
                
                // 2. Winder & PM sekarang bisa kelola (CRUD) Operator secara penuh
                Route::apiResource('operators', OperatorController::class);
            });

            // AKSES KHUSUS (HANYA PM) - Untuk Tambah, Edit, Hapus Data PM
            Route::middleware('role:superadmin|admin_paper_machine')->group(function () {
                // Reports (Write)
                Route::post('reports', [PaperMachineReportController::class, 'store']);
                Route::put('reports/{report}', [PaperMachineReportController::class, 'update']);
                Route::delete('reports/{report}', [PaperMachineReportController::class, 'destroy']);
                Route::patch('reports/{id}/unlock', [PaperMachineReportController::class, 'unlock'])
                    ->middleware('role:superadmin');

                // Rolls (Write)
                Route::post('reports/{report}/rolls', [PaperMachineRollController::class, 'store']);
                Route::put('rolls/{id}', [PaperMachineRollController::class, 'update']);
                Route::delete('rolls/{id}', [PaperMachineRollController::class, 'destroy']);

                // Problems (Write)
                Route::post('reports/{report}/problems', [PaperMachineProblemController::class, 'store']);
                Route::put('problems/{id}', [PaperMachineProblemController::class, 'update']);
                Route::delete('problems/{id}', [PaperMachineProblemController::class, 'destroy']);
            });
        });

       // ██████ LAB MODULE ██████
        Route::prefix('lab')
            ->middleware('role:superadmin|admin_lab')
            ->group(function () {
                // JALUR DROPDOWN KHUSUS QC (Wajib ditaruh di atas apiResource)
                Route::get('quality-tests/pending-rolls', [QualityTestController::class, 'getPendingRolls']);
                Route::apiResource('quality-tests', QualityTestController::class);
            });

        // ██████ WINDER MODULE ██████
        Route::prefix('admin-winder')
            ->middleware('role:superadmin|admin_winder')
            ->group(function () {
                Route::apiResource('winder-logs', WinderLogController::class);
                
                Route::patch('winder-logs/{id}/unlock', [WinderLogController::class, 'unlock'])
                    ->middleware('role:superadmin'); 
            });

        // ██████ WAREHOUSE MODULE (GUDANG BARANG JADI) ██████
        Route::prefix('warehouse')
            ->middleware('role:superadmin|admin_warehouse')
            ->group(function () {
                
                // ✅ FITUR PRE-ORDER
                Route::get('pre-orders', [PreOrderController::class, 'index']);
                Route::post('pre-orders', [PreOrderController::class, 'store']);

                // Fase 1 & 2: Stok Gudang
                // 🔥 PERBAIKAN: Ubah 'inventory' menjadi 'stock' agar sinkron dengan Angular
                Route::get('stock', [FinishedGoodController::class, 'index']);

                // Fase 3: Outbound (Pembuatan Surat Jalan & Scan Keluar Barang)
                Route::get('delivery-orders', [DeliveryOrderController::class, 'index']);
                Route::post('delivery-orders', [DeliveryOrderController::class, 'store']);
                Route::get('delivery-orders/{id}', [DeliveryOrderController::class, 'show']);
                Route::post('delivery-orders/{id}/scan-out', [DeliveryOrderController::class, 'scanOut']);
            });
            
    }); // <-- End of Protected Routes
});