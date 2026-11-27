<?php

namespace App\Http\Controllers;

use App\Models\SuratTerbit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuratVerifikasiController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);
        $identifikator = trim($validated['q'] ?? '');
        $suratTerbit = $identifikator === ''
            ? null
            : $this->cariSurat($identifikator);

        return $this->halaman($suratTerbit, $identifikator !== '');
    }

    public function show(string $kodeVerifikasi): View
    {
        return $this->halaman($this->cariSurat($kodeVerifikasi), true);
    }

    private function cariSurat(string $identifikator): ?SuratTerbit
    {
        return SuratTerbit::query()
            ->with(['pengajuanSurat.penduduk', 'pengajuanSurat.jenisSurat', 'pejabatDesa'])
            ->where(function ($query) use ($identifikator): void {
                $query->where('kode_verifikasi', $identifikator)
                    ->orWhere('nomor_surat', $identifikator);
            })
            ->first();
    }

    private function halaman(?SuratTerbit $suratTerbit, bool $dicari): View
    {
        $nik = $suratTerbit?->pengajuanSurat?->penduduk?->nik;
        $nikDisamarkan = $nik === null
            ? null
            : (strlen($nik) <= 8
                ? str_repeat('*', strlen($nik))
                : substr($nik, 0, 4).str_repeat('*', strlen($nik) - 8).substr($nik, -4));

        return view('surat.verifikasi', compact('suratTerbit', 'dicari', 'nikDisamarkan'));
    }
}
