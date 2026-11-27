<?php

namespace App\Services;

use App\Enums\StatusPengajuan;
use App\Models\JenisSurat;
use App\Models\PejabatDesa;
use App\Models\PengajuanSurat;
use App\Models\PengaturanDesa;
use App\Models\SuratTerbit;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LogicException;
use RuntimeException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Throwable;

class GenerateSuratService
{
    public function __construct(
        private readonly NomorSuratService $nomorSurat,
        private readonly AlurPengajuanService $alurPengajuan,
        private readonly PengaturanDesaService $pengaturanDesa,
    ) {}

    public function terbitkan(
        PengajuanSurat $pengajuan,
        User $penyetuju,
        ?string $ipAddress = null,
    ): SuratTerbit {
        $filePdf = null;

        try {
            return DB::transaction(function () use ($pengajuan, $penyetuju, $ipAddress, &$filePdf): SuratTerbit {
                $pengajuan = PengajuanSurat::query()
                    ->with(['penduduk', 'jenisSurat', 'user'])
                    ->findOrFail($pengajuan->id);

                if ($pengajuan->status !== StatusPengajuan::Disetujui) {
                    throw new LogicException('Surat hanya dapat diterbitkan setelah persetujuan Kepala Desa.');
                }

                if (! $penyetuju->hasRole('kades') || (int) $pengajuan->disetujui_oleh !== (int) $penyetuju->id) {
                    throw new LogicException('Penerbitan surat harus dilakukan sebagai bagian dari persetujuan Kepala Desa.');
                }

                if ($pengajuan->suratTerbit()->exists()) {
                    throw new LogicException('Surat untuk pengajuan ini sudah pernah diterbitkan.');
                }

                $pengaturan = $this->pengaturanDesa->ambil();
                $pejabat = PejabatDesa::query()
                    ->where('jabatan', 'Kepala Desa')
                    ->where('aktif', true)
                    ->firstOrFail();

                $suratTerbit = SuratTerbit::create([
                    'pengajuan_surat_id' => $pengajuan->id,
                    'nomor_surat' => $this->nomorSurat->generate($pengajuan->jenisSurat),
                    'tgl_terbit' => now()->toDateString(),
                    'pejabat_id' => $pejabat->id,
                    'kode_verifikasi' => (string) Str::uuid(),
                ]);
                $filePdf = 'surat-terbit/'.$suratTerbit->kode_verifikasi.'.pdf';

                $this->simpanPdf($pengajuan, $suratTerbit, $pengaturan, $filePdf);
                $suratTerbit->update(['file_pdf' => $filePdf]);

                $this->alurPengajuan->transisikan(
                    $pengajuan,
                    StatusPengajuan::Selesai,
                    $penyetuju,
                    null,
                    $ipAddress,
                );

                return $suratTerbit->fresh(['pengajuanSurat', 'pejabatDesa']);
            });
        } catch (Throwable $exception) {
            if ($filePdf) {
                Storage::disk('local')->delete($filePdf);
            }

            throw $exception;
        }
    }

    public function cetakUlang(PengajuanSurat $pengajuan): SuratTerbit
    {
        return DB::transaction(function () use ($pengajuan): SuratTerbit {
            $pengajuan->loadMissing(['penduduk', 'jenisSurat', 'user', 'suratTerbit.pejabatDesa']);

            if ($pengajuan->status !== StatusPengajuan::Selesai || ! $pengajuan->suratTerbit) {
                throw new LogicException('Cetak ulang hanya tersedia untuk surat yang sudah terbit.');
            }

            $pengaturan = $this->pengaturanDesa->ambil();
            $filePdf = $pengajuan->suratTerbit->file_pdf
                ?: 'surat-terbit/'.$pengajuan->suratTerbit->kode_verifikasi.'.pdf';
            $this->simpanPdf(
                $pengajuan,
                $pengajuan->suratTerbit,
                $pengaturan,
                $filePdf,
            );
            $pengajuan->suratTerbit->update(['file_pdf' => $filePdf]);

            return $pengajuan->suratTerbit->fresh(['pengajuanSurat', 'pejabatDesa']);
        });
    }

    private function simpanPdf(
        PengajuanSurat $pengajuan,
        SuratTerbit $suratTerbit,
        PengaturanDesa $pengaturan,
        string $filePdf,
    ): void {
        $template = $this->templateView($pengajuan->jenisSurat);
        $verificationUrl = route('surat.verifikasi', $suratTerbit->kode_verifikasi);
        $qrCode = QrCode::format('svg')
            ->size(120)
            ->margin(0)
            ->generate($verificationUrl);

        $pdf = Pdf::loadView($template, [
            'pengajuan' => $pengajuan,
            'suratTerbit' => $suratTerbit,
            'pengaturan' => $pengaturan,
            'logoDataUri' => $this->gambarDataUri($pengaturan->logo_path),
            'tandaTanganDataUri' => $this->gambarDataUri($suratTerbit->pejabatDesa->tanda_tangan_path),
            'qrCodeDataUri' => 'data:image/svg+xml;base64,'.base64_encode($qrCode),
            'verificationUrl' => $verificationUrl,
        ])->setPaper('a4', 'portrait');

        if (! Storage::disk('local')->put($filePdf, $pdf->output())) {
            throw new RuntimeException('File PDF surat gagal disimpan.');
        }
    }

    private function templateView(JenisSurat $jenisSurat): string
    {
        if ($jenisSurat->template_view === 'surat.pdf') {
            $templateLama = [
                'SKD' => 'surat.templates.domisili',
                'SKTM' => 'surat.templates.sktm',
                'SKU' => 'surat.templates.usaha',
                'SKL' => 'surat.templates.kelahiran',
                'SKM' => 'surat.templates.kematian',
                'SKCK' => 'surat.templates.pengantar',
            ];
            $jenisSurat->template_view = $templateLama[$jenisSurat->kode] ?? '';
        }

        if (! $jenisSurat->template_view || ! view()->exists($jenisSurat->template_view)) {
            throw new LogicException("Template surat '{$jenisSurat->template_view}' tidak ditemukan.");
        }

        return $jenisSurat->template_view;
    }

    private function gambarDataUri(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            throw new RuntimeException("Berkas gambar '{$path}' tidak ditemukan.");
        }

        $mime = $disk->mimeType($path);

        if (! in_array($mime, ['image/png', 'image/jpeg', 'image/gif', 'image/bmp'], true)) {
            throw new RuntimeException("Format gambar '{$path}' tidak dapat disertakan dalam PDF.");
        }

        return 'data:'.$mime.';base64,'.base64_encode($disk->get($path));
    }
}
