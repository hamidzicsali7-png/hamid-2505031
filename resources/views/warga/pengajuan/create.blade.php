<x-app-layout>
    <div class="mx-auto max-w-4xl space-y-5">
        <div>
            <a href="{{ route('warga.pengajuan.index') }}" class="text-sm font-semibold text-primary hover:underline">← Kembali ke pengajuan</a>
            <h2 class="mt-3 text-2xl font-extrabold tracking-tight text-slate-900">
                {{ $pengajuan ? 'Perbaiki Pengajuan' : 'Ajukan Surat' }}
            </h2>
            <p class="mt-1 text-sm text-slate-500">Pilih jenis surat dan lengkapi data yang diperlukan.</p>
        </div>

        @if (! $profilCocok)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900">
                <p class="font-bold">Data penduduk belum terhubung dengan akun Anda.</p>
                <p class="mt-1">Pengajuan surat belum dapat dibuat. Silakan hubungi kantor desa untuk mencocokkan NIK Anda dengan data penduduk.</p>
                @if ($teleponDesa)<p class="mt-2 font-semibold">Telepon kantor desa: {{ $teleponDesa }}</p>@endif
            </div>
        @elseif (! $terpilih)
            <div>
                <h3 class="mb-3 font-bold text-slate-800">Pilih jenis surat</h3>
                @if ($jenisSurat->isEmpty())
                    <x-card><x-empty-state title="Belum ada layanan surat aktif" description="Silakan hubungi kantor desa untuk informasi layanan surat." /></x-card>
                @else
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach ($jenisSurat as $jenis)
                            <a href="{{ route('warga.pengajuan.create', ['jenis_surat_id' => $jenis->id]) }}" class="group rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-900/5 transition hover:-translate-y-0.5 hover:shadow-md sm:p-6">
                                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-primary-50 text-primary">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 3h7l5 5v11a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 0v5h5M10 13h6m-6 4h6"/></svg>
                                </span>
                                <h3 class="mt-4 font-bold text-slate-900 group-hover:text-primary">{{ $jenis->nama }}</h3>
                                <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $jenis->deskripsi ?: 'Layanan surat keterangan desa.' }}</p>
                                <p class="mt-4 text-xs font-semibold text-primary">{{ $jenis->syaratSurat->count() }} syarat dokumen · Pilih layanan →</p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            @php($dataTambahanLama = old('data_tambahan', $pengajuan?->data_tambahan ?? []))
            <form method="POST" action="{{ $pengajuan ? route('warga.pengajuan.update', $pengajuan) : route('warga.pengajuan.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @if ($pengajuan) @method('PATCH') @endif
                <input type="hidden" name="jenis_surat_id" value="{{ $terpilih->id }}">

                @if ($errors->any())
                    <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-danger">
                        <p class="font-bold">Periksa kembali data pengajuan:</p>
                        <ul class="mt-2 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <x-card padding="p-5 sm:p-7">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div><p class="text-xs font-bold uppercase tracking-wide text-slate-400">Jenis Surat</p><h3 class="mt-1 text-lg font-extrabold text-slate-900">{{ $terpilih->nama }}</h3><p class="mt-1 text-sm text-slate-500">{{ $terpilih->deskripsi }}</p></div>
                        @if ($pengajuan)<x-badge-status :status="$pengajuan->status" />@endif
                    </div>
                </x-card>

                <x-card padding="p-5 sm:p-7">
                    <h3 class="font-bold text-slate-900">Data Pemohon</h3>
                    <p class="mt-1 text-xs text-slate-500">Data ini diambil dari profil penduduk dan tidak dapat diubah pada formulir pengajuan.</p>
                    <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Nama</dt><dd class="mt-1 font-semibold text-slate-800">{{ $penduduk->nama }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">NIK</dt><dd class="mt-1 font-semibold text-slate-800">{{ $penduduk->nik }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Tempat, tanggal lahir</dt><dd class="mt-1 font-semibold text-slate-800">{{ $penduduk->tempat_lahir }}, {{ $penduduk->tgl_lahir->locale('id')->translatedFormat('d F Y') }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Alamat</dt><dd class="mt-1 font-semibold text-slate-800">{{ $penduduk->alamat }}, RT {{ $penduduk->rt }} / RW {{ $penduduk->rw }}{{ $penduduk->dusun ? ', '.$penduduk->dusun : '' }}</dd></div>
                    </dl>
                </x-card>

                <x-card padding="p-5 sm:p-7">
                    <h3 class="font-bold text-slate-900">Informasi Pengajuan</h3>
                    <div class="mt-4">
                        <label for="keperluan" class="mb-1.5 block text-sm font-semibold text-slate-700">Keperluan</label>
                        <textarea id="keperluan" name="keperluan" rows="3" required maxlength="255" placeholder="Jelaskan tujuan penggunaan surat" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">{{ old('keperluan', $pengajuan?->keperluan) }}</textarea>
                        @error('keperluan')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>

                    @if (count($terpilih->field_tambahan ?? []))
                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            @foreach ($terpilih->field_tambahan as $field)
                                <div class="{{ $field['type'] === 'textarea' ? 'sm:col-span-2' : '' }}">
                                    <label for="data_tambahan_{{ $field['name'] }}" class="mb-1.5 block text-sm font-semibold text-slate-700">
                                        {{ $field['label'] }} @if ($field['required'] ?? false)<span class="text-danger">*</span>@endif
                                    </label>
                                    @if ($field['type'] === 'textarea')
                                        <textarea id="data_tambahan_{{ $field['name'] }}" name="data_tambahan[{{ $field['name'] }}]" rows="3" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">{{ $dataTambahanLama[$field['name']] ?? '' }}</textarea>
                                    @else
                                        <input id="data_tambahan_{{ $field['name'] }}" name="data_tambahan[{{ $field['name'] }}]" type="{{ $field['type'] }}" value="{{ $dataTambahanLama[$field['name']] ?? '' }}" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                                    @endif
                                    @error('data_tambahan.'.$field['name'])<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-card>

                <x-card padding="p-5 sm:p-7">
                    <h3 class="font-bold text-slate-900">Dokumen Persyaratan</h3>
                    <p class="mt-1 text-xs text-slate-500">Format PDF, JPG, JPEG, atau PNG. Maksimal 2 MB per dokumen. Dokumen wajib harus tersedia saat pengajuan dikirim.</p>
                    <div class="mt-5 space-y-4">
                        @forelse ($terpilih->syaratSurat as $syarat)
                            @php($fileTersimpan = $dokumenTerpasang->get($syarat->nama_dokumen))
                            <div class="rounded-2xl border border-slate-100 p-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <label for="dokumen_{{ $syarat->id }}" class="text-sm font-semibold text-slate-800">{{ $syarat->nama_dokumen }} @if ($syarat->wajib)<span class="text-danger">*</span>@else<span class="text-xs font-normal text-slate-400">(opsional)</span>@endif</label>
                                    @if ($fileTersimpan)
                                        <a href="{{ route('warga.pengajuan.dokumen', [$pengajuan, $fileTersimpan]) }}" class="text-xs font-bold text-primary hover:underline">Lihat dokumen tersimpan</a>
                                    @endif
                                </div>
                                <input id="dokumen_{{ $syarat->id }}" type="file" name="dokumen[{{ $syarat->id }}]" accept=".pdf,.jpg,.jpeg,.png" class="mt-2 block w-full rounded-xl border border-slate-200 text-sm file:mr-4 file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:font-semibold file:text-primary">
                                @error('dokumen.'.$syarat->id)<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                                @if ($fileTersimpan)<p class="mt-1 text-xs text-slate-400">Unggah berkas baru hanya jika ingin mengganti dokumen ini.</p>@endif
                            </div>
                        @empty
                            <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-500">Tidak ada dokumen persyaratan untuk jenis surat ini.</p>
                        @endforelse
                    </div>
                </x-card>

                <div class="flex flex-wrap justify-end gap-3">
                    <a href="{{ route('warga.pengajuan.index') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700">Batal</a>
                    <button type="submit" name="aksi" value="draft" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Simpan sebagai Draft</button>
                    <x-button type="submit" name="aksi" value="ajukan">{{ $pengajuan?->status === \App\Enums\StatusPengajuan::Revisi ? 'Perbaiki dan Ajukan Ulang' : 'Ajukan Surat' }}</x-button>
                </div>
            </form>
        @endif
    </div>
</x-app-layout>
