<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Selamat datang kembali</h1>
        <p class="mt-1 text-sm text-slate-500">Masuk untuk melanjutkan layanan desa.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <x-input name="email" label="Email" type="email" required autofocus autocomplete="username" />
        <x-input name="password" label="Kata Sandi" type="password" required autocomplete="current-password" class="mt-4" />

        <div class="mt-4 flex items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-500">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-primary shadow-sm focus:ring-primary/30" name="remember">
                <span>Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-primary hover:underline" href="{{ route('password.request') }}">
                    Lupa kata sandi?
                </a>
            @endif
        </div>

        <x-button type="submit" class="mt-6 w-full">
                Masuk
        </x-button>

        <p class="mt-5 text-center text-sm text-slate-500">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-bold text-primary hover:underline">Daftar sekarang</a>
        </p>
    </form>
</x-guest-layout>
