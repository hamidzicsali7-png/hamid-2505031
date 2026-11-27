<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Surat - {{ config('app.name', 'SIKADES') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-soft px-4 py-10 font-sans text-slate-800">
    <main class="mx-auto max-w-xl rounded-3xl bg-white p-7 shadow-lg ring-1 ring-slate-900/5 sm:p-10">
        <div class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-emerald-100 text-emerald-700">
            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/></svg>
        </div>
        <p class="mt-4 text-center text-xs font-bold uppercase tracking-widest text-emerald-700">Dokumen terverifikasi</p>
        <h1 class="mt-2 text-center text-2xl font-extrabold text-slate-900">Surat Resmi Desa</h1>
        <dl class="mt-7 space-y-4 rounded-2xl bg-soft p-5 text-sm">
            <div><dt class="text-xs font-semibold text-slate-400">Jenis surat</dt><dd class="mt-1 font-bold">{{ $suratTerbit->pengajuanSurat->jenisSurat->nama }}</dd></div>
            <div><dt class="text-xs font-semibold text-slate-400">Nomor surat</dt><dd class="mt-1 font-bold">{{ $suratTerbit->nomor_surat }}</dd></div>
            <div><dt class="text-xs font-semibold text-slate-400">Tanggal terbit</dt><dd class="mt-1 font-bold">{{ $suratTerbit->tgl_terbit->locale('id')->translatedFormat('d F Y') }}</dd></div>
            <div><dt class="text-xs font-semibold text-slate-400">Penandatangan</dt><dd class="mt-1 font-bold">{{ $suratTerbit->pejabatDesa->nama }} · {{ $suratTerbit->pejabatDesa->jabatan }}</dd></div>
        </dl>
        <p class="mt-5 text-center text-xs text-slate-500">Kode verifikasi: {{ $suratTerbit->kode_verifikasi }}</p>
    </main>
</body>
</html>
