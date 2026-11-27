<?php

namespace App\Services;

use App\Enums\StatusPengajuan;
use App\Exceptions\TransisiPengajuanTidakSah;
use App\Models\JenisSurat;
use App\Models\LogAktivitas;
use App\Models\NomorSuratCounter;
use App\Models\PejabatDesa;
use App\Models\PengajuanSurat;
use App\Models\PengaturanDesa;
use App\Models\SuratTerbit;
use App\Models\User;
use App\Notifications\StatusPengajuanNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AlurPengajuanService
{
    public function transisikan(
        PengajuanSurat $pengajuan,
        StatusPengajuan $statusTujuan,
        User $pelaku,
        ?string $catatan = null,
        ?string $ipAddress = null,
    ): PengajuanSurat {
        $alasan = trim((string) $catatan);

        if (in_array($statusTujuan, [StatusPengajuan::Ditolak, StatusPengajuan::Revisi], true) && $alasan === '') {
            throw ValidationException::withMessages([
                'catatan' => 'Alasan penolakan atau revisi wajib diisi.',
            ]);
        }

        return DB::transaction(function () use ($pengajuan, $statusTujuan, $pelaku, $alasan, $ipAddress): PengajuanSurat {
            $pengajuan = PengajuanSurat::query()
                ->lockForUpdate()
                ->findOrFail($pengajuan->id);
            $statusAsal = $pengajuan->status;

            if (! $this->transisiDiizinkan($pengajuan, $statusAsal, $statusTujuan, $pelaku)) {
                throw new TransisiPengajuanTidakSah(
                    "Transisi {$statusAsal->value} ke {$statusTujuan->value} tidak diizinkan untuk peran ini.",
                );
            }

            $catatanStatus = in_array($statusTujuan, [StatusPengajuan::Ditolak, StatusPengajuan::Revisi], true)
                ? $alasan
                : null;
            $data = [
                'status' => $statusTujuan,
                'catatan' => $catatanStatus,
            ];

            if ($statusTujuan === StatusPengajuan::Diverifikasi) {
                $data['diverifikasi_oleh'] = $pelaku->id;
            }

            if ($statusTujuan === StatusPengajuan::Disetujui) {
                $data['disetujui_oleh'] = $pelaku->id;
            }

            $pengajuan->update($data);

            if ($statusTujuan === StatusPengajuan::Selesai) {
                $this->terbitkanSurat($pengajuan);
            }

            LogAktivitas::create([
                'user_id' => $pelaku->id,
                'pengajuan_surat_id' => $pengajuan->id,
                'aksi' => 'pengajuan.status',
                'keterangan' => "Status pengajuan {$pengajuan->kode_pengajuan} berubah dari {$statusAsal->label()} menjadi {$statusTujuan->label()}."
                    .($catatanStatus ? ' Catatan: '.$catatanStatus : ''),
                'ip_address' => $ipAddress ?? '127.0.0.1',
            ]);

            $pengajuan->user->notify(new StatusPengajuanNotification(
                $pengajuan,
                $statusTujuan,
                $catatanStatus,
            ));

            return $pengajuan->fresh();
        });
    }

    private function transisiDiizinkan(
        PengajuanSurat $pengajuan,
        StatusPengajuan $statusAsal,
        StatusPengajuan $statusTujuan,
        User $pelaku,
    ): bool {
        if (in_array($statusAsal, [StatusPengajuan::Draft, StatusPengajuan::Revisi], true)) {
            return $statusTujuan === StatusPengajuan::Diajukan
                && $pelaku->hasRole('warga')
                && (int) $pengajuan->user_id === (int) $pelaku->id;
        }

        if ($statusAsal === StatusPengajuan::Diajukan) {
            return $pelaku->hasAnyRole(['admin', 'sekretaris'])
                && in_array($statusTujuan, [
                    StatusPengajuan::Diverifikasi,
                    StatusPengajuan::Ditolak,
                    StatusPengajuan::Revisi,
                ], true);
        }

        if ($statusAsal === StatusPengajuan::Diverifikasi) {
            return $pelaku->hasRole('kades')
                && in_array($statusTujuan, [
                    StatusPengajuan::Disetujui,
                    StatusPengajuan::Ditolak,
                    StatusPengajuan::Revisi,
                ], true);
        }

        return $statusAsal === StatusPengajuan::Disetujui
            && $statusTujuan === StatusPengajuan::Selesai
            && $pelaku->hasAnyRole(['admin', 'sekretaris']);
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
        $jenisSurat = JenisSurat::query()->findOrFail($pengajuan->jenis_surat_id);
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
