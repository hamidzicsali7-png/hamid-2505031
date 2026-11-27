<?php

namespace App\Http\Controllers;

use App\Models\SuratTerbit;
use Illuminate\View\View;

class SuratVerifikasiController extends Controller
{
    public function show(string $kodeVerifikasi): View
    {
        $suratTerbit = SuratTerbit::query()
            ->with(['pengajuanSurat.jenisSurat', 'pejabatDesa'])
            ->where('kode_verifikasi', $kodeVerifikasi)
            ->firstOrFail();

        return view('surat.verifikasi', compact('suratTerbit'));
    }
}
