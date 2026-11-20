@php
    $pengajuanTerbaru = [
        ['inisial' => 'AL', 'nama' => 'Ayu Lestari', 'surat' => 'Surat Keterangan Domisili', 'waktu' => '8 menit lalu', 'warna' => 'bg-violet-100 text-violet-700'],
        ['inisial' => 'RF', 'nama' => 'Rizky Firmansyah', 'surat' => 'Surat Keterangan Usaha', 'waktu' => '24 menit lalu', 'warna' => 'bg-sky-100 text-sky-700'],
        ['inisial' => 'SN', 'nama' => 'Siti Nurhaliza', 'surat' => 'Surat Keterangan Tidak Mampu', 'waktu' => '1 jam lalu', 'warna' => 'bg-rose-100 text-rose-700'],
        ['inisial' => 'DP', 'nama' => 'Dedi Pratama', 'surat' => 'Surat Pengantar SKCK', 'waktu' => '2 jam lalu', 'warna' => 'bg-amber-100 text-amber-700'],
    ];
@endphp

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
                @foreach ($pengajuanTerbaru as $item)
                    <div class="flex items-center gap-3">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl text-xs font-bold {{ $item['warna'] }}">{{ $item['inisial'] }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-800">{{ $item['nama'] }}</p>
                            <p class="mt-0.5 truncate text-xs text-slate-500">{{ $item['surat'] }}</p>
                        </div>
                        <span class="shrink-0 text-[10px] text-slate-400">{{ $item['waktu'] }}</span>
                    </div>
                @endforeach
            </div>
        </x-card>

        <div class="space-y-5 xl:col-span-8">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <h3 class="font-bold text-slate-900">Daftar Pengajuan</h3>
                    <p class="mt-1 text-xs text-slate-500">Ringkasan minggu ini</p>
                </div>
                <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-success">↑ 12,5% dari minggu lalu</span>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="relative overflow-hidden rounded-3xl bg-primary p-5 text-white shadow-lg shadow-primary/15 sm:p-6">
                    <div class="absolute -right-7 -top-8 h-32 w-32 rounded-full bg-white/10"></div>
                    <div class="relative flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-white/75">Menunggu Verifikasi</p>
                            <p class="mt-3 text-4xl font-extrabold">12</p>
                            <p class="mt-2 text-xs text-white/70">Perlu diperiksa oleh operator</p>
                        </div>
                        <span class="grid h-11 w-11 place-items-center rounded-2xl bg-white/15">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 3h7l5 5v11a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 0v5h5M10 13h6m-6 4h4"/></svg>
                        </span>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-3xl bg-[#4169df] p-5 text-white shadow-lg shadow-blue-500/15 sm:p-6">
                    <div class="absolute -right-7 -top-8 h-32 w-32 rounded-full bg-white/10"></div>
                    <div class="relative flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-white/75">Menunggu TTD Kades</p>
                            <p class="mt-3 text-4xl font-extrabold">4</p>
                            <p class="mt-2 text-xs text-white/70">Siap untuk persetujuan</p>
                        </div>
                        <span class="grid h-11 w-11 place-items-center rounded-2xl bg-white/15">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15.2 5.2 3.6 3.6M4 20l4.6-.9L19.5 8.2a2.5 2.5 0 0 0-3.6-3.6L5 15.5 4 20Z"/></svg>
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">
                <x-stat-card title="Total Penduduk" value="2.450" trend="4,8%" icon="users" />
                <x-stat-card title="Pengajuan Bulan Ini" value="186" trend="8,2%" icon="document" />
                <x-stat-card title="Surat Selesai" value="142" trend="6,4%" icon="check" />
                <x-stat-card title="Rata-rata Proses" value="1,8 hari" trend="0,6 hari" direction="down" caption="lebih cepat" />
            </div>
        </div>
    </div>
</div>
</x-app-layout>
