<x-app-layout>
    <div class="mx-auto max-w-4xl space-y-5">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div><a href="{{ route('admin.jenis-surat.index') }}" class="text-sm font-semibold text-primary hover:underline">← Kembali ke jenis surat</a><h2 class="mt-3 text-2xl font-extrabold text-slate-900">{{ $jenisSurat->nama }}</h2><p class="mt-1 text-sm text-slate-500">Kode {{ $jenisSurat->kode }}</p></div>
            @can('update', $jenisSurat)<a href="{{ route('admin.jenis-surat.edit', $jenisSurat) }}" class="inline-flex rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white">Ubah Data</a>@endcan
        </div>
        <x-card padding="p-5 sm:p-7">
            <dl class="grid gap-5 sm:grid-cols-2">
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Status</dt><dd class="mt-1 font-semibold">{{ $jenisSurat->aktif ? 'Aktif' : 'Nonaktif' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Deskripsi</dt><dd class="mt-1 text-slate-700">{{ $jenisSurat->deskripsi ?: '—' }}</dd></div>
            </dl>
            <div class="mt-6 border-t border-slate-100 pt-5"><h3 class="font-bold">Syarat dokumen</h3><ul class="mt-3 list-inside list-disc space-y-1 text-sm text-slate-600">@forelse ($jenisSurat->syaratSurat as $syarat)<li>{{ $syarat->nama_dokumen }}{{ $syarat->wajib ? ' (wajib)' : ' (opsional)' }}</li>@empty<li>Belum ada syarat.</li>@endforelse</ul></div>
            <div class="mt-6 border-t border-slate-100 pt-5"><h3 class="font-bold">Field tambahan</h3><div class="mt-3 flex flex-wrap gap-2">@forelse ($jenisSurat->field_tambahan ?? [] as $field)<span class="rounded-xl bg-primary-50 px-3 py-2 text-xs font-semibold text-primary">{{ $field['label'] }} · {{ $field['type'] }}{{ ($field['required'] ?? false) ? ' · wajib' : '' }}</span>@empty<p class="text-sm text-slate-500">Tidak ada field tambahan.</p>@endforelse</div></div>
        </x-card>
        @can('delete', $jenisSurat)
            <form method="POST" action="{{ route('admin.jenis-surat.destroy', $jenisSurat) }}" onsubmit="return confirm('Hapus jenis surat ini?')">@csrf @method('DELETE')<x-button type="submit" variant="danger">Hapus Jenis Surat</x-button></form>
        @endcan
    </div>
</x-app-layout>
