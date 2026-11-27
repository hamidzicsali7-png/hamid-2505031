<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\SuratVerifikasiController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));
Route::get('/verifikasi/{kodeVerifikasi}', [SuratVerifikasiController::class, 'show'])->name('surat.verifikasi');

Route::get('/dashboard', function () {
    $user = request()->user();

    if ($user->hasAnyRole(['admin', 'kades', 'sekretaris'])) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('warga')) {
        return redirect()->route('warga.dashboard');
    }

    abort(403);
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'tandaiDibaca'])->name('notifikasi.baca');
});

require __DIR__.'/admin.php';
require __DIR__.'/warga.php';
require __DIR__.'/auth.php';
