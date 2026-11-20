<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Atur ulang kata sandi</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">
            Masukkan alamat email Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <x-input name="email" label="Email" type="email" required autofocus autocomplete="email" />

        <x-button type="submit" class="mt-5 w-full">Kirim Tautan Atur Ulang Kata Sandi</x-button>
    </form>
</x-guest-layout>
