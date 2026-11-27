<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2E2E8F">
    <title>Verifikasi Surat - {{ config('app.name', 'SIKADES') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-soft px-4 py-8 font-sans text-slate-800 sm:py-12">
    <main class="mx-auto max-w-xl">
        <header class="mb-6 text-center">
            <p class="text-sm font-extrabold uppercase tracking-[0.2em] text-primary-700">SIKADES</p>
            <h1 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">Verifikasi Surat Desa</h1>
            <p class="mt-2 text-sm text-slate-500">Periksa keaslian surat dengan kode verifikasi atau nomor surat.</p>
        </header>

        <section class="rounded-3xl bg-white p-5 shadow-lg shadow-slate-900/5 ring-1 ring-slate-900/5 sm:p-8">
            <form method="GET" action="{{ route('surat.verifikasi.form') }}" class="space-y-3">
                <label for="q" class="block text-sm font-bold text-slate-700">Kode verifikasi atau nomor surat</label>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <input
                        id="q"
                        name="q"
                        type="text"
                        value="{{ request('q') }}"
                        maxlength="255"
                        autocomplete="off"
                        placeholder="Masukkan kode atau nomor surat"
                        class="min-w-0 flex-1 rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    >
                    <button type="submit" class="rounded-2xl bg-primary-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        Periksa Surat
                    </button>
                </div>
                @error('q')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </form>

            @if ($dicari)
                @if ($suratTerbit)
                    <section aria-live="polite" class="mt-7 rounded-2xl border border-emerald-200 bg-emerald-50 p-5 sm:p-6">
                        <div class="flex items-start gap-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/></svg>
                            </span>
                            <div>
                                <h2 class="text-lg font-extrabold text-emerald-900">Surat Asli dan Terdaftar</h2>
                                <p class="mt-1 text-sm text-emerald-800">Data surat ini tercatat dalam sistem SIKADES.</p>
                            </div>
                        </div>
                        <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                            <div>
                                <dt class="text-xs font-semibold text-slate-500">Nomor surat</dt>
                                <dd class="mt-1 break-words font-bold text-slate-900">{{ $suratTerbit->nomor_surat }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-slate-500">Jenis surat</dt>
                                <dd class="mt-1 font-bold text-slate-900">{{ $suratTerbit->pengajuanSurat->jenisSurat->nama }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-slate-500">Nama pemohon</dt>
                                <dd class="mt-1 font-bold text-slate-900">{{ $suratTerbit->pengajuanSurat->penduduk->nama }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-slate-500">NIK</dt>
                                <dd class="mt-1 font-bold tracking-wider text-slate-900">{{ $nikDisamarkan }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-slate-500">Tanggal terbit</dt>
                                <dd class="mt-1 font-bold text-slate-900">{{ $suratTerbit->tgl_terbit->locale('id')->translatedFormat('d F Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-slate-500">Penandatangan</dt>
                                <dd class="mt-1 font-bold text-slate-900">{{ $suratTerbit->pejabatDesa->nama }} · {{ $suratTerbit->pejabatDesa->jabatan }}</dd>
                            </div>
                        </dl>
                    </section>
                @else
                    <section aria-live="polite" class="mt-7 rounded-2xl border border-red-200 bg-red-50 p-5 text-center sm:p-6">
                        <span class="mx-auto grid h-10 w-10 place-items-center rounded-full bg-red-100 text-red-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </span>
                        <h2 class="mt-3 text-lg font-extrabold text-red-900">Surat Tidak Ditemukan</h2>
                        <p class="mt-1 text-sm text-red-800">Periksa kembali kode verifikasi atau nomor surat yang Anda masukkan.</p>
                    </section>
                @endif
            @else
                <p class="mt-6 rounded-2xl bg-soft p-4 text-sm leading-6 text-slate-600">
                    Pindai QR Code pada surat atau masukkan kode verifikasi/nomor surat di atas untuk memeriksa status keasliannya.
                </p>
            @endif
        </section>

        <footer class="mt-5 text-center text-xs text-slate-500">
            Layanan verifikasi dokumen resmi desa
        </footer>
    </main>
</body>
</html>
