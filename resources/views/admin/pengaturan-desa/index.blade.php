<x-app-layout>
    <div class="mx-auto max-w-4xl space-y-5">
        <div><h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Pengaturan Desa</h2><p class="mt-1 text-sm text-slate-500">Profil desa digunakan sebagai identitas pada layanan dan surat resmi.</p></div>

        @if (auth()->user()->hasRole('admin'))
            <form method="POST" action="{{ route('admin.pengaturan-desa.update') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')
                <x-card padding="p-5 sm:p-7">
                    <div class="grid gap-5 md:grid-cols-2">
                        @foreach ([
                            ['nama_desa', 'Nama Desa'],
                            ['kecamatan', 'Kecamatan'],
                            ['kabupaten', 'Kabupaten'],
                            ['provinsi', 'Provinsi'],
                            ['kode_pos', 'Kode Pos'],
                            ['telepon', 'Telepon'],
                            ['email', 'Email'],
                            ['nama_kades', 'Nama Kepala Desa'],
                        ] as [$field, $label])
                            <div><label for="{{ $field }}" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ $label }}</label><input id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $pengaturan->{$field}) }}" required type="{{ $field === 'email' ? 'email' : 'text' }}" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">@error($field)<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror</div>
                        @endforeach
                        <div class="md:col-span-2"><label for="alamat" class="mb-1.5 block text-sm font-semibold text-slate-700">Alamat Desa</label><textarea id="alamat" name="alamat" rows="3" required class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">{{ old('alamat', $pengaturan->alamat) }}</textarea>@error('alamat')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror</div>
                        <div class="md:col-span-2"><label for="format_nomor_surat" class="mb-1.5 block text-sm font-semibold text-slate-700">Format Nomor Surat</label><input id="format_nomor_surat" name="format_nomor_surat" value="{{ old('format_nomor_surat', $pengaturan->format_nomor_surat) }}" required placeholder="470/{urutan}/DS/{bulan_romawi}/{tahun}" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary"><p class="mt-1 text-xs text-slate-500">Placeholder: {urutan}, {bulan_romawi}, {tahun}; {nomor} dan {kode} juga didukung.</p>@error('format_nomor_surat')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror</div>
                        <div class="md:col-span-2">
                            <label for="logo" class="mb-1.5 block text-sm font-semibold text-slate-700">Logo Desa</label>
                            @if ($pengaturan->logo_path)<img src="{{ Storage::disk('public')->url($pengaturan->logo_path) }}" alt="Logo {{ $pengaturan->nama_desa }}" class="mb-3 h-20 w-20 rounded-xl border border-slate-100 bg-white object-contain p-2">@endif
                            <input id="logo" name="logo" type="file" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="block w-full rounded-xl border border-slate-200 text-sm file:mr-4 file:border-0 file:bg-primary-50 file:px-4 file:py-2.5 file:font-semibold file:text-primary"><p class="mt-1 text-xs text-slate-400">Format JPG atau PNG, maksimal 2 MB. Kosongkan jika tidak ingin mengganti.</p>@error('logo')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </x-card>
                <div class="flex justify-end"><x-button type="submit">Simpan Pengaturan</x-button></div>
            </form>
        @else
            <x-card padding="p-5 sm:p-7">
                <div class="flex flex-wrap items-start gap-5">
                    @if ($pengaturan->logo_path)<img src="{{ Storage::disk('public')->url($pengaturan->logo_path) }}" alt="Logo {{ $pengaturan->nama_desa }}" class="h-20 w-20 rounded-xl border border-slate-100 bg-white object-contain p-2">@endif
                    <div><h3 class="text-xl font-extrabold text-slate-900">{{ $pengaturan->nama_desa ?: 'Profil desa belum diatur' }}</h3><p class="mt-1 text-sm text-slate-500">{{ $pengaturan->kecamatan }}, {{ $pengaturan->kabupaten }}, {{ $pengaturan->provinsi }}</p></div>
                </div>
                <dl class="mt-6 grid gap-5 border-t border-slate-100 pt-5 sm:grid-cols-2">
                    <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Alamat</dt><dd class="mt-1 text-sm font-semibold text-slate-800">{{ $pengaturan->alamat ?: '—' }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Kode Pos</dt><dd class="mt-1 font-semibold">{{ $pengaturan->kode_pos ?: '—' }}</dd></div><div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Telepon</dt><dd class="mt-1 font-semibold">{{ $pengaturan->telepon ?: '—' }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Email</dt><dd class="mt-1 font-semibold">{{ $pengaturan->email ?: '—' }}</dd></div><div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Kepala Desa</dt><dd class="mt-1 font-semibold">{{ $pengaturan->nama_kades ?: '—' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Format Nomor Surat</dt><dd class="mt-1 font-mono text-sm font-semibold">{{ $pengaturan->format_nomor_surat ?: '—' }}</dd></div>
                </dl>
            </x-card>
        @endif
    </div>
</x-app-layout>
