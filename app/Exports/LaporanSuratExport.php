<?php

namespace App\Exports;

use App\Models\PengaturanDesa;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanSuratExport implements FromArray, ShouldAutoSize, WithTitle
{
    /**
     * @param  array<string, mixed>  $laporan
     */
    public function __construct(
        private readonly array $laporan,
        private readonly PengaturanDesa $pengaturan,
    ) {}

    public function array(): array
    {
        $baris = [
            ['PEMERINTAH '.$this->pengaturan->nama_desa],
            [$this->pengaturan->alamat],
            ['Kecamatan '.$this->pengaturan->kecamatan.', Kabupaten '.$this->pengaturan->kabupaten.', '.$this->pengaturan->provinsi.' '.$this->pengaturan->kode_pos],
            ['Telepon '.$this->pengaturan->telepon.' · '.$this->pengaturan->email],
            [],
            ['Laporan Pengajuan Surat SIKADES'],
            [
                'Periode',
                $this->laporan['tanggalMulai']->locale('id')->translatedFormat('d F Y'),
                's.d.',
                $this->laporan['tanggalSelesai']->locale('id')->translatedFormat('d F Y'),
            ],
            [],
            ['Rekap per Jenis Surat', 'Jumlah Pengajuan'],
        ];

        foreach ($this->laporan['rekapJenis'] as $item) {
            $baris[] = [$item->nama, (int) $item->jumlah];
        }

        $baris[] = [];
        $baris[] = ['Rekap per Status', 'Jumlah Pengajuan'];

        foreach ($this->laporan['rekapStatus'] as $item) {
            $baris[] = [$item->label, $item->jumlah];
        }

        return $baris;
    }

    public function title(): string
    {
        return 'Laporan Surat';
    }
}
