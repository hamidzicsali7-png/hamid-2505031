<x-app-layout>
    <div class="space-y-5">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div><h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Pejabat Desa</h2><p class="mt-1 text-sm text-slate-500">Kelola pejabat dan tanda tangan yang digunakan pada surat terbit.</p></div>
            @can('create', \App\Models\PejabatDesa::class)<a href="{{ route('admin.pejabat-desa.create') }}" class="inline-flex items-center rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white">Tambah Pejabat</a>@endcan
        </div>
        <x-card padding="p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-400"><tr><th class="px-5 py-4 font-semibold">Nama</th><th class="px-5 py-4 font-semibold">Jabatan</th><th class="px-5 py-4 font-semibold">NIP</th><th class="px-5 py-4 font-semibold">Status</th><th class="px-5 py-4 font-semibold">Aksi</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pejabat as $item)
                            <tr>
                                <td class="px-5 py-4"><a href="{{ route('admin.pejabat-desa.show', $item) }}" class="font-bold text-slate-800 hover:text-primary">{{ $item->nama }}</a></td>
                                <td class="px-5 py-4 text-slate-600">{{ $item->jabatan }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $item->nip ?: '—' }}</td>
                                <td class="px-5 py-4"><span class="rounded-full px-3 py-1 text-xs font-bold {{ $item->aktif ? 'bg-emerald-50 text-success' : 'bg-slate-100 text-slate-500' }}">{{ $item->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="whitespace-nowrap px-5 py-4"><a href="{{ route('admin.pejabat-desa.show', $item) }}" class="font-semibold text-primary hover:underline">Detail</a>@can('update', $item)<a href="{{ route('admin.pejabat-desa.edit', $item) }}" class="ml-3 font-semibold text-slate-600 hover:text-primary">Ubah</a>@endcan</td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><x-empty-state title="Belum ada data pejabat" description="Tambahkan pejabat desa untuk pengesahan surat." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($pejabat->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $pejabat->links() }}</div>@endif
        </x-card>
    </div>
</x-app-layout>
