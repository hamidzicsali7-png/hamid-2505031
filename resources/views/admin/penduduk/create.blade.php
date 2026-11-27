<x-app-layout>
    <div class="mx-auto max-w-4xl space-y-5">
        <div>
            <a href="{{ route('admin.penduduk.index') }}" class="text-sm font-semibold text-primary hover:underline">← Kembali ke penduduk</a>
            <h2 class="mt-3 text-2xl font-extrabold text-slate-900">Tambah Penduduk</h2>
        </div>
        @include('admin.penduduk._form')
    </div>
</x-app-layout>
