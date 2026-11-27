<?php

namespace App\Http\Controllers\Warga;

use App\Enums\StatusPengajuan;
use App\Http\Controllers\Controller;
use App\Models\PengajuanSurat;
use App\Models\PengaturanDesa;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $pengajuan = PengajuanSurat::query()
            ->with('jenisSurat')
            ->where('user_id', $user->id);
        $jumlahPerStatus = $pengajuan
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');
        $jumlahPerStatus = collect(StatusPengajuan::cases())
            ->mapWithKeys(fn (StatusPengajuan $status): array => [
                $status->value => (int) $jumlahPerStatus->get($status->value, 0),
            ]);
        $pengajuanTerakhir = PengajuanSurat::query()
            ->with(['jenisSurat', 'suratTerbit'])
            ->where('user_id', $user->id)
            ->latest('tgl_pengajuan')
            ->first();
        $riwayatSingkat = PengajuanSurat::query()
            ->with('jenisSurat')
            ->where('user_id', $user->id)
            ->latest('tgl_pengajuan')
            ->limit(5)
            ->get();
        $penduduk = $user->penduduk;
        $profilCocok = $penduduk && $user->nik === $penduduk->nik;
        $teleponDesa = PengaturanDesa::query()->value('telepon');

        return view('warga.dashboard', compact(
            'jumlahPerStatus',
            'pengajuanTerakhir',
            'riwayatSingkat',
            'profilCocok',
            'teleponDesa',
        ));
    }
}
