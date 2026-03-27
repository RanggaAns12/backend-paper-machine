<?php

use Illuminate\Support\Facades\Route;

// Import Controllers
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Superadmin\UserController;
use App\Http\Controllers\Superadmin\MachineController;
use App\Http\Controllers\Lab\QualityTestController;
use App\Http\Controllers\Winder\WinderLogController;

// Import Paper Machine Controllers
use App\Http\Controllers\PaperMachine\PaperMachineReportController;
use App\Http\Controllers\PaperMachine\PaperMachineRollController;
use App\Http\Controllers\PaperMachine\PaperMachineProblemController;
use App\Http\Controllers\PaperMachine\OperatorController;

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

        // ██████ PAPER MACHINE MODULE ██████
        // Roles: superadmin | admin_paper_machine
        Route::prefix('paper-machine')
            ->middleware('role:superadmin|admin_paper_machine')
            ->group(function () {
                
                // Reports
                Route::apiResource('reports', PaperMachineReportController::class);
                Route::patch('reports/{id}/unlock', [PaperMachineReportController::class, 'unlock'])
                    ->name('paper-machine.reports.unlock')
                    ->middleware('role:superadmin'); // Hanya superadmin bisa unlock

                // Rolls
                Route::post('reports/{report}/rolls', [PaperMachineRollController::class, 'store'])
                    ->name('paper-machine.reports.rolls.store');
                Route::put('rolls/{id}', [PaperMachineRollController::class, 'update'])
                    ->name('paper-machine.rolls.update');
                Route::delete('rolls/{id}', [PaperMachineRollController::class, 'destroy'])
                    ->name('paper-machine.rolls.destroy');

                // Problems
                Route::post('reports/{report}/problems', [PaperMachineProblemController::class, 'store'])
                    ->name('paper-machine.reports.problems.store');
                Route::put('problems/{id}', [PaperMachineProblemController::class, 'update'])
                    ->name('paper-machine.problems.update');
                Route::delete('problems/{id}', [PaperMachineProblemController::class, 'destroy'])
                    ->name('paper-machine.problems.destroy');

                // Operators
                Route::apiResource('operators', OperatorController::class);
            });

        // ██████ LAB MODULE ██████
        // Roles: superadmin | admin_lab
        Route::prefix('lab')
            ->middleware('role:superadmin|admin_lab')
            ->group(function () {
                Route::apiResource('quality-tests', QualityTestController::class);
            });

        // ██████ WINDER MODULE ██████
        // Roles: superadmin | admin_winder
        Route::prefix('winder')
            ->middleware('role:superadmin|admin_winder')
            ->group(function () {
                // Endpoint untuk modul Winder (Otomatis mencakup index, store, show, update, destroy)
                Route::apiResource('winder-logs', WinderLogController::class);
            });
    });
});