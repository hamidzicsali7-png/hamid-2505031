@php($isEdit = $jenisSurat->exists)
<form method="POST" action="{{ $isEdit ? route('admin.jenis-surat.update', $jenisSurat) : route('admin.jenis-surat.store') }}" class="space-y-5">
    @csrf
    @if ($isEdit) @method('PUT') @endif
    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-danger">
            <p class="font-bold">Periksa kembali data berikut:</p>
            <ul class="mt-2 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    <x-card padding="p-5 sm:p-7">
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="nama" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Jenis Surat</label>
                <input id="nama" name="nama" value="{{ old('nama', $jenisSurat->nama) }}" required class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                @error('nama')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="kode" class="mb-1.5 block text-sm font-semibold text-slate-700">Kode</label>
                <input id="kode" name="kode" value="{{ old('kode', $jenisSurat->kode) }}" required maxlength="30" class="w-full rounded-xl border-slate-200 text-sm uppercase focus:border-primary focus:ring-primary">
                @error('kode')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="aktif" class="mb-1.5 block text-sm font-semibold text-slate-700">Status</label>
                <select id="aktif" name="aktif" required class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                    <option value="1" @selected((string) old('aktif', (int) ($jenisSurat->aktif ?? true)) === '1')>Aktif</option>
                    <option value="0" @selected((string) old('aktif', (int) ($jenisSurat->aktif ?? true)) === '0')>Nonaktif</option>
                </select>
                @error('aktif')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label for="deskripsi" class="mb-1.5 block text-sm font-semibold text-slate-700">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="3" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">{{ old('deskripsi', $jenisSurat->deskripsi) }}</textarea>
            </div>
        </div>
    </x-card>

    <x-card padding="p-5 sm:p-7">
        <div x-data="{ syarat: @js(old('syarat', $syarat ?? [])) }">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div><h3 class="font-bold text-slate-900">Syarat Dokumen</h3><p class="mt-1 text-xs text-slate-500">Setiap dokumen yang ditambahkan wajib dilampirkan.</p></div>
                <button type="button" @click="syarat.push('')" class="rounded-xl border border-primary/20 px-3 py-2 text-sm font-bold text-primary">Tambah Baris</button>
            </div>
            <template x-for="(nama, index) in syarat" :key="index">
                <div class="mb-3 flex gap-2">
                    <input type="text" x-model="syarat[index]" :name="'syarat[' + index + ']'" required maxlength="255" placeholder="Contoh: Fotokopi KTP" class="min-w-0 flex-1 rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                    <button type="button" @click="syarat.splice(index, 1)" class="rounded-xl px-3 text-sm font-semibold text-danger hover:bg-red-50">Hapus</button>
                </div>
            </template>
            <p x-show="syarat.length === 0" class="rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-500">Belum ada syarat dokumen.</p>
            @error('syarat.*')<p class="mt-2 text-xs text-danger">{{ $message }}</p>@enderror
        </div>
    </x-card>

    <x-card padding="p-5 sm:p-7">
        <div x-data="{ fields: @js(old('field_tambahan', $jenisSurat->field_tambahan ?? [])) }">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div><h3 class="font-bold text-slate-900">Field Tambahan</h3><p class="mt-1 text-xs text-slate-500">Kolom yang ditampilkan saat warga mengajukan surat.</p></div>
                <button type="button" @click="fields.push({name:'',label:'',type:'text',required:false})" class="rounded-xl border border-primary/20 px-3 py-2 text-sm font-bold text-primary">Tambah Field</button>
            </div>
            <template x-for="(field, index) in fields" :key="index">
                <div class="mb-4 rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Nama field (huruf kecil)</label>
                            <input x-model="field.name" :name="'field_tambahan[' + index + '][name]'" required pattern="[a-z][a-z0-9_]*" placeholder="contoh: nama_usaha" class="w-full rounded-xl border-slate-200 text-sm">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Label</label>
                            <input x-model="field.label" :name="'field_tambahan[' + index + '][label]'" required placeholder="Nama Usaha" class="w-full rounded-xl border-slate-200 text-sm">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Jenis input</label>
                            <select x-model="field.type" :name="'field_tambahan[' + index + '][type]'" class="w-full rounded-xl border-slate-200 text-sm">
                                <option value="text">Teks</option><option value="textarea">Teks panjang</option><option value="number">Angka</option><option value="date">Tanggal</option><option value="email">Email</option>
                            </select>
                        </div>
                        <label class="flex items-center gap-2 pt-5 text-sm font-medium text-slate-700">
                            <input type="checkbox" x-model="field.required" :name="'field_tambahan[' + index + '][required]'" value="1" class="rounded border-slate-300 text-primary focus:ring-primary">
                            Wajib diisi
                        </label>
                    </div>
                    <button type="button" @click="fields.splice(index, 1)" class="mt-3 text-xs font-bold text-danger">Hapus field</button>
                </div>
            </template>
            <p x-show="fields.length === 0" class="rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-500">Tidak ada field tambahan.</p>
        </div>
    </x-card>
    <div class="flex justify-end gap-3">
        <a href="{{ $isEdit ? route('admin.jenis-surat.show', $jenisSurat) : route('admin.jenis-surat.index') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700">Batal</a>
        <x-button type="submit">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Jenis Surat' }}</x-button>
    </div>
</form>
