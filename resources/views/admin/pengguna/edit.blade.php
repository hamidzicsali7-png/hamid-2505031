<x-app-layout>
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">Kelola Akun Petugas</h2>
            <p class="mt-1 text-sm text-slate-500">Perbarui data dan peran untuk {{ $pengguna->name }}.</p>
        </div>
        <x-card padding="p-5 sm:p-7">
            <form method="POST" action="{{ route('admin.pengguna.update', $pengguna) }}">
                @csrf
                @method('PUT')
                @include('admin.pengguna._form')
            </form>
        </x-card>
    </div>
</x-app-layout>
