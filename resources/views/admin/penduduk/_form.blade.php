@php($isEdit = $penduduk->exists)
<form method="POST" action="{{ $isEdit ? route('admin.penduduk.update', $penduduk) : route('admin.penduduk.store') }}" class="space-y-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif
    <x-card padding="p-5 sm:p-7">
        <div class="grid gap-5 md:grid-cols-2">
            @foreach ([
                ['nik', 'NIK', 'text', '16 digit angka'],
                ['no_kk', 'Nomor Kartu Keluarga', 'text', '16 digit angka'],
                ['nama', 'Nama Lengkap', 'text', 'Nama sesuai identitas'],
                ['tempat_lahir', 'Tempat Lahir', 'text', ''],
                ['tgl_lahir', 'Tanggal Lahir', 'date', ''],
                ['agama', 'Agama', 'text', ''],
                ['pekerjaan', 'Pekerjaan', 'text', ''],
                ['status_perkawinan', 'Status Perkawinan', 'text', 'Contoh: Kawin'],
                ['kewarganegaraan', 'Kewarganegaraan', 'text', 'WNI'],
                ['rt', 'RT', 'text', 'Contoh: 001'],
                ['rw', 'RW', 'text', 'Contoh: 002'],
                ['dusun', 'Dusun', 'text', 'Opsional'],
            ] as [$field, $label, $type, $placeholder])
                <div>
                    <label for="{{ $field }}" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ $label }}</label>
                    <input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" value="{{ old($field, $penduduk->{$field} instanceof \Illuminate\Support\Carbon ? $penduduk->{$field}->format('Y-m-d') : $penduduk->{$field}) }}" placeholder="{{ $placeholder }}" @required(! in_array($field, ['dusun'])) class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                    @error($field)<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                </div>
            @endforeach
            <div>
                <label for="jenis_kelamin" class="mb-1.5 block text-sm font-semibold text-slate-700">Jenis Kelamin</label>
                <select id="jenis_kelamin" name="jenis_kelamin" required class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                    <option value="">Pilih jenis kelamin</option>
                    <option value="L" @selected(old('jenis_kelamin', $penduduk->jenis_kelamin) === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('jenis_kelamin', $penduduk->jenis_kelamin) === 'P')>Perempuan</option>
                </select>
                @error('jenis_kelamin')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="user_id" class="mb-1.5 block text-sm font-semibold text-slate-700">Akun Warga</label>
                <select id="user_id" name="user_id" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                    <option value="">Tidak ditautkan</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected((string) old('user_id', $penduduk->user_id) === (string) $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('user_id')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label for="alamat" class="mb-1.5 block text-sm font-semibold text-slate-700">Alamat</label>
                <textarea id="alamat" name="alamat" rows="3" required class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">{{ old('alamat', $penduduk->alamat) }}</textarea>
                @error('alamat')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
            </div>
        </div>
    </x-card>
    <div class="flex justify-end gap-3">
        <a href="{{ $isEdit ? route('admin.penduduk.show', $penduduk) : route('admin.penduduk.index') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700">Batal</a>
        <x-button type="submit">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Penduduk' }}</x-button>
    </div>
</form>
