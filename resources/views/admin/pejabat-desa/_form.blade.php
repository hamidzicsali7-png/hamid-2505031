@php($isEdit = $pejabatDesa->exists)
<form method="POST" action="{{ $isEdit ? route('admin.pejabat-desa.update', $pejabatDesa) : route('admin.pejabat-desa.store') }}" enctype="multipart/form-data" class="space-y-5">
    @csrf
    @if ($isEdit) @method('PUT') @endif
    <x-card padding="p-5 sm:p-7">
        <div class="grid gap-5 md:grid-cols-2">
            <div><label for="nama" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama</label><input id="nama" name="nama" value="{{ old('nama', $pejabatDesa->nama) }}" required class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">@error('nama')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror</div>
            <div><label for="jabatan" class="mb-1.5 block text-sm font-semibold text-slate-700">Jabatan</label><input id="jabatan" name="jabatan" value="{{ old('jabatan', $pejabatDesa->jabatan) }}" required class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">@error('jabatan')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror</div>
            <div><label for="nip" class="mb-1.5 block text-sm font-semibold text-slate-700">NIP <span class="font-normal text-slate-400">(opsional)</span></label><input id="nip" name="nip" value="{{ old('nip', $pejabatDesa->nip) }}" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">@error('nip')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror</div>
            <div><label for="aktif" class="mb-1.5 block text-sm font-semibold text-slate-700">Status</label><select id="aktif" name="aktif" required class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary"><option value="1" @selected((string) old('aktif', (int) ($pejabatDesa->aktif ?? true)) === '1')>Aktif</option><option value="0" @selected((string) old('aktif', (int) ($pejabatDesa->aktif ?? true)) === '0')>Nonaktif</option></select>@error('aktif')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror</div>
            <div class="md:col-span-2">
                <label for="tanda_tangan" class="mb-1.5 block text-sm font-semibold text-slate-700">Gambar Tanda Tangan</label>
                @if ($pejabatDesa->tanda_tangan_path)<img src="{{ Storage::disk('public')->url($pejabatDesa->tanda_tangan_path) }}" alt="Tanda tangan {{ $pejabatDesa->nama }}" class="mb-3 h-24 rounded-xl border border-slate-100 bg-white object-contain p-2">@endif
                <input id="tanda_tangan" name="tanda_tangan" type="file" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="block w-full rounded-xl border border-slate-200 text-sm file:mr-4 file:border-0 file:bg-primary-50 file:px-4 file:py-2.5 file:font-semibold file:text-primary">
                <p class="mt-1 text-xs text-slate-400">Format JPG atau PNG, maksimal 2 MB. Kosongkan jika tidak ingin mengganti.</p>
                @error('tanda_tangan')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
            </div>
        </div>
    </x-card>
    <div class="flex justify-end gap-3"><a href="{{ $isEdit ? route('admin.pejabat-desa.show', $pejabatDesa) : route('admin.pejabat-desa.index') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700">Batal</a><x-button type="submit">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Pejabat' }}</x-button></div>
</form>
