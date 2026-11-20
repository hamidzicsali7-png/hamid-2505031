<x-app-layout>
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">Beranda Warga</h2>
        <p class="mt-1 text-sm text-slate-500">Layanan surat desa dalam genggaman Anda.</p>
    </div>
    <div class="grid gap-5 lg:grid-cols-5">
        <x-card class="lg:col-span-3" padding="p-5 sm:p-7">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pengajuan terakhir</p>
                    <h3 class="mt-2 text-lg font-extrabold text-slate-900">Surat Keterangan Domisili</h3>
                    <p class="mt-1 text-sm text-slate-500">Diajukan pada 18 November 2026</p>
                </div>
                <x-badge-status status="diverifikasi" />
            </div>

            <div class="mt-7">
                <div class="flex items-center justify-between text-xs font-semibold">
                    <span class="text-primary">Pengajuan sedang diverifikasi</span>
                    <span class="text-slate-400">2 dari 4 tahap</span>
                </div>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full w-1/2 rounded-full bg-primary"></div>
                </div>
            </div>

            <div class="mt-7 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-5">
                <p class="text-xs text-slate-500">Nomor pengajuan <span class="font-bold text-slate-700">#SKD-2026-0184</span></p>
                <a href="{{ route('warga.pengajuan.index') }}" class="text-sm font-bold text-primary hover:underline">Lihat detail</a>
            </div>
        </x-card>

        <div class="flex flex-col justify-between gap-5 rounded-3xl bg-primary p-6 text-white shadow-lg shadow-primary/15 lg:col-span-2">
            <div>
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-white/15">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14"/></svg>
                </span>
                <h3 class="mt-5 text-xl font-extrabold">Butuh surat keterangan?</h3>
                <p class="mt-2 max-w-xs text-sm leading-6 text-white/75">Ajukan surat secara online. Petugas desa akan membantu prosesnya.</p>
            </div>
            <a href="{{ route('warga.pengajuan.create') }}" class="inline-flex w-fit items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-primary transition hover:bg-primary-50">
                Ajukan Surat
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3.5 10a.75.75 0 0 1 .75-.75h10.19l-3.22-3.22a.75.75 0 1 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 0 1-1.06-1.06l3.22-3.22H4.25A.75.75 0 0 1 3.5 10Z" clip-rule="evenodd"/></svg>
            </a>
        </div>
    </div>

    <x-card padding="p-5 sm:p-6">
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900">Riwayat Singkat</h3>
                <p class="mt-1 text-xs text-slate-500">Aktivitas pengajuan surat Anda</p>
            </div>
            <a href="{{ route('warga.pengajuan.index') }}" class="text-xs font-bold text-primary hover:underline">Semua pengajuan</a>
        </div>

        <div class="divide-y divide-slate-100">
            <div class="flex items-center gap-3 py-4 first:pt-0">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-blue-50 text-blue-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 3h7l5 5v11a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 0v5h5M10 13h6m-6 4h4"/></svg>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-bold text-slate-800">Surat Keterangan Domisili</p>
                    <p class="mt-1 text-xs text-slate-400">18 November 2026 · #SKD-2026-0184</p>
                </div>
                <x-badge-status status="diverifikasi" class="hidden sm:inline-flex" />
            </div>
            <div class="flex items-center gap-3 py-4 last:pb-0">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-emerald-50 text-success">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/></svg>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-bold text-slate-800">Surat Keterangan Usaha</p>
                    <p class="mt-1 text-xs text-slate-400">2 November 2026 · #SKU-2026-0152</p>
                </div>
                <x-badge-status status="selesai" class="hidden sm:inline-flex" />
            </div>
        </div>
    </x-card>
</div>
</x-app-layout>
