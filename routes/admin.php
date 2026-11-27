<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ArsipSuratController;
use App\Http\Controllers\Admin\JenisSuratController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PejabatDesaController;
use App\Http\Controllers\Admin\PendudukController;
use App\Http\Controllers\Admin\PengajuanSuratController;
use App\Http\Controllers\Admin\PengaturanDesaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin|kades|sekretaris'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/arsip-surat', [ArsipSuratController::class, 'index'])->name('arsip-surat.index');
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/excel', [LaporanController::class, 'excel'])->name('laporan.excel');
        Route::get('/laporan/pdf', [LaporanController::class, 'pdf'])->name('laporan.pdf');
        Route::get('/penduduk/template', [PendudukController::class, 'template'])->name('penduduk.template');
        Route::post('/penduduk/impor', [PendudukController::class, 'import'])->name('penduduk.import');
        Route::resource('penduduk', PendudukController::class);
        Route::resource('jenis-surat', JenisSuratController::class)->parameters(['jenis-surat' => 'jenis_surat']);
        Route::resource('pejabat-desa', PejabatDesaController::class)->parameters(['pejabat-desa' => 'pejabat_desa']);
        Route::get('/pengaturan-desa', [PengaturanDesaController::class, 'index'])->name('pengaturan-desa.index');
        Route::put('/pengaturan-desa', [PengaturanDesaController::class, 'update'])->name('pengaturan-desa.update');
        Route::get('/pengajuan', [PengajuanSuratController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/{pengajuan}/dokumen/{dokumen}', [PengajuanSuratController::class, 'dokumen'])->name('pengajuan.dokumen');
        Route::get('/pengajuan/{pengajuan}', [PengajuanSuratController::class, 'show'])->name('pengajuan.show');
        Route::patch('/pengajuan/{pengajuan}/status', [PengajuanSuratController::class, 'status'])->name('pengajuan.status');
        Route::get('/pengajuan/{pengajuan}/pdf', [PengajuanSuratController::class, 'pdf'])->name('pengajuan.pdf');
        Route::post('/pengajuan/{pengajuan}/pdf/cetak-ulang', [PengajuanSuratController::class, 'cetakUlang'])
            ->middleware('role:admin')
            ->name('pengajuan.pdf.cetak-ulang');
        Route::get('/antrean-persetujuan', [PengajuanSuratController::class, 'antrean'])
            ->middleware('role:kades')
            ->name('antrean-persetujuan.index');
    });
