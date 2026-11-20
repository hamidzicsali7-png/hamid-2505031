<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PengajuanSuratController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin|kades|sekretaris'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/pengajuan', [PengajuanSuratController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/{pengajuan}', [PengajuanSuratController::class, 'show'])->name('pengajuan.show');
        Route::patch('/pengajuan/{pengajuan}/status', [PengajuanSuratController::class, 'status'])->name('pengajuan.status');
        Route::get('/pengajuan/{pengajuan}/pdf', [PengajuanSuratController::class, 'pdf'])->name('pengajuan.pdf');
    });
