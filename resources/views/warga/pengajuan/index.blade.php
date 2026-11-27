<x-app-layout>
    <div class="space-y-5">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div><p class="text-sm text-slate-500">Beranda warga</p><h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Pengajuan Saya</h2></div>
            <a href="{{ route('warga.pengajuan.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-800">+ Ajukan Surat</a>
        </div>

        <x-card padding="p-4 sm:p-5">
            <form method="GET" action="{{ route('warga.pengajuan.index') }}" class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_220px_auto_auto]">
                <div><label for="q" class="sr-only">Cari pengajuan</label><input id="q" name="q" value="{{ request('q') }}" placeholder="Cari kode, keperluan, atau jenis surat" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary"></div>
                <div>
                    <label for="status" class="sr-only">Filter status</label>
                    <select id="status" name="status" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                        <option value="">Semua status</option>
                        @foreach ($statusList as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <x-button type="submit">Cari</x-button>
                <a href="{{ route('warga.pengajuan.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a>
            </form>
        </x-card>

        <div class="grid gap-4 md:grid-cols-2">
            @forelse ($pengajuan as $item)
                <a href="{{ route('warga.pengajuan.show', $item) }}" class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-900/5 transition hover:-translate-y-0.5 hover:shadow-md sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0"><p class="truncate font-bold text-slate-900">{{ $item->jenisSurat->nama }}</p><p class="mt-1 text-xs text-slate-400">{{ $item->kode_pengajuan }}</p></div>
                        <x-badge-status :status="$item->status" />
                    </div>
                    <p class="mt-4 line-clamp-2 text-sm text-slate-600">{{ $item->keperluan }}</p>
                    <p class="mt-5 text-xs text-slate-400">{{ $item->tgl_pengajuan->locale('id')->translatedFormat('d F Y') }}</p>
                </a>
            @empty
                <x-card class="md:col-span-2"><x-empty-state title="Belum ada pengajuan" description="Ajukan surat untuk melihat status dan riwayat prosesnya di sini." action-label="Ajukan Surat" :action-url="route('warga.pengajuan.create')" /></x-card>
            @endforelse
        </div>
        @if ($pengajuan->hasPages())<div>{{ $pengajuan->links() }}</div>@endif
    </div>
</x-app-layout>
