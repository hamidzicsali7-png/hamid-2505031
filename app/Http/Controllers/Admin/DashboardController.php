<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPengajuan;
use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\SuratTerbit;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $awalMinggu = now()->startOfWeek();
        $akhirMinggu = now()->endOfWeek();

        return view('admin.dashboard', [
            'pengajuanTerbaru' => PengajuanSurat::query()
                ->with(['penduduk', 'jenisSurat'])
                ->latest('tgl_pengajuan')
                ->limit(5)
                ->get(),
            'jumlahMingguIni' => PengajuanSurat::query()
                ->whereBetween('tgl_pengajuan', [$awalMinggu, $akhirMinggu])
                ->count(),
            'menungguVerifikasi' => PengajuanSurat::query()
                ->where('status', StatusPengajuan::Diajukan)
                ->count(),
            'menungguTtdKades' => PengajuanSurat::query()
                ->where('status', StatusPengajuan::Diverifikasi)
                ->count(),
            'totalPenduduk' => Penduduk::query()->count(),
            'pengajuanBulanIni' => PengajuanSurat::query()
                ->whereBetween('tgl_pengajuan', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
            'suratSelesai' => SuratTerbit::query()->count(),
            'pengajuanHariIni' => PengajuanSurat::query()
                ->whereDate('tgl_pengajuan', today())
                ->count(),
        ]);
    }
}
