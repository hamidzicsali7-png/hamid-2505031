<x-app-layout>
    <div class="mx-auto max-w-5xl">
        <p class="text-sm text-slate-500">Portal warga</p>
        <h1 class="mt-1 text-3xl font-bold">Selamat datang, {{ auth()->user()->name }}</h1>
        <p class="mt-3 text-slate-600">Ajukan surat keterangan dan pantau prosesnya di sini.</p>

        <a href="{{ route('warga.pengajuan.index') }}"
            class="mt-7 inline-flex rounded-2xl bg-indigo-600 px-5 py-3 font-semibold text-white">
            Lihat Pengajuan Saya
        </a>
    </div>
</x-app-layout>
