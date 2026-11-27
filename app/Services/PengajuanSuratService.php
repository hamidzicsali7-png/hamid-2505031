<?php

namespace App\Services;

use App\Enums\StatusPengajuan;
use App\Models\JenisSurat;
use App\Models\LogAktivitas;
use App\Models\NomorSuratCounter;
use App\Models\PejabatDesa;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\PengaturanDesa;
use App\Models\SuratTerbit;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LogicException;
use RuntimeException;
use Throwable;

class PengajuanSuratService
{
    /**
     * Simpan pengajuan baru atau perbaikan pengajuan yang sudah dimiliki warga.
     *
     * @param  array<string, mixed>  $data
     */
    public function simpan(
        array $data,
        Penduduk $penduduk,
        ?PengajuanSurat $pengajuan = null,
        ?string $ipAddress = null,
    ): PengajuanSurat {
        if (! $penduduk->user_id) {
            throw new LogicException('Penduduk harus terhubung ke akun pengguna untuk mengajukan surat.');
        }

        if ($pengajuan && (
            (int) $pengajuan->penduduk_id !== (int) $penduduk->id
            || (int) $pengajuan->user_id !== (int) $penduduk->user_id
            || ! in_array($pengajuan->status, [StatusPengajuan::Draft, StatusPengajuan::Revisi], true)
        )) {
            throw new LogicException('Pengajuan ini tidak dapat diubah oleh penduduk tersebut.');
        }

        $aksi = $data['aksi'];
        $dokumen = $data['dokumen'] ?? [];
        unset($data['aksi'], $data['dokumen']);
        $fileBaru = [];
        $fileLama = [];

        try {
            $hasil = DB::transaction(function () use ($data, $dokumen, $aksi, $penduduk, $pengajuan, $ipAddress, &$fileBaru, &$fileLama): PengajuanSurat {
                $pengajuanBaru = $pengajuan === null;
                $statusSebelumnya = $pengajuan?->status;

                if ($pengajuanBaru) {
                    $pengajuan = new PengajuanSurat([
                        'kode_pengajuan' => $this->buatKodePengajuan(),
                        'user_id' => $penduduk->user_id,
                        'penduduk_id' => $penduduk->id,
                        'jenis_surat_id' => $data['jenis_surat_id'],
                    ]);
                }

                $statusBaru = $aksi === 'ajukan'
                    ? StatusPengajuan::Diajukan
                    : ($statusSebelumnya ?? StatusPengajuan::Draft);

                $pengajuan->fill([
                    'keperluan' => $data['keperluan'],
                    'data_tambahan' => $data['data_tambahan'] ?? [],
                    'status' => $statusBaru,
                    'catatan' => $aksi === 'ajukan' ? null : $pengajuan->catatan,
                    'tgl_pengajuan' => $pengajuanBaru || $aksi === 'ajukan' ? now() : $pengajuan->tgl_pengajuan,
                ])->save();

                $jenisSurat = JenisSurat::with('syaratSurat')
                    ->findOrFail($pengajuan->jenis_surat_id);

                foreach ($dokumen as $syaratId => $file) {
                    if (! $file instanceof UploadedFile) {
                        continue;
                    }

                    $syarat = $jenisSurat->syaratSurat->firstWhere('id', (int) $syaratId);

                    if (! $syarat) {
                        throw new LogicException('Dokumen yang diunggah tidak sesuai dengan persyaratan surat.');
                    }

                    $path = $file->store('pengajuan/'.$pengajuan->id, 'local');

                    if (! $path) {
                        throw new RuntimeException('Dokumen gagal disimpan ke penyimpanan privat.');
                    }

                    $fileBaru[] = $path;
                    $dokumenTersimpan = $pengajuan->dokumenPengajuan()
                        ->where('nama_dokumen', $syarat->nama_dokumen)
                        ->first();

                    if ($dokumenTersimpan) {
                        $fileLama[] = $dokumenTersimpan->file_path;
                        $dokumenTersimpan->update(['file_path' => $path]);
                    } else {
                        $pengajuan->dokumenPengajuan()->create([
                            'nama_dokumen' => $syarat->nama_dokumen,
                            'file_path' => $path,
                        ]);
                    }
                }

                if ($statusSebelumnya !== $statusBaru) {
                    $this->catatPerubahanStatus(
                        $pengajuan,
                        $statusSebelumnya,
                        $statusBaru,
                        $penduduk->user_id,
                        $ipAddress,
                    );
                }

                return $pengajuan->fresh(['jenisSurat', 'dokumenPengajuan']);
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($fileBaru);

            throw $exception;
        }

        Storage::disk('local')->delete($fileLama);

        return $hasil;
    }

    public function ubahStatus(
        PengajuanSurat $pengajuan,
        StatusPengajuan $status,
        ?int $userId = null,
        ?string $catatan = null,
        ?string $ipAddress = null,
    ): PengajuanSurat {
        return DB::transaction(function () use ($pengajuan, $status, $userId, $catatan, $ipAddress): PengajuanSurat {
            $statusSebelumnya = $pengajuan->status;
            $pengajuan->update(['status' => $status, 'catatan' => $catatan]);

            if ($status === StatusPengajuan::Diverifikasi) {
                $pengajuan->update(['diverifikasi_oleh' => $userId]);
            }

            if ($status === StatusPengajuan::Disetujui) {
                $pengajuan->update(['disetujui_oleh' => $userId]);
            }

            if ($statusSebelumnya !== $status) {
                $this->catatPerubahanStatus($pengajuan, $statusSebelumnya, $status, $userId, $ipAddress, $catatan);
            }

            if ($status === StatusPengajuan::Selesai && ! $pengajuan->suratTerbit()->exists()) {
                $this->terbitkanSurat($pengajuan);
            }

            return $pengajuan->fresh();
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function buatKodePengajuan(): string
    {
        $sekarang = now();
        $tanggal = $sekarang->toDateString();

        DB::table('pengajuan_kode_counter')->insertOrIgnore([
            'tanggal' => $tanggal,
            'urutan_terakhir' => 0,
            'created_at' => $sekarang,
            'updated_at' => $sekarang,
        ]);

        $berhasil = DB::table('pengajuan_kode_counter')
            ->where('tanggal', $tanggal)
            ->update([
                'urutan_terakhir' => DB::raw('urutan_terakhir + 1'),
                'updated_at' => $sekarang,
            ]);

        if ($berhasil !== 1) {
            throw new RuntimeException('Nomor pengajuan tidak berhasil dibuat.');
        }

        $urutan = (int) DB::table('pengajuan_kode_counter')
            ->where('tanggal', $tanggal)
            ->value('urutan_terakhir');

        return 'PGJ-'.$sekarang->format('Ymd').'-'.str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);
    }

    private function catatPerubahanStatus(
        PengajuanSurat $pengajuan,
        ?StatusPengajuan $statusSebelumnya,
        StatusPengajuan $statusBaru,
        ?int $userId,
        ?string $ipAddress,
        ?string $catatan = null,
    ): void {
        $keterangan = $statusSebelumnya
            ? "Status pengajuan {$pengajuan->kode_pengajuan} berubah dari {$statusSebelumnya->label()} menjadi {$statusBaru->label()}."
            : "Pengajuan {$pengajuan->kode_pengajuan} dibuat dengan status {$statusBaru->label()}.";

        if ($catatan) {
            $keterangan .= ' Catatan: '.$catatan;
        }

        LogAktivitas::create([
            'user_id' => $userId,
            'pengajuan_surat_id' => $pengajuan->id,
            'aksi' => 'pengajuan.status',
            'keterangan' => $keterangan,
            'ip_address' => $ipAddress ?? '127.0.0.1',
        ]);
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
