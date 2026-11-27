<x-app-layout>
    @php($fieldLabels = collect($pengajuan->jenisSurat->field_tambahan ?? [])->keyBy('name'))
    <div class="mx-auto max-w-4xl space-y-5">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <a href="{{ route('warga.pengajuan.index') }}" class="text-sm font-semibold text-primary hover:underline">← Kembali ke riwayat</a>
                <h2 class="mt-3 text-2xl font-extrabold tracking-tight text-slate-900">{{ $pengajuan->jenisSurat->nama }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $pengajuan->kode_pengajuan }} · {{ $pengajuan->tgl_pengajuan->locale('id')->translatedFormat('d F Y, H:i') }}</p>
            </div>
            <x-badge-status :status="$pengajuan->status" />
        </div>

        <x-card padding="p-5 sm:p-7">
            <h3 class="font-bold text-slate-900">Informasi Pengajuan</h3>
            <dl class="mt-4 grid gap-5 sm:grid-cols-2">
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Pemohon</dt><dd class="mt-1 font-semibold text-slate-800">{{ $pengajuan->penduduk->nama }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">NIK</dt><dd class="mt-1 font-semibold text-slate-800">{{ $pengajuan->penduduk->nik }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Keperluan</dt><dd class="mt-1 font-semibold text-slate-800">{{ $pengajuan->keperluan }}</dd></div>
                @foreach ($pengajuan->data_tambahan ?? [] as $namaField => $nilai)
                    @if ($nilai !== null && $nilai !== '')
                        <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $fieldLabels->get($namaField)['label'] ?? str($namaField)->replace('_', ' ')->title() }}</dt><dd class="mt-1 font-semibold text-slate-800">{{ $nilai }}</dd></div>
                    @endif
                @endforeach
            </dl>
        </x-card>

        @if ($pengajuan->catatan)
            <div class="rounded-2xl border {{ $pengajuan->status === \App\Enums\StatusPengajuan::Ditolak ? 'border-red-200 bg-red-50 text-red-800' : 'border-amber-200 bg-amber-50 text-amber-900' }} p-5">
                <h3 class="font-bold">{{ $pengajuan->status === \App\Enums\StatusPengajuan::Ditolak ? 'Catatan Penolakan' : 'Catatan Petugas' }}</h3>
                <p class="mt-2 whitespace-pre-line text-sm">{{ $pengajuan->catatan }}</p>
            </div>
        @endif

        <x-card padding="p-5 sm:p-7">
            <h3 class="font-bold text-slate-900">Dokumen Terlampir</h3>
            <div class="mt-4 divide-y divide-slate-100">
                @forelse ($pengajuan->dokumenPengajuan as $dokumen)
                    <div class="flex flex-wrap items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">
                        <div><p class="text-sm font-semibold text-slate-800">{{ $dokumen->nama_dokumen }}</p><p class="mt-1 text-xs text-slate-400">Dokumen privat</p></div>
                        <a href="{{ route('warga.pengajuan.dokumen', [$pengajuan, $dokumen]) }}" class="text-sm font-bold text-primary hover:underline">Unduh</a>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada dokumen yang diunggah.</p>
                @endforelse
            </div>
        </x-card>

        <x-card padding="p-5 sm:p-7">
            <h3 class="font-bold text-slate-900">Perjalanan Status</h3>
            <ol class="mt-5 space-y-5">
                @forelse ($pengajuan->logAktivitas->sortBy('created_at') as $log)
                    <li class="relative flex gap-3">
                        <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full bg-primary ring-4 ring-primary-50"></span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-800">{{ $log->keterangan }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $log->created_at->locale('id')->translatedFormat('d F Y, H:i') }} · {{ $log->user?->name ?? 'Sistem' }}</p>
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-slate-500">Riwayat status belum tersedia.</li>
                @endforelse
            </ol>
        </x-card>

        <div class="flex flex-wrap justify-end gap-3">
            @if ($pengajuan->status === \App\Enums\StatusPengajuan::Draft)
                <a href="{{ route('warga.pengajuan.edit', $pengajuan) }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700">Lanjutkan Draft</a>
            @elseif ($pengajuan->status === \App\Enums\StatusPengajuan::Revisi)
                <a href="{{ route('warga.pengajuan.edit', $pengajuan) }}" class="inline-flex items-center rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white">Perbaiki dan Ajukan Ulang</a>
            @endif
            @if ($pengajuan->status === \App\Enums\StatusPengajuan::Selesai && $pengajuan->suratTerbit)
                <a href="{{ route('warga.pengajuan.pdf', $pengajuan) }}" class="inline-flex items-center rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white">Unduh Surat PDF</a>
            @endif
        </div>
    </div>
</x-app-layout>
