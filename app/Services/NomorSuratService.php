<?php

namespace App\Services;

use App\Models\JenisSurat;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class NomorSuratService
{
    private const BULAN_ROMAWI = [
        1 => 'I',
        2 => 'II',
        3 => 'III',
        4 => 'IV',
        5 => 'V',
        6 => 'VI',
        7 => 'VII',
        8 => 'VIII',
        9 => 'IX',
        10 => 'X',
        11 => 'XI',
        12 => 'XII',
    ];

    public function __construct(private readonly PengaturanDesaService $pengaturanDesa) {}

    public function generate(JenisSurat $jenisSurat, ?CarbonInterface $tanggal = null): string
    {
        $tanggal ??= now();

        return DB::transaction(function () use ($jenisSurat, $tanggal): string {
            $pengaturan = $this->pengaturanDesa->ambil();
            $tahun = (int) $tanggal->format('Y');
            $sekarang = now();

            DB::table('nomor_surat_counter')->insertOrIgnore([
                'jenis_surat_id' => $jenisSurat->id,
                'tahun' => $tahun,
                'urutan_terakhir' => 0,
                'created_at' => $sekarang,
                'updated_at' => $sekarang,
            ]);

            $counterBerubah = DB::table('nomor_surat_counter')
                ->where('jenis_surat_id', $jenisSurat->id)
                ->where('tahun', $tahun)
                ->increment('urutan_terakhir', 1, ['updated_at' => $sekarang]);

            if ($counterBerubah !== 1) {
                throw new RuntimeException('Urutan nomor surat tidak berhasil dibuat.');
            }

            $urutan = (int) DB::table('nomor_surat_counter')
                ->where('jenis_surat_id', $jenisSurat->id)
                ->where('tahun', $tahun)
                ->value('urutan_terakhir');
            $urutanFormat = str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);

            return strtr($pengaturan->format_nomor_surat, [
                '{urutan}' => $urutanFormat,
                '{nomor}' => $urutanFormat,
                '{kode}' => $jenisSurat->kode,
                '{bulan_romawi}' => self::BULAN_ROMAWI[(int) $tanggal->format('n')],
                '{tahun}' => (string) $tahun,
            ]);
        });
    }
}
