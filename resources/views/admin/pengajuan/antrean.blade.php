<x-app-layout>
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Antrean Persetujuan</h2>
            <p class="mt-1 text-sm text-slate-500">Pengajuan yang sudah diverifikasi dan menunggu keputusan Kepala Desa.</p>
        </div>

        <div class="space-y-4">
            @forelse ($pengajuan as $item)
                <article x-data="{ terbuka: false, statusTujuan: '', namaTindakan: '', perluAlasan: false }" class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-900/5 sm:p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $item->kode_pengajuan }}</p>
                            <h3 class="mt-1 text-lg font-bold text-slate-900">{{ $item->jenisSurat->nama }}</h3>
                            <p class="mt-1 text-sm text-slate-600">{{ $item->penduduk->nama }} · NIK {{ $item->penduduk->nik }}</p>
                            <p class="mt-2 text-xs text-slate-400">{{ $item->tgl_pengajuan?->locale('id')->translatedFormat('d F Y') }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('admin.pengajuan.show', $item) }}" class="rounded-xl bg-soft px-3 py-2 text-xs font-bold text-slate-600">Detail</a>
                            <button type="button" @click="terbuka = true; statusTujuan = 'disetujui'; namaTindakan = 'Setujui pengajuan'; perluAlasan = false" class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-700">Setujui</button>
                            <button type="button" @click="terbuka = true; statusTujuan = 'ditolak'; namaTindakan = 'Tolak pengajuan'; perluAlasan = true" class="rounded-xl bg-red-600 px-3 py-2 text-xs font-bold text-white hover:bg-red-700">Tolak</button>
                            <button type="button" @click="terbuka = true; statusTujuan = 'revisi'; namaTindakan = 'Minta revisi'; perluAlasan = true" class="rounded-xl bg-amber-500 px-3 py-2 text-xs font-bold text-white hover:bg-amber-600">Revisi</button>
                        </div>
                    </div>

                    <p class="mt-4 line-clamp-2 text-sm text-slate-500">{{ $item->keperluan }}</p>

                    <div x-cloak x-show="terbuka" x-transition class="fixed inset-0 z-[60] grid place-items-center bg-slate-950/50 p-4" style="display: none;" @keydown.escape.window="terbuka = false">
                        <div @click.outside="terbuka = false" class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-lg font-extrabold text-slate-900" x-text="namaTindakan"></h3>
                                    <p class="mt-1 text-sm text-slate-500">{{ $item->kode_pengajuan }} · {{ $item->penduduk->nama }}</p>
                                </div>
                                <button type="button" @click="terbuka = false" class="text-2xl leading-none text-slate-400" aria-label="Tutup">×</button>
                            </div>
                            <form method="POST" action="{{ route('admin.pengajuan.status', $item) }}" class="mt-5 space-y-4">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" :value="statusTujuan">
                                <label x-show="perluAlasan" class="block text-sm font-semibold text-slate-700">
                                    Alasan untuk warga
                                    <textarea name="catatan" rows="4" x-bind:required="perluAlasan" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary" placeholder="Jelaskan alasan penolakan atau hal yang perlu direvisi"></textarea>
                                </label>
                                <p x-show="!perluAlasan" class="rounded-xl bg-soft p-3 text-sm text-slate-600">Pengajuan akan disetujui dan warga akan menerima notifikasi.</p>
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="terbuka = false" class="rounded-xl bg-soft px-4 py-2.5 text-sm font-bold text-slate-600">Batal</button>
                                    <button class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white">Konfirmasi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-3xl bg-white px-6 py-14 text-center shadow-sm ring-1 ring-slate-900/5">
                    <p class="text-lg font-bold text-slate-800">Antrean persetujuan kosong</p>
                    <p class="mt-1 text-sm text-slate-500">Belum ada pengajuan yang menunggu tanda tangan Kepala Desa.</p>
                </div>
            @endforelse
        </div>

        @if ($pengajuan->hasPages())
            <div>{{ $pengajuan->links() }}</div>
        @endif
    </div>
</x-app-layout>
