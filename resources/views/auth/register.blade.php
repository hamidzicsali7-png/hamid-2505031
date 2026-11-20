<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Buat akun warga</h1>
        <p class="mt-1 text-sm text-slate-500">Lengkapi data diri untuk menggunakan layanan desa.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="space-y-4">
            <x-input name="nama" label="Nama Lengkap" required autofocus autocomplete="name" />
            <x-input name="nik" label="NIK (16 digit)" required inputmode="numeric" pattern="[0-9]{16}" maxlength="16" autocomplete="off" />
            <x-input name="email" label="Email" type="email" required autocomplete="username" />
            <x-input name="no_hp" label="Nomor HP" type="tel" required autocomplete="tel" />
            <x-input name="password" label="Kata Sandi" type="password" required autocomplete="new-password" />
            <x-input name="password_confirmation" label="Konfirmasi Kata Sandi" type="password" required autocomplete="new-password" />
        </div>

        <x-button type="submit" class="mt-6 w-full">Daftar</x-button>
        <p class="mt-5 text-center text-sm text-slate-500">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-bold text-primary hover:underline">Masuk</a>
        </p>
    </form>
</x-guest-layout>
