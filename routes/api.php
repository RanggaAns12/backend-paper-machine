<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Superadmin\UserController;
use App\Http\Controllers\Superadmin\MachineController;
use App\Http\Controllers\Lab\QualityTestController;
use App\Http\Controllers\PaperMachine\PaperMachineReportController;
use App\Http\Controllers\PaperMachine\PaperMachineRollController;
use App\Http\Controllers\PaperMachine\PaperMachineProblemController;
use App\Http\Controllers\Winder\WinderLogController;
use Illuminate\Support\Facades\Route;

Route::get('/test', fn() => response()->json(['ok' => true]));

Route::middleware(['json.force'])->group(function () {
// ── Auth ──────────────────────────────────────────────────
Route::prefix('auth')->group(function () {

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('auth.login');

    Route::middleware(['auth:sanctum', 'active'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/me',     [AuthController::class, 'me'])->name('auth.me');
    });
});

// ── Protected ─────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'active'])->group(function () {

    // Superadmin
    Route::prefix('superadmin')
        ->middleware('role:superadmin')
        ->group(function () {
            Route::apiResource('users',    UserController::class);
            Route::apiResource('machines', MachineController::class);
        });

    // Paper Machine
    Route::prefix('paper-machine')
        ->middleware('role:superadmin|admin_paper_machine')
        ->group(function () {
            Route::apiResource('reports', PaperMachineReportController::class);
            Route::post('reports/{id}/rolls',    [PaperMachineRollController::class, 'store']);
            Route::put('rolls/{id}',             [PaperMachineRollController::class, 'update']);
            Route::delete('rolls/{id}',          [PaperMachineRollController::class, 'destroy']);
            Route::post('reports/{id}/problems', [PaperMachineProblemController::class, 'store']);
            Route::put('problems/{id}',          [PaperMachineProblemController::class, 'update']);
            Route::delete('problems/{id}',       [PaperMachineProblemController::class, 'destroy']);
        });

    // Lab
    Route::prefix('lab')
        ->middleware('role:superadmin|admin_lab')
        ->group(function () {
            Route::apiResource('quality-tests', QualityTestController::class);
        });

    // Winder
    Route::prefix('winder')
        ->middleware('role:superadmin|admin_winder')
        ->group(function () {
            Route::apiResource('winder-logs', WinderLogController::class);
        });
});

});
