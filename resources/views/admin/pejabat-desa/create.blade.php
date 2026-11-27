<x-app-layout>
    <div class="mx-auto max-w-3xl space-y-5">
        <div><a href="{{ route('admin.pejabat-desa.index') }}" class="text-sm font-semibold text-primary hover:underline">← Kembali ke pejabat</a><h2 class="mt-3 text-2xl font-extrabold text-slate-900">Tambah Pejabat Desa</h2></div>
        @include('admin.pejabat-desa._form')
    </div>
</x-app-layout>
