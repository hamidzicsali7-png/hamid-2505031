<?php

namespace App\Services;

use App\Enums\StatusPengajuan;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanSuratService
{
    /**
     * @param  array<string, mixed>  $filter
     * @return array<string, mixed>
     */
    public function buatLaporan(array $filter): array
    {
        $periode = $filter['periode'] ?? 'bulanan';
        $tahun = (int) ($filter['tahun'] ?? now()->year);

        [$tanggalMulai, $tanggalSelesai] = match ($periode) {
            'tahunan' => [
                Carbon::createFromDate($tahun, 1, 1)->startOfYear(),
                Carbon::createFromDate($tahun, 1, 1)->endOfYear(),
            ],
            'rentang' => [
                Carbon::parse($filter['tanggal_mulai'])->startOfDay(),
                Carbon::parse($filter['tanggal_selesai'])->endOfDay(),
            ],
            default => [
                Carbon::createFromDate($tahun, (int) ($filter['bulan'] ?? now()->month), 1)->startOfMonth(),
                Carbon::createFromDate($tahun, (int) ($filter['bulan'] ?? now()->month), 1)->endOfMonth(),
            ],
        };
        $jenisSuratId = $filter['jenis_surat_id'] ?? null;
        $awalPeriode = $tanggalMulai->copy()->startOfDay();
        $akhirPeriode = $tanggalSelesai->copy()->endOfDay();

        $rekapJenis = DB::table('jenis_surat')
            ->leftJoin('pengajuan_surat', function (JoinClause $join) use ($awalPeriode, $akhirPeriode): void {
                $join->on('jenis_surat.id', '=', 'pengajuan_surat.jenis_surat_id')
                    ->whereBetween('pengajuan_surat.tgl_pengajuan', [$awalPeriode, $akhirPeriode]);
            })
            ->when($jenisSuratId, fn ($query) => $query->where('jenis_surat.id', $jenisSuratId))
            ->select('jenis_surat.id', 'jenis_surat.nama')
            ->selectRaw('COUNT(pengajuan_surat.id) as jumlah')
            ->groupBy('jenis_surat.id', 'jenis_surat.nama')
            ->orderBy('jenis_surat.nama')
            ->get();

        $jumlahPerStatus = DB::table('pengajuan_surat')
            ->whereBetween('tgl_pengajuan', [$awalPeriode, $akhirPeriode])
            ->when($jenisSuratId, fn ($query) => $query->where('jenis_surat_id', $jenisSuratId))
            ->select('status')
            ->selectRaw('COUNT(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $rekapStatus = collect(StatusPengajuan::cases())
            ->map(fn (StatusPengajuan $status): object => (object) [
                'status' => $status,
                'label' => $status->label(),
                'jumlah' => (int) ($jumlahPerStatus[$status->value] ?? 0),
            ]);

        return [
            'rekapJenis' => $rekapJenis,
            'rekapStatus' => $rekapStatus,
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'filter' => [
                'periode' => $periode,
                'bulan' => $periode === 'bulanan' ? (int) ($filter['bulan'] ?? now()->month) : '',
                'tahun' => in_array($periode, ['bulanan', 'tahunan'], true) ? $tahun : '',
                'tanggal_mulai' => $periode === 'rentang' ? $tanggalMulai->toDateString() : '',
                'tanggal_selesai' => $periode === 'rentang' ? $tanggalSelesai->toDateString() : '',
                'jenis_surat_id' => $jenisSuratId,
            ],
        ];
    }
}
