<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPengajuan;
use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\SuratTerbit;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $awalMinggu = now()->startOfWeek();
        $akhirMinggu = now()->endOfWeek();
        $akhirGrafik = now()->endOfMonth();
        $awalGrafik = $akhirGrafik->copy()->subMonths(11)->startOfMonth();
        $periodeGrafik = collect(range(0, 11))
            ->map(fn (int $offset): Carbon => $awalGrafik->copy()->addMonths($offset));
        $kunciBulan = $periodeGrafik->map(fn (Carbon $bulan): string => $bulan->format('Y-m'));
        $labelBulan = $periodeGrafik
            ->map(fn (Carbon $bulan): string => $bulan->locale('id')->translatedFormat('M Y'))
            ->all();
        $rentangTanggalGrafik = [$awalGrafik->toDateString(), $akhirGrafik->toDateString()];
        $suratTerbitPerBulan = DB::table('surat_terbit')
            ->selectRaw("strftime('%Y-%m', tgl_terbit) as bulan, COUNT(*) as jumlah")
            ->whereBetween('tgl_terbit', $rentangTanggalGrafik)
            ->groupBy('bulan')
            ->pluck('jumlah', 'bulan');
        $pengajuanPerBulan = DB::table('pengajuan_surat')
            ->selectRaw("strftime('%Y-%m', tgl_pengajuan) as bulan, COUNT(*) as jumlah")
            ->whereBetween('tgl_pengajuan', [$awalGrafik->copy()->startOfDay(), $akhirGrafik->copy()->endOfDay()])
            ->groupBy('bulan')
            ->pluck('jumlah', 'bulan');
        $suratTerbitPerJenis = DB::table('jenis_surat')
            ->leftJoin('pengajuan_surat', 'jenis_surat.id', '=', 'pengajuan_surat.jenis_surat_id')
            ->leftJoin('surat_terbit', function (JoinClause $join) use ($rentangTanggalGrafik): void {
                $join->on('surat_terbit.pengajuan_surat_id', '=', 'pengajuan_surat.id')
                    ->whereBetween('surat_terbit.tgl_terbit', $rentangTanggalGrafik);
            })
            ->select('jenis_surat.nama')
            ->selectRaw('COUNT(surat_terbit.id) as jumlah')
            ->groupBy('jenis_surat.id', 'jenis_surat.nama')
            ->orderBy('jenis_surat.nama')
            ->get();
        $komposisiSurat = DB::table('surat_terbit')
            ->join('pengajuan_surat', 'surat_terbit.pengajuan_surat_id', '=', 'pengajuan_surat.id')
            ->join('jenis_surat', 'pengajuan_surat.jenis_surat_id', '=', 'jenis_surat.id')
            ->whereBetween('surat_terbit.tgl_terbit', $rentangTanggalGrafik)
            ->select('jenis_surat.nama')
            ->selectRaw('COUNT(surat_terbit.id) as jumlah')
            ->groupBy('jenis_surat.id', 'jenis_surat.nama')
            ->orderBy('jenis_surat.nama')
            ->get();

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
            'menungguVerifikasiLama' => PengajuanSurat::query()
                ->where('status', StatusPengajuan::Diajukan)
                ->where('tgl_pengajuan', '<', now()->subDays(2))
                ->count(),
            'grafik' => [
                'labelBulan' => $labelBulan,
                'suratTerbit' => $kunciBulan->map(fn (string $bulan): int => (int) ($suratTerbitPerBulan[$bulan] ?? 0))->all(),
                'pengajuanMasuk' => $kunciBulan->map(fn (string $bulan): int => (int) ($pengajuanPerBulan[$bulan] ?? 0))->all(),
                'labelSuratPerJenis' => $suratTerbitPerJenis->pluck('nama')->all(),
                'suratPerJenis' => $suratTerbitPerJenis->pluck('jumlah')->map(fn ($jumlah): int => (int) $jumlah)->all(),
                'labelKomposisiSurat' => $komposisiSurat->pluck('nama')->all(),
                'komposisiSurat' => $komposisiSurat->pluck('jumlah')->map(fn ($jumlah): int => (int) $jumlah)->all(),
            ],
        ]);
    }
}
