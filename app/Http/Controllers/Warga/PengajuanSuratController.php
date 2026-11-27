<?php

namespace App\Http\Controllers\Warga;

use App\Enums\StatusPengajuan;
use App\Http\Controllers\Controller;
use App\Http\Requests\PengajuanSuratRequest;
use App\Models\DokumenPengajuan;
use App\Models\JenisSurat;
use App\Models\PengajuanSurat;
use App\Models\PengaturanDesa;
use App\Services\PengajuanSuratService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PengajuanSuratController extends Controller
{
    public function index(Request $request): View
    {
        $query = PengajuanSurat::query()
            ->with('jenisSurat')
            ->where('user_id', $request->user()->id);

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($query) use ($search): void {
                $query->where('kode_pengajuan', 'like', "%{$search}%")
                    ->orWhere('keperluan', 'like', "%{$search}%")
                    ->orWhereHas('jenisSurat', fn ($jenis) => $jenis->where('nama', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            $statusValid = array_map(fn (StatusPengajuan $item): string => $item->value, StatusPengajuan::cases());

            if (in_array($status, $statusValid, true)) {
                $query->where('status', $status);
            }
        }

        $pengajuan = $query->latest('tgl_pengajuan')->paginate(10)->withQueryString();

        return view('warga.pengajuan.index', [
            'pengajuan' => $pengajuan,
            'statusList' => StatusPengajuan::cases(),
        ]);
    }

    public function create(Request $request): View
    {
        $jenisSurat = JenisSurat::query()
            ->with('syaratSurat')
            ->where('aktif', true)
            ->orderBy('nama')
            ->get();
        $terpilih = $jenisSurat->firstWhere('id', (int) $request->query('jenis_surat_id'));
        $penduduk = $request->user()->penduduk;
        $teleponDesa = PengaturanDesa::query()->value('telepon');
        $profilCocok = $penduduk && $request->user()->nik === $penduduk->nik;

        return view('warga.pengajuan.create', [
            'jenisSurat' => $jenisSurat,
            'terpilih' => $terpilih,
            'penduduk' => $penduduk,
            'profilCocok' => $profilCocok,
            'teleponDesa' => $teleponDesa,
            'pengajuan' => null,
            'dokumenTerpasang' => collect(),
        ]);
    }

    public function store(PengajuanSuratRequest $request, PengajuanSuratService $service): RedirectResponse
    {
        $penduduk = $request->user()->penduduk;

        if (! $penduduk || $request->user()->nik !== $penduduk->nik) {
            return to_route('warga.pengajuan.create')->with(
                'error',
                'Data penduduk belum terhubung ke akun Anda. Silakan hubungi kantor desa.',
            );
        }

        $pengajuan = $service->simpan($request->validated(), $penduduk, null, $request->ip());

        return to_route('warga.pengajuan.show', $pengajuan)
            ->with('success', $pengajuan->status === StatusPengajuan::Draft
                ? 'Pengajuan berhasil disimpan sebagai draft.'
                : 'Pengajuan berhasil diajukan.');
    }

    public function edit(PengajuanSurat $pengajuan): View
    {
        $this->authorize('update', $pengajuan);
        $pengajuan->load(['jenisSurat.syaratSurat', 'dokumenPengajuan', 'penduduk']);

        return view('warga.pengajuan.create', [
            'jenisSurat' => collect([$pengajuan->jenisSurat]),
            'terpilih' => $pengajuan->jenisSurat,
            'penduduk' => $pengajuan->penduduk,
            'profilCocok' => $pengajuan->penduduk && request()->user()->nik === $pengajuan->penduduk->nik,
            'pengajuan' => $pengajuan,
            'teleponDesa' => null,
            'dokumenTerpasang' => $pengajuan->dokumenPengajuan->keyBy('nama_dokumen'),
        ]);
    }

    public function update(
        PengajuanSuratRequest $request,
        PengajuanSurat $pengajuan,
        PengajuanSuratService $service,
    ): RedirectResponse {
        $this->authorize('update', $pengajuan);
        $penduduk = $request->user()->penduduk;

        if (
            ! $penduduk
            || $request->user()->nik !== $penduduk->nik
            || (int) $pengajuan->penduduk_id !== (int) $penduduk->id
        ) {
            abort(403);
        }

        $pengajuan = $service->simpan($request->validated(), $penduduk, $pengajuan, $request->ip());

        return to_route('warga.pengajuan.show', $pengajuan)
            ->with('success', $request->validated('aksi') === 'draft'
                ? 'Perubahan pengajuan berhasil disimpan.'
                : 'Perbaikan berhasil diajukan kembali.');
    }

    public function show(PengajuanSurat $pengajuan): View
    {
        $this->authorize('view', $pengajuan);
        $pengajuan->load([
            'penduduk',
            'jenisSurat',
            'dokumenPengajuan',
            'suratTerbit',
            'logAktivitas.user',
        ]);

        return view('warga.pengajuan.show', compact('pengajuan'));
    }

    public function downloadDokumen(PengajuanSurat $pengajuan, DokumenPengajuan $dokumen)
    {
        $this->authorize('view', $pengajuan);
        $dokumen = $pengajuan->dokumenPengajuan()->whereKey($dokumen->id)->firstOrFail();

        abort_unless(Storage::disk('local')->exists($dokumen->file_path), 404);

        $extension = pathinfo($dokumen->file_path, PATHINFO_EXTENSION);
        $namaFile = Str::slug($dokumen->nama_dokumen).'.'.$extension;

        return Storage::disk('local')->download($dokumen->file_path, $namaFile);
    }

    public function pdf(PengajuanSurat $pengajuan, Request $request)
    {
        $this->authorize('view', $pengajuan);
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
}
