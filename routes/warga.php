<?php

use App\Http\Controllers\Warga\DashboardController;
use App\Http\Controllers\Warga\PengajuanSuratController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:warga'])
    ->prefix('warga')
    ->name('warga.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/pengajuan', [PengajuanSuratController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/buat', [PengajuanSuratController::class, 'create'])->name('pengajuan.create');
        Route::post('/pengajuan', [PengajuanSuratController::class, 'store'])->name('pengajuan.store');
        Route::get('/pengajuan/{pengajuan}/edit', [PengajuanSuratController::class, 'edit'])->name('pengajuan.edit');
        Route::patch('/pengajuan/{pengajuan}', [PengajuanSuratController::class, 'update'])->name('pengajuan.update');
        Route::get('/pengajuan/{pengajuan}/dokumen/{dokumen}', [PengajuanSuratController::class, 'downloadDokumen'])->name('pengajuan.dokumen');
        Route::get('/pengajuan/{pengajuan}/pdf', [PengajuanSuratController::class, 'pdf'])->name('pengajuan.pdf');
        Route::get('/pengajuan/{pengajuan}', [PengajuanSuratController::class, 'show'])->name('pengajuan.show');
    });
