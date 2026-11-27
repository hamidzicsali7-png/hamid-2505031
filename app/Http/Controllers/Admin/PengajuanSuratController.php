<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPengajuan;
use App\Http\Controllers\Controller;
use App\Models\PengajuanSurat;
use App\Services\PengajuanSuratService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PengajuanSuratController extends Controller
{
    public function index()
    {
        $pengajuan = PengajuanSurat::with(['penduduk', 'jenisSurat'])->latest()->paginate(12);
        return view('admin.pengajuan.index', compact('pengajuan'));
    }

    public function show(PengajuanSurat $pengajuan)
    {
        $pengajuan->load(['penduduk', 'jenisSurat', 'diverifikasiOleh', 'disetujuiOleh']);
        return view('admin.pengajuan.show', compact('pengajuan'));
    }

    public function status(Request $request, PengajuanSurat $pengajuan, PengajuanSuratService $service)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([
                StatusPengajuan::Diverifikasi->value,
                StatusPengajuan::Disetujui->value,
                StatusPengajuan::Selesai->value,
                StatusPengajuan::Ditolak->value,
                StatusPengajuan::Revisi->value,
            ])],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);
        $service->ubahStatus($pengajuan, StatusPengajuan::from($validated['status']), $request->user()->id, $validated['catatan'] ?? null);

        return back()->with('status', 'Status pengajuan berhasil diperbarui.');
    }

    public function pdf(PengajuanSurat $pengajuan)
    {
        $pengajuan->load(['penduduk', 'jenisSurat', 'suratTerbit.pejabatDesa']);
        abort_unless($pengajuan->suratTerbit, 404);

        $suratTerbit = $pengajuan->suratTerbit;
        $pdf = Pdf::loadView('surat.pdf', compact('pengajuan', 'suratTerbit'));

        return $pdf->download('surat-'.$suratTerbit->nomor_surat.'.pdf');
    }
}
