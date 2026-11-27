<x-app-layout>
    <div class="mx-auto max-w-4xl space-y-5">
        <div>
            <a href="{{ route('admin.penduduk.show', $penduduk) }}" class="text-sm font-semibold text-primary hover:underline">← Kembali ke detail</a>
            <h2 class="mt-3 text-2xl font-extrabold text-slate-900">Ubah Data Penduduk</h2>
        </div>
        @include('admin.penduduk._form')
    </div>
</x-app-layout>
