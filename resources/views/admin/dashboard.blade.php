<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">Ringkasan Desa</h2>
                <p class="mt-1 text-sm text-slate-500">Pantau aktivitas pelayanan surat minggu ini.</p>
            </div>
            <span class="rounded-xl bg-white px-3.5 py-2 text-xs font-semibold text-slate-500 shadow-sm ring-1 ring-slate-900/5">
                {{ now()->startOfWeek()->locale('id')->translatedFormat('d M') }} – {{ now()->endOfWeek()->locale('id')->translatedFormat('d M Y') }}
            </span>
        </div>

        <div class="grid gap-5 xl:grid-cols-12">
            <x-card class="xl:col-span-4" padding="p-5 sm:p-6">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900">Pengajuan Terbaru</h3>
                        <p class="mt-1 text-xs text-slate-400">Aktivitas warga terkini</p>
                    </div>
                    <a href="{{ route('admin.pengajuan.index') }}" class="text-xs font-bold text-primary hover:underline">Lihat semua</a>
                </div>

                <div class="space-y-4">
                    @forelse ($pengajuanTerbaru as $item)
                        <a href="{{ route('admin.pengajuan.show', $item) }}" class="flex items-center gap-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-violet-100 text-xs font-bold text-violet-700">
                                {{ strtoupper(substr($item->penduduk->nama, 0, 2)) }}
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-bold text-slate-800">{{ $item->penduduk->nama }}</span>
                                <span class="mt-0.5 block truncate text-xs text-slate-500">{{ $item->jenisSurat->nama }}</span>
                            </span>
                            <span class="shrink-0 text-[10px] text-slate-400">{{ $item->tgl_pengajuan?->locale('id')->diffForHumans() }}</span>
                        </a>
                    @empty
                        <p class="rounded-2xl bg-soft px-4 py-6 text-center text-sm text-slate-500">Belum ada pengajuan surat.</p>
                    @endforelse
                </div>
            </x-card>

            <div class="space-y-5 xl:col-span-8">
                <div class="flex items-end justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-slate-900">Daftar Pengajuan</h3>
                        <p class="mt-1 text-xs text-slate-500">{{ $jumlahMingguIni }} pengajuan masuk minggu ini</p>
                    </div>
                    <a href="{{ route('admin.pengajuan.index') }}" class="text-xs font-bold text-primary hover:underline">Kelola pengajuan</a>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <a href="{{ route('admin.pengajuan.index', ['status' => 'diajukan']) }}" class="relative overflow-hidden rounded-3xl bg-primary p-5 text-white shadow-lg shadow-primary/15 sm:p-6">
                        <div class="absolute -right-7 -top-8 h-32 w-32 rounded-full bg-white/10"></div>
                        <div class="relative flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-white/75">Menunggu Verifikasi</p>
                                <p class="mt-3 text-4xl font-extrabold">{{ $menungguVerifikasi }}</p>
                                <p class="mt-2 text-xs text-white/70">Perlu diperiksa oleh operator</p>
                            </div>
                            <span class="grid h-11 w-11 place-items-center rounded-2xl bg-white/15">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 3h7l5 5v11a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 0v5h5M10 13h6m-6 4h4"/></svg>
                            </span>
                        </div>
                    </a>

                    @if (auth()->user()->hasRole('kades'))
                        <a href="{{ route('admin.antrean-persetujuan.index') }}" class="relative overflow-hidden rounded-3xl bg-[#4169df] p-5 text-white shadow-lg shadow-blue-500/15 sm:p-6">
                    @else
                        <a href="{{ route('admin.pengajuan.index', ['status' => 'diverifikasi']) }}" class="relative overflow-hidden rounded-3xl bg-[#4169df] p-5 text-white shadow-lg shadow-blue-500/15 sm:p-6">
                    @endif
                        <div class="absolute -right-7 -top-8 h-32 w-32 rounded-full bg-white/10"></div>
                        <div class="relative flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-white/75">Menunggu TTD Kades</p>
                                <p class="mt-3 text-4xl font-extrabold">{{ $menungguTtdKades }}</p>
                                <p class="mt-2 text-xs text-white/70">Siap untuk persetujuan</p>
                            </div>
                            <span class="grid h-11 w-11 place-items-center rounded-2xl bg-white/15">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15.2 5.2 3.6 3.6M4 20l4.6-.9L19.5 8.2a2.5 2.5 0 0 0-3.6-3.6L5 15.5 4 20Z"/></svg>
                            </span>
                        </div>
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">
                    <x-stat-card title="Total Penduduk" :value="number_format($totalPenduduk, 0, ',', '.')" icon="users" />
                    <x-stat-card title="Pengajuan Bulan Ini" :value="number_format($pengajuanBulanIni, 0, ',', '.')" icon="document" />
                    <x-stat-card title="Surat Selesai" :value="number_format($suratSelesai, 0, ',', '.')" icon="check" />
                    <x-stat-card title="Pengajuan Hari Ini" :value="number_format($pengajuanHariIni, 0, ',', '.')" icon="document" />
                </div>
            </div>
        </div>

        <x-card class="border-l-4 border-warning" padding="p-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="font-bold text-slate-900">Pengajuan Belum Diverifikasi</h3>
                    <p class="mt-1 text-sm text-slate-500">Pengajuan berstatus diajukan yang telah menunggu lebih dari 2 hari.</p>
                </div>
                <span class="rounded-2xl bg-amber-50 px-4 py-2 text-2xl font-extrabold text-warning">
                    {{ number_format($menungguVerifikasiLama, 0, ',', '.') }}
                </span>
            </div>
        </x-card>

        <section class="space-y-4" aria-labelledby="grafik-dashboard-title">
            <div>
                <h3 id="grafik-dashboard-title" class="text-lg font-extrabold text-slate-900">Grafik Pelayanan</h3>
                <p class="mt-1 text-sm text-slate-500">Tren 12 bulan terakhir, termasuk bulan berjalan.</p>
            </div>
            <div class="grid gap-5 xl:grid-cols-2">
                <x-card padding="p-5 sm:p-6">
                    <h4 class="mb-4 font-bold text-slate-900">Surat Terbit per Bulan</h4>
                    <div class="h-64">
                        <canvas
                            role="img"
                            aria-label="Grafik area jumlah surat terbit per bulan"
                            data-dashboard-chart="{{ json_encode(['type' => 'area', 'labels' => $grafik['labelBulan'], 'values' => $grafik['suratTerbit'], 'label' => 'Surat terbit'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}"
                        ></canvas>
                    </div>
                </x-card>
                <x-card padding="p-5 sm:p-6">
                    <h4 class="mb-4 font-bold text-slate-900">Pengajuan Masuk per Bulan</h4>
                    <div class="h-64">
                        <canvas
                            role="img"
                            aria-label="Grafik garis jumlah pengajuan masuk per bulan"
                            data-dashboard-chart="{{ json_encode(['type' => 'line', 'labels' => $grafik['labelBulan'], 'values' => $grafik['pengajuanMasuk'], 'label' => 'Pengajuan masuk'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}"
                        ></canvas>
                    </div>
                </x-card>
                <x-card padding="p-5 sm:p-6">
                    <h4 class="mb-4 font-bold text-slate-900">Surat Terbit per Jenis</h4>
                    <div class="h-64">
                        <canvas
                            role="img"
                            aria-label="Grafik batang jumlah surat terbit per jenis surat"
                            data-dashboard-chart="{{ json_encode(['type' => 'bar', 'labels' => $grafik['labelSuratPerJenis'], 'values' => $grafik['suratPerJenis'], 'label' => 'Surat terbit'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}"
                        ></canvas>
                    </div>
                </x-card>
                <x-card padding="p-5 sm:p-6">
                    <h4 class="mb-4 font-bold text-slate-900">Komposisi Surat Terbit</h4>
                    <div class="h-64">
                        <canvas
                            role="img"
                            aria-label="Grafik donat komposisi surat terbit per jenis surat"
                            data-dashboard-chart="{{ json_encode(['type' => 'doughnut', 'labels' => $grafik['labelKomposisiSurat'], 'values' => $grafik['komposisiSurat'], 'label' => 'Surat terbit'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}"
                        ></canvas>
                    </div>
                </x-card>
            </div>
        </section>
    </div>
</x-app-layout>
