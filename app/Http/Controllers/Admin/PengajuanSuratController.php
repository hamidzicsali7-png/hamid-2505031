<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPengajuan;
use App\Http\Controllers\Controller;
use App\Models\DokumenPengajuan;
use App\Models\JenisSurat;
use App\Models\PengajuanSurat;
use App\Services\AlurPengajuanService;
use App\Services\GenerateSuratService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PengajuanSuratController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(StatusPengajuan::class)],
            'jenis_surat_id' => ['nullable', 'integer', Rule::exists('jenis_surat', 'id')],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        $query = PengajuanSurat::query()->with(['penduduk', 'jenisSurat']);

        if ($filter['q'] ?? false) {
            $kataKunci = '%'.$filter['q'].'%';
            $query->where(function ($query) use ($kataKunci): void {
                $query->where('kode_pengajuan', 'like', $kataKunci)
                    ->orWhereHas('penduduk', function ($penduduk) use ($kataKunci): void {
                        $penduduk->where('nama', 'like', $kataKunci)
                            ->orWhere('nik', 'like', $kataKunci);
                    });
            });
        }

        if ($filter['status'] ?? false) {
            $query->where('status', $filter['status']);
        }

        if ($filter['jenis_surat_id'] ?? false) {
            $query->where('jenis_surat_id', $filter['jenis_surat_id']);
        }

        if ($filter['tanggal_mulai'] ?? false) {
            $query->whereDate('tgl_pengajuan', '>=', $filter['tanggal_mulai']);
        }

        if ($filter['tanggal_selesai'] ?? false) {
            $query->whereDate('tgl_pengajuan', '<=', $filter['tanggal_selesai']);
        }

        $pengajuan = $query->latest('tgl_pengajuan')->paginate(12)->withQueryString();

        return view('admin.pengajuan.index', [
            'pengajuan' => $pengajuan,
            'jenisSurat' => JenisSurat::query()->orderBy('nama')->get(),
            'statusPengajuan' => StatusPengajuan::cases(),
        ]);
    }

    public function show(PengajuanSurat $pengajuan)
    {
        $pengajuan->load([
            'penduduk',
            'user',
            'jenisSurat',
            'dokumenPengajuan',
            'diverifikasiOleh',
            'disetujuiOleh',
            'suratTerbit',
            'logAktivitas' => fn ($query) => $query->with('user')->latest(),
        ]);

        return view('admin.pengajuan.show', compact('pengajuan'));
    }

    public function antrean()
    {
        $pengajuan = PengajuanSurat::query()
            ->with(['penduduk', 'jenisSurat'])
            ->where('status', StatusPengajuan::Diverifikasi)
            ->latest('tgl_pengajuan')
            ->paginate(12);

        return view('admin.pengajuan.antrean', compact('pengajuan'));
    }

    public function status(Request $request, PengajuanSurat $pengajuan, AlurPengajuanService $service)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([
                StatusPengajuan::Diverifikasi->value,
                StatusPengajuan::Disetujui->value,
                StatusPengajuan::Ditolak->value,
                StatusPengajuan::Revisi->value,
            ])],
            'catatan' => ['required_if:status,ditolak,revisi', 'nullable', 'string', 'max:2000'],
        ]);
        $service->transisikan(
            $pengajuan,
            StatusPengajuan::from($validated['status']),
            $request->user(),
            $validated['catatan'] ?? null,
            $request->ip(),
        );

        return back()->with('status', 'Status pengajuan berhasil diperbarui.');
    }

    public function pdf(PengajuanSurat $pengajuan, Request $request)
    {
        abort_unless($pengajuan->status === StatusPengajuan::Selesai, 404);
        $suratTerbit = $pengajuan->suratTerbit;
        abort_unless($suratTerbit?->file_pdf && Storage::disk('local')->exists($suratTerbit->file_pdf), 404);

        $namaFile = 'surat-'.$suratTerbit->id.'.pdf';

        if ($request->boolean('unduh')) {
            return Storage::disk('local')->download($suratTerbit->file_pdf, $namaFile, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        return response()->file(Storage::disk('local')->path($suratTerbit->file_pdf), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$namaFile.'"',
        ]);
    }

    public function cetakUlang(PengajuanSurat $pengajuan, GenerateSuratService $service)
    {
        $service->cetakUlang($pengajuan);

        return back()->with('success', 'PDF surat berhasil dicetak ulang.');
    }

    public function dokumen(
        PengajuanSurat $pengajuan,
        DokumenPengajuan $dokumen,
        Request $request,
    ) {
        abort_unless((int) $dokumen->pengajuan_surat_id === (int) $pengajuan->id, 404);
        abort_unless(Storage::disk('local')->exists($dokumen->file_path), 404);

        if ($request->boolean('unduh')) {
            return Storage::disk('local')->download($dokumen->file_path, $dokumen->nama_dokumen);
        }

        return response()->file(Storage::disk('local')->path($dokumen->file_path));
    }
}
