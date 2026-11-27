<?php

namespace App\Services;

use App\Enums\StatusPengajuan;
use App\Exceptions\TransisiPengajuanTidakSah;
use App\Models\LogAktivitas;
use App\Models\PengajuanSurat;
use App\Models\User;
use App\Notifications\StatusPengajuanNotification;
use Illuminate\Support\Facades\DB;
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

            if ($statusTujuan === StatusPengajuan::Selesai && ! $pengajuan->suratTerbit()->exists()) {
                throw new TransisiPengajuanTidakSah('Pengajuan tidak dapat diselesaikan sebelum surat diterbitkan.');
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

            LogAktivitas::create([
                'user_id' => $pelaku->id,
                'pengajuan_surat_id' => $pengajuan->id,
                'aksi' => 'pengajuan.status',
                'keterangan' => "Status pengajuan {$pengajuan->kode_pengajuan} berubah dari {$statusAsal->label()} menjadi {$statusTujuan->label()}.",
                'ip_address' => $ipAddress ?? '127.0.0.1',
            ]);

            $pengajuan->user->notify(new StatusPengajuanNotification(
                $pengajuan,
                $statusTujuan,
                $catatanStatus,
            ));

            if ($statusTujuan === StatusPengajuan::Disetujui) {
                $suratTerbit = app(GenerateSuratService::class)->terbitkan(
                    $pengajuan->fresh(['jenisSurat', 'penduduk', 'user']),
                    $pelaku,
                    $ipAddress,
                );

                return $suratTerbit->pengajuanSurat;
            }

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
            && $pelaku->hasAnyRole(['admin', 'kades', 'sekretaris']);
    }
}
