<x-app-layout>
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">Tambah Akun Petugas</h2>
            <p class="mt-1 text-sm text-slate-500">Buat akun dengan peran operator, sekretaris, atau Kepala Desa.</p>
        </div>
        <x-card padding="p-5 sm:p-7">
            <form method="POST" action="{{ route('admin.pengguna.store') }}">
                @include('admin.pengguna._form')
            </form>
        </x-card>
    </div>
</x-app-layout>
