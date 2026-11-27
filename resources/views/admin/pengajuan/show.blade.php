<x-app-layout>
    @php
        $status = $pengajuan->status;
        $peran = auth()->user();
        $opsiStatus = [];

        if ($status === \App\Enums\StatusPengajuan::Diajukan && $peran->hasAnyRole(['admin', 'sekretaris'])) {
            $opsiStatus = [
                \App\Enums\StatusPengajuan::Diverifikasi->value => 'Tandai sudah diverifikasi',
                \App\Enums\StatusPengajuan::Ditolak->value => 'Tolak pengajuan',
                \App\Enums\StatusPengajuan::Revisi->value => 'Minta revisi',
            ];
        } elseif ($status === \App\Enums\StatusPengajuan::Diverifikasi && $peran->hasRole('kades')) {
            $opsiStatus = [
                \App\Enums\StatusPengajuan::Disetujui->value => 'Setujui pengajuan',
                \App\Enums\StatusPengajuan::Ditolak->value => 'Tolak pengajuan',
                \App\Enums\StatusPengajuan::Revisi->value => 'Minta revisi',
            ];
        }
    @endphp

    <div class="space-y-5">
        <a href="{{ route('admin.pengajuan.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-primary hover:underline">← Kembali ke daftar</a>

        <div class="grid items-start gap-5 xl:grid-cols-3">
            <div class="space-y-5 xl:col-span-2">
                <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-900/5 sm:p-8">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $pengajuan->kode_pengajuan }}</p>
                            <h2 class="mt-2 text-2xl font-extrabold text-slate-900">{{ $pengajuan->jenisSurat->nama }}</h2>
                        </div>
                        <x-badge-status :status="$pengajuan->status" />
                    </div>

                    <div class="mt-7 grid gap-5 text-sm sm:grid-cols-2">
                        <div><p class="text-xs font-semibold text-slate-400">Nama pemohon</p><p class="mt-1 font-bold text-slate-800">{{ $pengajuan->penduduk->nama }}</p></div>
                        <div><p class="text-xs font-semibold text-slate-400">NIK</p><p class="mt-1 font-bold text-slate-800">{{ $pengajuan->penduduk->nik }}</p></div>
                        <div><p class="text-xs font-semibold text-slate-400">Nomor KK</p><p class="mt-1 font-bold text-slate-800">{{ $pengajuan->penduduk->no_kk }}</p></div>
                        <div><p class="text-xs font-semibold text-slate-400">Nomor HP</p><p class="mt-1 font-bold text-slate-800">{{ $pengajuan->user->no_hp ?: '—' }}</p></div>
                        <div><p class="text-xs font-semibold text-slate-400">Tempat, tanggal lahir</p><p class="mt-1 font-bold text-slate-800">{{ $pengajuan->penduduk->tempat_lahir }}, {{ $pengajuan->penduduk->tgl_lahir?->locale('id')->translatedFormat('d F Y') }}</p></div>
                        <div><p class="text-xs font-semibold text-slate-400">Pekerjaan</p><p class="mt-1 font-bold text-slate-800">{{ $pengajuan->penduduk->pekerjaan ?: '—' }}</p></div>
                        <div class="sm:col-span-2"><p class="text-xs font-semibold text-slate-400">Alamat</p><p class="mt-1 font-bold text-slate-800">{{ $pengajuan->penduduk->alamat }}, RT {{ $pengajuan->penduduk->rt }} / RW {{ $pengajuan->penduduk->rw }}{{ $pengajuan->penduduk->dusun ? ', '.$pengajuan->penduduk->dusun : '' }}</p></div>
                        <div><p class="text-xs font-semibold text-slate-400">Tanggal pengajuan</p><p class="mt-1 font-bold text-slate-800">{{ $pengajuan->tgl_pengajuan?->locale('id')->translatedFormat('d F Y H:i') }}</p></div>
                        <div class="sm:col-span-2"><p class="text-xs font-semibold text-slate-400">Keperluan</p><p class="mt-1 whitespace-pre-line font-medium text-slate-700">{{ $pengajuan->keperluan }}</p></div>
                    </div>

                    @if ($pengajuan->data_tambahan)
                        <div class="mt-6 border-t border-slate-100 pt-5">
                            <h3 class="text-sm font-bold text-slate-800">Data tambahan</h3>
                            <dl class="mt-3 grid gap-4 sm:grid-cols-2">
                                @foreach ($pengajuan->data_tambahan as $label => $nilai)
                                    <div><dt class="text-xs text-slate-400">{{ str($label)->replace('_', ' ')->title() }}</dt><dd class="mt-1 text-sm font-medium text-slate-700">{{ is_array($nilai) ? implode(', ', $nilai) : $nilai }}</dd></div>
                                @endforeach
                            </dl>
                        </div>
                    @endif

                    @if ($pengajuan->catatan)
                        <div class="mt-6 rounded-2xl border border-amber-100 bg-amber-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-amber-700">Catatan status saat ini</p>
                            <p class="mt-1 whitespace-pre-line text-sm text-amber-900">{{ $pengajuan->catatan }}</p>
                        </div>
                    @endif
                </section>

                <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-900/5">
                    <h3 class="font-bold text-slate-900">Dokumen Persyaratan</h3>
                    @forelse ($pengajuan->dokumenPengajuan as $dokumen)
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-soft p-4">
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $dokumen->nama_dokumen }}</p>
                                <p class="mt-1 text-xs text-slate-400">Berkas disimpan secara privat</p>
                            </div>
                            <div class="flex gap-2">
                                <a target="_blank" rel="noopener" href="{{ route('admin.pengajuan.dokumen', [$pengajuan, $dokumen]) }}" class="rounded-xl bg-white px-3 py-2 text-xs font-bold text-primary ring-1 ring-slate-200">Pratinjau</a>
                                <a href="{{ route('admin.pengajuan.dokumen', [$pengajuan, $dokumen, 'unduh' => 1]) }}" class="rounded-xl bg-primary px-3 py-2 text-xs font-bold text-white">Unduh</a>
                            </div>
                        </div>
                    @empty
                        <p class="mt-3 rounded-2xl bg-soft px-4 py-5 text-sm text-slate-500">Tidak ada dokumen terlampir.</p>
                    @endforelse
                </section>

                <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-900/5">
                    <h3 class="font-bold text-slate-900">Riwayat Status</h3>
                    <ol class="mt-5 space-y-5">
                        @forelse ($pengajuan->logAktivitas as $log)
                            <li class="relative flex gap-3">
                                <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-primary"></span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-800">{{ $log->keterangan }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $log->user?->name ?? 'Sistem' }} · {{ $log->created_at->locale('id')->diffForHumans() }}</p>
                                </div>
                            </li>
                        @empty
                            <li class="text-sm text-slate-500">Belum ada riwayat perubahan.</li>
                        @endforelse
                    </ol>
                </section>
            </div>

            <aside class="space-y-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-900/5">
                <h3 class="font-bold text-slate-900">Tindakan</h3>
                @if ($opsiStatus)
                    @if ($errors->any())
                        <div role="alert" class="rounded-xl bg-red-50 px-3 py-2 text-xs text-danger">{{ $errors->first() }}</div>
                    @endif
                    <form method="POST" action="{{ route('admin.pengajuan.status', $pengajuan) }}" x-data="{ statusTujuan: @js(array_key_first($opsiStatus)) }" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <label class="block text-xs font-semibold text-slate-500">
                            Perubahan status
                            <select name="status" x-model="statusTujuan" class="mt-1.5 w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                                @foreach ($opsiStatus as $nilai => $label)
                                    <option value="{{ $nilai }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label x-show="statusTujuan === 'ditolak' || statusTujuan === 'revisi'" class="block text-xs font-semibold text-slate-500">
                            Alasan untuk warga
                            <textarea name="catatan" rows="4" x-bind:required="statusTujuan === 'ditolak' || statusTujuan === 'revisi'" class="mt-1.5 w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary" placeholder="Jelaskan alasan atau dokumen yang perlu diperbaiki">{{ old('catatan') }}</textarea>
                        </label>
                        <button class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white hover:bg-primary-700">Simpan Perubahan</button>
                    </form>
                @else
                    <p class="rounded-2xl bg-soft p-4 text-sm text-slate-500">Tidak ada tindakan yang tersedia untuk peran dan status ini.</p>
                @endif

                @if ($pengajuan->status === \App\Enums\StatusPengajuan::Selesai && $pengajuan->suratTerbit)
                    <div class="space-y-2">
                        <a target="_blank" rel="noopener" href="{{ route('admin.pengajuan.pdf', $pengajuan) }}" class="block rounded-xl bg-primary px-4 py-3 text-center text-sm font-bold text-white">Lihat PDF</a>
                        <a href="{{ route('admin.pengajuan.pdf', ['pengajuan' => $pengajuan, 'unduh' => 1]) }}" class="block rounded-xl bg-slate-900 px-4 py-3 text-center text-sm font-bold text-white">Unduh PDF</a>
                        @if ($peran->hasRole('admin'))
                            <form method="POST" action="{{ route('admin.pengajuan.pdf.cetak-ulang', $pengajuan) }}">
                                @csrf
                                <button class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Cetak Ulang</button>
                            </form>
                        @endif
                    </div>
                @endif

                <dl class="space-y-3 border-t border-slate-100 pt-4 text-xs">
                    <div class="flex justify-between gap-3"><dt class="text-slate-400">Diverifikasi oleh</dt><dd class="text-right font-semibold text-slate-700">{{ $pengajuan->diverifikasiOleh?->name ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-400">Disetujui oleh</dt><dd class="text-right font-semibold text-slate-700">{{ $pengajuan->disetujuiOleh?->name ?? '—' }}</dd></div>
                </dl>
            </aside>
        </div>
    </div>
</x-app-layout>
