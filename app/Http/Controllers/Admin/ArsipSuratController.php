<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArsipSuratRequest;
use App\Models\JenisSurat;
use App\Models\SuratTerbit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ArsipSuratController extends Controller
{
    public function index(ArsipSuratRequest $request): View
    {
        $filter = $request->validated();
        $query = SuratTerbit::query()
            ->with(['pengajuanSurat.penduduk', 'pengajuanSurat.jenisSurat']);

        if ($filter['q'] ?? false) {
            $kataKunci = '%'.$filter['q'].'%';
            $query->where(function ($query) use ($kataKunci): void {
                $query->where('nomor_surat', 'like', $kataKunci)
                    ->orWhereHas('pengajuanSurat.penduduk', function ($penduduk) use ($kataKunci): void {
                        $penduduk->where('nama', 'like', $kataKunci)
                            ->orWhere('nik', 'like', $kataKunci);
                    });
            });
        }

        if ($filter['jenis_surat_id'] ?? false) {
            $query->whereHas('pengajuanSurat', fn ($pengajuan) => $pengajuan->where('jenis_surat_id', $filter['jenis_surat_id']));
        }

        if (($filter['tahun'] ?? false) || ($filter['bulan'] ?? false)) {
            $rentangTanggal = Carbon::createFromDate(
                (int) ($filter['tahun'] ?? now()->year),
                (int) ($filter['bulan'] ?? 1),
                1,
            );
            $mulai = $rentangTanggal->copy()->startOfMonth();
            $selesai = $rentangTanggal->copy()->endOfMonth();

            if (! ($filter['bulan'] ?? false)) {
                $mulai->startOfYear();
                $selesai->endOfYear();
            }

            $query->whereBetween('tgl_terbit', [$mulai->toDateString(), $selesai->toDateString()]);
        }

        $tahunTersedia = DB::table('surat_terbit')
            ->selectRaw("strftime('%Y', tgl_terbit) as tahun")
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('admin.arsip-surat.index', [
            'suratTerbit' => $query->latest('tgl_terbit')->paginate(15)->withQueryString(),
            'jenisSurat' => JenisSurat::query()->orderBy('nama')->get(),
            'tahunTersedia' => $tahunTersedia,
        ]);
    }
}
