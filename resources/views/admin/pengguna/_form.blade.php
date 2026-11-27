@csrf
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label for="name" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama</label>
        <input id="name" name="name" value="{{ old('name', $pengguna->name) }}" required maxlength="255" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
        @error('name')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="nik" class="mb-1.5 block text-sm font-semibold text-slate-700">NIK</label>
        <input id="nik" name="nik" value="{{ old('nik', $pengguna->nik) }}" inputmode="numeric" pattern="[0-9]{16}" maxlength="16" required class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
        @error('nik')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $pengguna->email) }}" required maxlength="255" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
        @error('email')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="no_hp" class="mb-1.5 block text-sm font-semibold text-slate-700">Nomor HP</label>
        <input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp', $pengguna->no_hp) }}" maxlength="20" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
        @error('no_hp')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="role" class="mb-1.5 block text-sm font-semibold text-slate-700">Peran</label>
        <select id="role" name="role" required class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
            @foreach (\App\Http\Requests\Admin\PenggunaPetugasRequest::ROLES as $role)
                <option value="{{ $role }}" @selected(old('role', $pengguna->exists ? $pengguna->getRoleNames()->first() : '') === $role)>{{ ucfirst($role) }}</option>
            @endforeach
        </select>
        @error('role')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Kata sandi {{ $pengguna->exists ? '(opsional)' : '' }}</label>
        <input id="password" name="password" type="password" autocomplete="new-password" {{ $pengguna->exists ? '' : 'required' }} class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
        @error('password')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-slate-700">Konfirmasi kata sandi</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
    </div>
</div>
<div class="mt-6 flex justify-end gap-3">
    <a href="{{ route('admin.pengguna.index') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600">Batal</a>
    <button type="submit" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white transition hover:bg-primary-800">{{ $pengguna->exists ? 'Simpan Perubahan' : 'Buat Akun' }}</button>
</div>
