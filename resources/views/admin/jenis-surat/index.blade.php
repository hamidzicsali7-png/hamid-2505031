<x-app-layout>
    <div class="space-y-5">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Jenis Surat</h2>
                <p class="mt-1 text-sm text-slate-500">Atur jenis layanan surat, persyaratan, dan kolom tambahan.</p>
            </div>
            @can('create', \App\Models\JenisSurat::class)
                <a href="{{ route('admin.jenis-surat.create') }}" class="inline-flex items-center rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-800">Tambah Jenis Surat</a>
            @endcan
        </div>
        <x-card padding="p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-400">
                        <tr><th class="px-5 py-4 font-semibold">Nama / Kode</th><th class="px-5 py-4 font-semibold">Persyaratan</th><th class="px-5 py-4 font-semibold">Status</th><th class="px-5 py-4 font-semibold">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($jenisSurat as $jenis)
                            <tr>
                                <td class="px-5 py-4"><a href="{{ route('admin.jenis-surat.show', $jenis) }}" class="font-bold text-slate-800 hover:text-primary">{{ $jenis->nama }}</a><p class="mt-1 text-xs text-slate-400">{{ $jenis->kode }}</p></td>
                                <td class="px-5 py-4 text-slate-600">{{ $jenis->syarat_surat_count }} dokumen</td>
                                <td class="px-5 py-4"><span class="rounded-full px-3 py-1 text-xs font-bold {{ $jenis->aktif ? 'bg-emerald-50 text-success' : 'bg-slate-100 text-slate-500' }}">{{ $jenis->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <a href="{{ route('admin.jenis-surat.show', $jenis) }}" class="font-semibold text-primary hover:underline">Detail</a>
                                    @can('update', $jenis)<a href="{{ route('admin.jenis-surat.edit', $jenis) }}" class="ml-3 font-semibold text-slate-600 hover:text-primary">Ubah</a>@endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4"><x-empty-state title="Belum ada jenis surat" description="Tambahkan jenis surat untuk mulai mengatur layanan." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($jenisSurat->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $jenisSurat->links() }}</div>@endif
        </x-card>
    </div>
</x-app-layout>
