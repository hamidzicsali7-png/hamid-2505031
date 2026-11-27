<x-app-layout>
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">Arsip Surat Terbit</h2>
            <p class="mt-1 text-sm text-slate-500">Cari dan unduh surat yang telah diterbitkan.</p>
        </div>

        <x-card padding="p-5 sm:p-6">
            <form method="GET" action="{{ route('admin.arsip-surat.index') }}" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                <div class="sm:col-span-2 xl:col-span-2">
                    <label for="q" class="mb-1.5 block text-sm font-semibold text-slate-700">Pencarian</label>
                    <input id="q" name="q" value="{{ request('q') }}" placeholder="Nomor surat, nama, atau NIK" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label for="jenis_surat_id" class="mb-1.5 block text-sm font-semibold text-slate-700">Jenis surat</label>
                    <select id="jenis_surat_id" name="jenis_surat_id" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                        <option value="">Semua jenis</option>
                        @foreach ($jenisSurat as $jenis)
                            <option value="{{ $jenis->id }}" @selected(request('jenis_surat_id') == $jenis->id)>{{ $jenis->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="bulan" class="mb-1.5 block text-sm font-semibold text-slate-700">Bulan</label>
                    <select id="bulan" name="bulan" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                        <option value="">Semua bulan</option>
                        @foreach (range(1, 12) as $bulan)
                            <option value="{{ $bulan }}" @selected(request('bulan') == $bulan)>{{ \Illuminate\Support\Carbon::createFromDate(2026, $bulan, 1)->locale('id')->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tahun" class="mb-1.5 block text-sm font-semibold text-slate-700">Tahun</label>
                    <select id="tahun" name="tahun" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                        <option value="">Semua tahun</option>
                        @foreach ($tahunTersedia as $tahun)
                            <option value="{{ $tahun }}" @selected(request('tahun') == $tahun)>{{ $tahun }}</option>
                        @endforeach
                        @if ($tahunTersedia->isEmpty())
                            <option value="{{ now()->year }}" @selected(request('tahun') == now()->year)>{{ now()->year }}</option>
                        @endif
                    </select>
                </div>
                <div class="flex items-end gap-2 sm:col-span-2 xl:col-span-5">
                    <button type="submit" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white transition hover:bg-primary-800">Terapkan filter</button>
                    <a href="{{ route('admin.arsip-surat.index') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-soft">Reset</a>
                </div>
            </form>
        </x-card>

        <x-card padding="p-0">
            <x-table>
                <thead>
                    <tr class="bg-soft text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-5 py-4 font-bold">Nomor / tanggal</th>
                        <th class="px-5 py-4 font-bold">Pemohon</th>
                        <th class="px-5 py-4 font-bold">Jenis surat</th>
                        <th class="px-5 py-4 text-right font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($suratTerbit as $surat)
                        <tr class="align-top">
                            <td class="px-5 py-4">
                                <span class="block font-bold text-slate-800">{{ $surat->nomor_surat }}</span>
                                <span class="mt-1 block text-xs text-slate-400">{{ $surat->tgl_terbit->locale('id')->translatedFormat('d F Y') }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="block font-semibold text-slate-700">{{ $surat->pengajuanSurat->penduduk->nama }}</span>
                                <span class="mt-1 block text-xs text-slate-400">NIK {{ $surat->pengajuanSurat->penduduk->nik }}</span>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $surat->pengajuanSurat->jenisSurat->nama }}</td>
                            <td class="px-5 py-4 text-right">
                                @if ($surat->file_pdf)
                                    <a href="{{ route('admin.pengajuan.pdf', ['pengajuan' => $surat->pengajuanSurat, 'unduh' => 1]) }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-50 px-3 py-2 text-xs font-bold text-primary hover:bg-primary-100">
                                        Unduh PDF
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400">PDF belum tersedia</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-sm text-slate-500">Belum ada surat yang sesuai dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-table>
            @if ($suratTerbit->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">{{ $suratTerbit->links() }}</div>
            @endif
        </x-card>
    </div>
</x-app-layout>
