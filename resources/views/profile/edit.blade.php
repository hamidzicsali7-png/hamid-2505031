<x-app-layout>
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">Profil Saya</h2>
            <p class="mt-1 text-sm text-slate-500">Perbarui informasi akun dan kata sandi Anda.</p>
        </div>

        <x-card padding="p-5 sm:p-7">
            <h3 class="font-bold text-slate-900">Informasi Akun</h3>
            <form method="POST" action="{{ route('profile.update') }}" class="mt-5 space-y-4">
                @csrf
                @method('PATCH')
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama</label>
                        <input id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="255" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                        @error('name')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="nik" class="mb-1.5 block text-sm font-semibold text-slate-700">NIK</label>
                        <input id="nik" name="nik" inputmode="numeric" pattern="[0-9]{16}" maxlength="16" value="{{ old('nik', $user->nik) }}" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                        @error('nik')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="255" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                        @error('email')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="no_hp" class="mb-1.5 block text-sm font-semibold text-slate-700">Nomor HP</label>
                        <input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp', $user->no_hp) }}" maxlength="20" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                        @error('no_hp')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white transition hover:bg-primary-800">Simpan Profil</button>
                </div>
            </form>
        </x-card>

        <x-card padding="p-5 sm:p-7">
            <h3 class="font-bold text-slate-900">Ubah Kata Sandi</h3>
            <form method="POST" action="{{ route('password.update') }}" class="mt-5 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="current_password" class="mb-1.5 block text-sm font-semibold text-slate-700">Kata sandi saat ini</label>
                    <input id="current_password" name="current_password" type="password" autocomplete="current-password" required class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                    @error('current_password', 'updatePassword')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Kata sandi baru</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                        @error('password', 'updatePassword')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-slate-700">Konfirmasi kata sandi baru</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white transition hover:bg-primary-800">Ubah Kata Sandi</button>
                </div>
            </form>
        </x-card>

        <x-card class="border border-red-100" padding="p-5 sm:p-7">
            <h3 class="font-bold text-slate-900">Hapus Akun</h3>
            <p class="mt-1 text-sm text-slate-500">Penghapusan akun memerlukan konfirmasi kata sandi dan tidak dapat dibatalkan.</p>
            <form method="POST" action="{{ route('profile.destroy') }}" class="mt-4 flex flex-col gap-3 sm:flex-row">
                @csrf
                @method('DELETE')
                <input name="password" type="password" autocomplete="current-password" required placeholder="Kata sandi saat ini" class="min-w-0 flex-1 rounded-xl border-slate-200 text-sm focus:border-danger focus:ring-danger">
                <button type="submit" class="rounded-xl bg-danger px-5 py-2.5 text-sm font-bold text-white transition hover:bg-red-700">Hapus Akun</button>
                @error('password', 'userDeletion')<p class="text-xs text-danger sm:basis-full">{{ $message }}</p>@enderror
            </form>
        </x-card>
    </div>
</x-app-layout>
