<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengajuanSuratController extends Controller
{
    public function index()
    {
        $pengajuan = \App\Models\PengajuanSurat::with(['penduduk', 'jenisSurat'])->latest()->paginate(12);
        return view('admin.pengajuan.index', compact('pengajuan'));
    }

    public function show(\App\Models\PengajuanSurat $pengajuan)
    {
        $pengajuan->load(['penduduk', 'jenisSurat', 'diverifikasiOleh', 'disetujuiOleh']);
        return view('admin.pengajuan.show', compact('pengajuan'));
    }

    public function status(\Illuminate\Http\Request $request, \App\Models\PengajuanSurat $pengajuan, \App\Services\PengajuanSuratService $service)
    {
        $request->validate(['status' => ['required', 'in:diverifikasi,disetujui,selesai,ditolak,revisi'], 'catatan' => ['nullable', 'string', 'max:2000']]);
        $service->ubahStatus($pengajuan, \App\StatusPengajuan::from($request->status), $request->user()->id, $request->catatan);
        return back()->with('status', 'Status pengajuan berhasil diperbarui.');
    }

    public function pdf(\App\Models\PengajuanSurat $pengajuan)
    {
        $pengajuan->load(['penduduk', 'jenisSurat']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('surat.pdf', compact('pengajuan'));
        return $pdf->download('surat-'.$pengajuan->nomor_surat.'.pdf');
    }
}
