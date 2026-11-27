<x-app-layout>
    <div class="mx-auto max-w-3xl space-y-5">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div><a href="{{ route('admin.pejabat-desa.index') }}" class="text-sm font-semibold text-primary hover:underline">← Kembali ke pejabat</a><h2 class="mt-3 text-2xl font-extrabold text-slate-900">{{ $pejabatDesa->nama }}</h2><p class="mt-1 text-sm text-slate-500">{{ $pejabatDesa->jabatan }}</p></div>
            @can('update', $pejabatDesa)<a href="{{ route('admin.pejabat-desa.edit', $pejabatDesa) }}" class="inline-flex rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white">Ubah Data</a>@endcan
        </div>
        <x-card padding="p-5 sm:p-7">
            <dl class="grid gap-5 sm:grid-cols-2"><div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">NIP</dt><dd class="mt-1 font-semibold">{{ $pejabatDesa->nip ?: '—' }}</dd></div><div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Status</dt><dd class="mt-1 font-semibold">{{ $pejabatDesa->aktif ? 'Aktif' : 'Nonaktif' }}</dd></div></dl>
            <div class="mt-6 border-t border-slate-100 pt-5"><h3 class="font-bold">Tanda tangan</h3>@if ($pejabatDesa->tanda_tangan_path)<img src="{{ Storage::disk('public')->url($pejabatDesa->tanda_tangan_path) }}" alt="Tanda tangan {{ $pejabatDesa->nama }}" class="mt-3 max-h-40 max-w-full rounded-xl border border-slate-100 bg-white p-3">@else<p class="mt-2 text-sm text-slate-500">Belum ada gambar tanda tangan.</p>@endif</div>
        </x-card>
        @can('delete', $pejabatDesa)<form method="POST" action="{{ route('admin.pejabat-desa.destroy', $pejabatDesa) }}" onsubmit="return confirm('Hapus data pejabat ini?')">@csrf @method('DELETE')<x-button type="submit" variant="danger">Hapus Pejabat</x-button></form>@endcan
    </div>
</x-app-layout>
