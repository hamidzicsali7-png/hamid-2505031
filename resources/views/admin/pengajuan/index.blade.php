<x-app-layout>
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Daftar Pengajuan</h2>
            <p class="mt-1 text-sm text-slate-500">Cari dan pantau proses surat warga.</p>
        </div>

        <form method="GET" class="grid gap-3 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-900/5 sm:grid-cols-2 xl:grid-cols-6">
            <label class="xl:col-span-2">
                <span class="mb-1.5 block text-xs font-semibold text-slate-500">Pencarian</span>
                <input name="q" value="{{ request('q') }}" placeholder="Nama, NIK, atau kode pengajuan" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
            </label>
            <label>
                <span class="mb-1.5 block text-xs font-semibold text-slate-500">Status</span>
                <select name="status" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                    <option value="">Semua status</option>
                    @foreach ($statusPengajuan as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span class="mb-1.5 block text-xs font-semibold text-slate-500">Jenis Surat</span>
                <select name="jenis_surat_id" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                    <option value="">Semua jenis</option>
                    @foreach ($jenisSurat as $jenis)
                        <option value="{{ $jenis->id }}" @selected((string) request('jenis_surat_id') === (string) $jenis->id)>{{ $jenis->nama }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span class="mb-1.5 block text-xs font-semibold text-slate-500">Dari tanggal</span>
                <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
            </label>
            <label>
                <span class="mb-1.5 block text-xs font-semibold text-slate-500">Sampai tanggal</span>
                <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
            </label>
            <div class="flex items-end gap-2 sm:col-span-2 xl:col-span-6">
                <button class="rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white hover:bg-primary-700">Terapkan Filter</button>
                <a href="{{ route('admin.pengajuan.index') }}" class="rounded-xl bg-soft px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-200">Reset</a>
            </div>
        </form>

        <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-900/5">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-5 py-4 font-semibold">Pemohon</th>
                            <th class="px-5 py-4 font-semibold">Kode / Jenis Surat</th>
                            <th class="px-5 py-4 font-semibold">Status</th>
                            <th class="px-5 py-4 font-semibold">Tanggal Pengajuan</th>
                            <th class="px-5 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pengajuan as $item)
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-800">{{ $item->penduduk->nama }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $item->penduduk->nik }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-700">{{ $item->kode_pengajuan }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $item->jenisSurat->nama }}</p>
                                </td>
                                <td class="px-5 py-4"><x-badge-status :status="$item->status" /></td>
                                <td class="px-5 py-4 text-slate-500">{{ $item->tgl_pengajuan?->locale('id')->translatedFormat('d M Y') }}</td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.pengajuan.show', $item) }}" class="font-bold text-primary hover:underline">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-slate-500">Tidak ada pengajuan yang cocok dengan filter.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($pengajuan->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">{{ $pengajuan->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
