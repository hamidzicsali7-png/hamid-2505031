<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengajuanSuratController extends Controller
{
    public function index()
    {
        $pengajuan = \App\Models\PengajuanSurat::with('jenisSurat')
            ->whereHas('penduduk', fn ($q) => $q->where('user_id', auth()->id()))
            ->latest()->paginate(10);
        return view('warga.pengajuan.index', compact('pengajuan'));
    }

    public function create()
    {
        $jenisSurat = \App\Models\JenisSurat::where('aktif', true)->get();
        return view('warga.pengajuan.create', compact('jenisSurat'));
    }

    public function store(\App\Http\Requests\PengajuanSuratRequest $request, \App\Services\PengajuanSuratService $service)
    {
        $penduduk = \App\Models\Penduduk::where('user_id', $request->user()->id)->firstOrFail();
        $service->ajukan($request->validated(), $penduduk->id);
        return redirect()->route('warga.pengajuan.index')->with('status', 'Pengajuan berhasil dikirim.');
    }

    public function show(\App\Models\PengajuanSurat $pengajuan)
    {
        $this->authorize('view', $pengajuan);
        $pengajuan->load(['penduduk', 'jenisSurat']);
        return view('warga.pengajuan.show', compact('pengajuan'));
    }
}
