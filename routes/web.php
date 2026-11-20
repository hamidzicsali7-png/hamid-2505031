<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->hasAnyRole(['admin', 'kades', 'sekretaris'])) {
            return app(\App\Http\Controllers\Admin\DashboardController::class)->index();
        }
        return redirect()->route('warga.pengajuan.index');
    })->name('dashboard');

    Route::middleware('role:admin|kades|sekretaris')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/pengajuan', [\App\Http\Controllers\Admin\PengajuanSuratController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/{pengajuan}', [\App\Http\Controllers\Admin\PengajuanSuratController::class, 'show'])->name('pengajuan.show');
        Route::patch('/pengajuan/{pengajuan}/status', [\App\Http\Controllers\Admin\PengajuanSuratController::class, 'status'])->name('pengajuan.status');
        Route::get('/pengajuan/{pengajuan}/pdf', [\App\Http\Controllers\Admin\PengajuanSuratController::class, 'pdf'])->name('pengajuan.pdf');
    });

    Route::prefix('warga')->name('warga.')->group(function () {
        Route::get('/pengajuan', [\App\Http\Controllers\Warga\PengajuanSuratController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/buat', [\App\Http\Controllers\Warga\PengajuanSuratController::class, 'create'])->name('pengajuan.create');
        Route::post('/pengajuan', [\App\Http\Controllers\Warga\PengajuanSuratController::class, 'store'])->name('pengajuan.store');
        Route::get('/pengajuan/{pengajuan}', [\App\Http\Controllers\Warga\PengajuanSuratController::class, 'show'])->name('pengajuan.show');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
