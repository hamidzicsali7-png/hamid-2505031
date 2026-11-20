<?php

namespace App\Services;

use App\Models\PengajuanSurat;
use App\StatusPengajuan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PengajuanSuratService
{
    public function ajukan(array $data, int $pendudukId): PengajuanSurat
    {
        return DB::transaction(fn () => PengajuanSurat::create([
            ...$data,
            'penduduk_id' => $pendudukId,
            'status' => StatusPengajuan::Diajukan,
            'tanggal_pengajuan' => now(),
        ]));
    }

    public function ubahStatus(PengajuanSurat $pengajuan, StatusPengajuan $status, ?int $userId = null, ?string $catatan = null): PengajuanSurat
    {
        $data = ['status' => $status, 'catatan' => $catatan];
        if ($status === StatusPengajuan::Diverifikasi) $data['diverifikasi_oleh'] = $userId;
        if ($status === StatusPengajuan::Disetujui) $data['disetujui_oleh'] = $userId;
        if ($status === StatusPengajuan::Selesai) {
            $data['tanggal_selesai'] = now();
            $data['nomor_surat'] = $pengajuan->nomor_surat ?: $this->buatNomorSurat($pengajuan);
        }
        $pengajuan->update($data);
        return $pengajuan->fresh();
    }

    private function buatNomorSurat(PengajuanSurat $pengajuan): string
    {
        $kode = $pengajuan->jenisSurat->kode;
        $urutan = str_pad((string) PengajuanSurat::whereYear('created_at', now()->year)->count(), 4, '0', STR_PAD_LEFT);
        return sprintf('470/%s/%s/%s', $urutan, $kode, now()->format('Y'));
    }
}
