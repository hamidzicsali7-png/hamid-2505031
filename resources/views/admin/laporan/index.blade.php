<x-app-layout>
    @php
        $parameterEkspor = $filter;
        $totalLaporan = $rekapStatus->sum('jumlah');
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">Laporan Pelayanan Surat</h2>
                <p class="mt-1 text-sm text-slate-500">Rekap pengajuan berdasarkan periode, jenis surat, dan status.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.laporan.excel', $parameterEkspor) }}" class="rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-800">Ekspor Excel</a>
                <a href="{{ route('admin.laporan.pdf', $parameterEkspor) }}" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white transition hover:bg-primary-800">Ekspor PDF</a>
            </div>
        </div>

        <x-card padding="p-5 sm:p-6">
            <form method="GET" action="{{ route('admin.laporan.index') }}" x-data="{ periode: '{{ $filter['periode'] }}' }" class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <label for="periode" class="mb-1.5 block text-sm font-semibold text-slate-700">Periode laporan</label>
                        <select id="periode" name="periode" x-model="periode" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                            <option value="bulanan">Bulanan</option>
                            <option value="tahunan">Tahunan</option>
                            <option value="rentang">Rentang tanggal</option>
                        </select>
                    </div>
                    <div x-show="periode === 'bulanan'">
                        <label for="bulan" class="mb-1.5 block text-sm font-semibold text-slate-700">Bulan</label>
                        <select id="bulan" name="bulan" x-bind:required="periode === 'bulanan'" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                            @foreach (range(1, 12) as $bulan)
                                <option value="{{ $bulan }}" @selected($filter['bulan'] == $bulan)>{{ \Illuminate\Support\Carbon::createFromDate(2026, $bulan, 1)->locale('id')->translatedFormat('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="periode !== 'rentang'">
                        <label for="tahun" class="mb-1.5 block text-sm font-semibold text-slate-700">Tahun</label>
                        <input id="tahun" name="tahun" type="number" min="2000" max="2100" value="{{ $filter['tahun'] ?: now()->year }}" x-bind:required="periode !== 'rentang'" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label for="jenis_surat_id" class="mb-1.5 block text-sm font-semibold text-slate-700">Jenis surat</label>
                        <select id="jenis_surat_id" name="jenis_surat_id" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                            <option value="">Semua jenis</option>
                            @foreach ($jenisSurat as $jenis)
                                <option value="{{ $jenis->id }}" @selected($filter['jenis_surat_id'] == $jenis->id)>{{ $jenis->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="periode === 'rentang'">
                        <label for="tanggal_mulai" class="mb-1.5 block text-sm font-semibold text-slate-700">Tanggal mulai</label>
                        <input id="tanggal_mulai" name="tanggal_mulai" type="date" value="{{ $filter['tanggal_mulai'] }}" x-bind:required="periode === 'rentang'" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                        @error('tanggal_mulai')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>
                    <div x-show="periode === 'rentang'">
                        <label for="tanggal_selesai" class="mb-1.5 block text-sm font-semibold text-slate-700">Tanggal selesai</label>
                        <input id="tanggal_selesai" name="tanggal_selesai" type="date" value="{{ $filter['tanggal_selesai'] }}" x-bind:required="periode === 'rentang'" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                        @error('tanggal_selesai')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white transition hover:bg-primary-800">Tampilkan laporan</button>
                    <span class="text-sm text-slate-500">
                        Periode {{ $tanggalMulai->locale('id')->translatedFormat('d F Y') }} – {{ $tanggalSelesai->locale('id')->translatedFormat('d F Y') }}
                    </span>
                </div>
            </form>
        </x-card>

        <div class="grid gap-5 lg:grid-cols-2">
            <x-card padding="p-0">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="font-bold text-slate-900">Rekap per Jenis Surat</h3>
                    <p class="mt-1 text-xs text-slate-500">{{ number_format($totalLaporan, 0, ',', '.') }} pengajuan dalam periode ini</p>
                </div>
                <x-table>
                    <thead>
                        <tr class="bg-soft text-left text-xs uppercase tracking-wide text-slate-500">
                            <th class="px-5 py-3 font-bold">Jenis surat</th>
                            <th class="px-5 py-3 text-right font-bold">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($rekapJenis as $item)
                            <tr>
                                <td class="px-5 py-3.5 text-sm font-medium text-slate-700">{{ $item->nama }}</td>
                                <td class="px-5 py-3.5 text-right text-sm font-bold text-slate-900">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-5 py-8 text-center text-sm text-slate-500">Belum ada jenis surat.</td></tr>
                        @endforelse
                    </tbody>
                </x-table>
            </x-card>

            <x-card padding="p-0">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="font-bold text-slate-900">Rekap per Status</h3>
                    <p class="mt-1 text-xs text-slate-500">Status pengajuan pada periode terpilih</p>
                </div>
                <x-table>
                    <thead>
                        <tr class="bg-soft text-left text-xs uppercase tracking-wide text-slate-500">
                            <th class="px-5 py-3 font-bold">Status</th>
                            <th class="px-5 py-3 text-right font-bold">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($rekapStatus as $item)
                            <tr>
                                <td class="px-5 py-3.5 text-sm font-medium text-slate-700">{{ $item->label }}</td>
                                <td class="px-5 py-3.5 text-right text-sm font-bold text-slate-900">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
            </x-card>
        </div>
    </div>
</x-app-layout>
