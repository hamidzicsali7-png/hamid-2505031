<?php

namespace App\Services;

use App\Enums\StatusPengajuan;
use App\Models\JenisSurat;
use App\Models\LogAktivitas;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use LogicException;
use RuntimeException;
use Throwable;

class PengajuanSuratService
{
    public function __construct(private readonly AlurPengajuanService $alurPengajuan) {}

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

                if ($pengajuanBaru) {
                    $pengajuan = new PengajuanSurat([
                        'kode_pengajuan' => $this->buatKodePengajuan(),
                        'user_id' => $penduduk->user_id,
                        'penduduk_id' => $penduduk->id,
                        'jenis_surat_id' => $data['jenis_surat_id'],
                        'status' => StatusPengajuan::Draft,
                    ]);
                }

                $pengajuan->fill([
                    'keperluan' => $data['keperluan'],
                    'data_tambahan' => $data['data_tambahan'] ?? [],
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

                if ($pengajuanBaru) {
                    LogAktivitas::create([
                        'user_id' => $penduduk->user_id,
                        'pengajuan_surat_id' => $pengajuan->id,
                        'aksi' => 'pengajuan.dibuat',
                        'keterangan' => "Pengajuan {$pengajuan->kode_pengajuan} disimpan sebagai draft.",
                        'ip_address' => $ipAddress ?? '127.0.0.1',
                    ]);
                }

                if ($aksi === 'ajukan') {
                    $this->alurPengajuan->transisikan(
                        $pengajuan,
                        StatusPengajuan::Diajukan,
                        User::query()->findOrFail($penduduk->user_id),
                        null,
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
}
