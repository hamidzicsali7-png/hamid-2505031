<?php

namespace App\Services;

use App\Enums\StatusPengajuan;
use App\Models\JenisSurat;
use App\Models\NomorSuratCounter;
use App\Models\PejabatDesa;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\PengaturanDesa;
use App\Models\SuratTerbit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;

class PengajuanSuratService
{
    public function ajukan(array $data, int $pendudukId): PengajuanSurat
    {
        return DB::transaction(function () use ($data, $pendudukId): PengajuanSurat {
            $penduduk = Penduduk::query()->findOrFail($pendudukId);

            if (! $penduduk->user_id) {
                throw new LogicException('Penduduk harus terhubung ke akun pengguna untuk mengajukan surat.');
            }

            return PengajuanSurat::create([
                'kode_pengajuan' => 'PG-'.Str::upper((string) Str::uuid()),
                'user_id' => $penduduk->user_id,
                'penduduk_id' => $penduduk->id,
                'jenis_surat_id' => $data['jenis_surat_id'],
                'keperluan' => $data['keperluan'],
                'data_tambahan' => $data['data_tambahan'] ?? [],
                'status' => StatusPengajuan::Diajukan,
                'tgl_pengajuan' => now(),
            ]);
        });
    }

    public function ubahStatus(
        PengajuanSurat $pengajuan,
        StatusPengajuan $status,
        ?int $userId = null,
        ?string $catatan = null,
    ): PengajuanSurat {
        return DB::transaction(function () use ($pengajuan, $status, $userId, $catatan): PengajuanSurat {
            $data = ['status' => $status, 'catatan' => $catatan];

            if ($status === StatusPengajuan::Diverifikasi) {
                $data['diverifikasi_oleh'] = $userId;
            }

            if ($status === StatusPengajuan::Disetujui) {
                $data['disetujui_oleh'] = $userId;
            }

            $pengajuan->update($data);

            if ($status === StatusPengajuan::Selesai && ! $pengajuan->suratTerbit()->exists()) {
                $this->terbitkanSurat($pengajuan);
            }

            return $pengajuan->fresh();
        });
    }

    private function terbitkanSurat(PengajuanSurat $pengajuan): void
    {
        $tahun = now()->year;
        $counter = NomorSuratCounter::query()->firstOrCreate(
            ['jenis_surat_id' => $pengajuan->jenis_surat_id, 'tahun' => $tahun],
            ['urutan_terakhir' => 0],
        );
        $counter->increment('urutan_terakhir');
        $urutan = str_pad((string) $counter->fresh()->urutan_terakhir, 4, '0', STR_PAD_LEFT);
        $jenisSurat = $pengajuan->jenisSurat ?? JenisSurat::query()->findOrFail($pengajuan->jenis_surat_id);
        $pengaturan = PengaturanDesa::query()->firstOrFail();
        $pejabat = PejabatDesa::query()
            ->where('jabatan', 'Kepala Desa')
            ->where('aktif', true)
            ->firstOrFail();
        $nomorSurat = strtr($pengaturan->format_nomor_surat, [
            '{nomor}' => $urutan,
            '{kode}' => $jenisSurat->kode,
            '{tahun}' => (string) $tahun,
        ]);

        SuratTerbit::create([
            'pengajuan_surat_id' => $pengajuan->id,
            'nomor_surat' => $nomorSurat,
            'tgl_terbit' => now()->toDateString(),
            'pejabat_id' => $pejabat->id,
            'kode_verifikasi' => (string) Str::uuid(),
        ]);
    }
}
