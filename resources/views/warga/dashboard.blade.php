<x-app-layout>
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">Beranda Warga</h2>
            <p class="mt-1 text-sm text-slate-500">Pantau pengajuan dan layanan surat desa Anda.</p>
        </div>

        @if (! $profilCocok)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                <p class="font-bold">Data penduduk belum terhubung</p>
                <p class="mt-1">NIK pada akun Anda belum cocok dengan data penduduk. Silakan hubungi kantor desa untuk verifikasi sebelum mengajukan surat.</p>
                @if ($teleponDesa)<p class="mt-2 font-semibold">Telepon kantor desa: {{ $teleponDesa }}</p>@endif
            </div>
        @endif

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-7">
            @foreach (\App\Enums\StatusPengajuan::cases() as $status)
                <x-card padding="p-4">
                    <p class="text-xs font-semibold text-slate-500">{{ $status->label() }}</p>
                    <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ $jumlahPerStatus[$status->value] }}</p>
                </x-card>
            @endforeach
        </div>

        <div class="grid gap-5 lg:grid-cols-5">
            <x-card class="lg:col-span-3" padding="p-5 sm:p-7">
                @if ($pengajuanTerakhir)
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pengajuan terakhir</p>
                            <h3 class="mt-2 text-lg font-extrabold text-slate-900">{{ $pengajuanTerakhir->jenisSurat->nama }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $pengajuanTerakhir->tgl_pengajuan->locale('id')->translatedFormat('d F Y') }}</p>
                        </div>
                        <x-badge-status :status="$pengajuanTerakhir->status" />
                    </div>
                    <div class="mt-7 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-5">
                        <p class="text-xs text-slate-500">Kode pengajuan <span class="font-bold text-slate-700">{{ $pengajuanTerakhir->kode_pengajuan }}</span></p>
                        <a href="{{ route('warga.pengajuan.show', $pengajuanTerakhir) }}" class="text-sm font-bold text-primary hover:underline">Lihat detail</a>
                    </div>
                @else
                    <x-empty-state title="Belum ada pengajuan" description="Pengajuan surat yang Anda buat akan muncul di sini." />
                @endif
            </x-card>

            <div class="flex flex-col justify-between gap-5 rounded-3xl bg-primary p-6 text-white shadow-lg shadow-primary/15 lg:col-span-2">
                <div>
                    <span class="grid h-11 w-11 place-items-center rounded-2xl bg-white/15">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14"/></svg>
                    </span>
                    <h3 class="mt-5 text-xl font-extrabold">Butuh surat keterangan?</h3>
                    <p class="mt-2 max-w-xs text-sm leading-6 text-white/75">Ajukan surat secara online dan pantau prosesnya di sini.</p>
                </div>
                <a href="{{ route('warga.pengajuan.create') }}" class="inline-flex w-fit items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-primary transition hover:bg-primary-50">
                    Ajukan Surat
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3.5 10a.75.75 0 0 1 .75-.75h10.19l-3.22-3.22a.75.75 0 1 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 0 1-1.06-1.06l3.22-3.22H4.25A.75.75 0 0 1 3.5 10Z" clip-rule="evenodd"/></svg>
                </a>
            </div>
        </div>

        <x-card padding="p-5 sm:p-6">
            <div class="mb-5 flex items-center justify-between">
                <div><h3 class="font-bold text-slate-900">Riwayat Singkat</h3><p class="mt-1 text-xs text-slate-500">Aktivitas pengajuan surat Anda</p></div>
                <a href="{{ route('warga.pengajuan.index') }}" class="text-xs font-bold text-primary hover:underline">Semua pengajuan</a>
            </div>
            @forelse ($riwayatSingkat as $item)
                <a href="{{ route('warga.pengajuan.show', $item) }}" class="flex items-center gap-3 border-t border-slate-100 py-4 first:border-0 first:pt-0 last:pb-0">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-primary-50 text-primary">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 3h7l5 5v11a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 0v5h5M10 13h6m-6 4h6"/></svg>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-bold text-slate-800">{{ $item->jenisSurat->nama }}</span>
                        <span class="mt-1 block text-xs text-slate-400">{{ $item->kode_pengajuan }} · {{ $item->tgl_pengajuan->locale('id')->translatedFormat('d M Y') }}</span>
                    </span>
                    <x-badge-status :status="$item->status" class="hidden sm:inline-flex" />
                </a>
            @empty
                <p class="border-t border-slate-100 pt-4 text-sm text-slate-500">Belum ada riwayat pengajuan.</p>
            @endforelse
        </x-card>
    </div>
</x-app-layout>
