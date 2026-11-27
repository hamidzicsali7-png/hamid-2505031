<x-app-layout>
    <div class="space-y-5">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Data Penduduk</h2>
                <p class="mt-1 text-sm text-slate-500">Kelola data warga yang terdaftar di desa.</p>
            </div>
            @can('create', \App\Models\Penduduk::class)
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.penduduk.template') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Unduh Template</a>
                    <a href="{{ route('admin.penduduk.create') }}" class="inline-flex items-center rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-800">Tambah Penduduk</a>
                </div>
            @endcan
        </div>

        @can('create', \App\Models\Penduduk::class)
            <x-card padding="p-4 sm:p-5">
                <form method="POST" action="{{ route('admin.penduduk.import') }}" enctype="multipart/form-data" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    @csrf
                    <div class="flex-1">
                        <label for="file-impor" class="mb-1.5 block text-sm font-semibold text-slate-700">Impor Excel atau CSV</label>
                        <input id="file-impor" type="file" name="file" accept=".xlsx,.xls,.csv,.txt" required class="block w-full rounded-xl border border-slate-200 bg-white text-sm text-slate-600 file:mr-4 file:border-0 file:bg-primary-50 file:px-4 file:py-2.5 file:font-semibold file:text-primary">
                        @error('file')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>
                    <x-button type="submit">Impor Data</x-button>
                </form>
            </x-card>
        @endcan

        <x-card padding="p-4 sm:p-5">
            <form method="GET" action="{{ route('admin.penduduk.index') }}" class="grid gap-3 md:grid-cols-[minmax(0,1fr)_160px_160px_auto_auto]">
                <div>
                    <label for="q" class="sr-only">Cari nama, NIK, atau nomor KK</label>
                    <input id="q" name="q" value="{{ request('q') }}" placeholder="Cari nama, NIK, atau nomor KK" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label for="rt" class="sr-only">Filter RT</label>
                    <select id="rt" name="rt" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                        <option value="">Semua RT</option>
                        @foreach ($daftarRt as $rt)
                            <option value="{{ $rt }}" @selected(request('rt') === $rt)>RT {{ $rt }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="rw" class="sr-only">Filter RW</label>
                    <select id="rw" name="rw" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                        <option value="">Semua RW</option>
                        @foreach ($daftarRw as $rw)
                            <option value="{{ $rw }}" @selected(request('rw') === $rw)>RW {{ $rw }}</option>
                        @endforeach
                    </select>
                </div>
                <x-button type="submit">Cari</x-button>
                <a href="{{ route('admin.penduduk.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a>
            </form>
        </x-card>

        <x-card padding="p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-5 py-4 font-semibold">Nama / NIK</th>
                            <th class="px-5 py-4 font-semibold">No. KK</th>
                            <th class="px-5 py-4 font-semibold">Alamat</th>
                            <th class="px-5 py-4 font-semibold">RT / RW</th>
                            <th class="px-5 py-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($penduduk as $warga)
                            <tr class="text-slate-600">
                                <td class="px-5 py-4">
                                    <a href="{{ route('admin.penduduk.show', $warga) }}" class="font-bold text-slate-800 hover:text-primary">{{ $warga->nama }}</a>
                                    <p class="mt-1 text-xs text-slate-400">{{ $warga->nik }}</p>
                                </td>
                                <td class="px-5 py-4">{{ $warga->no_kk }}</td>
                                <td class="max-w-xs truncate px-5 py-4">{{ $warga->alamat }}</td>
                                <td class="px-5 py-4">{{ $warga->rt }} / {{ $warga->rw }}</td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <a href="{{ route('admin.penduduk.show', $warga) }}" class="font-semibold text-primary hover:underline">Detail</a>
                                    @can('update', $warga)
                                        <a href="{{ route('admin.penduduk.edit', $warga) }}" class="ml-3 font-semibold text-slate-600 hover:text-primary">Ubah</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><x-empty-state title="Penduduk belum ditemukan" description="Coba ubah kata pencarian atau filter RT/RW." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($penduduk->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">{{ $penduduk->links() }}</div>
            @endif
        </x-card>
    </div>
</x-app-layout>
