<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">Kelola Pengguna</h2>
                <p class="mt-1 text-sm text-slate-500">Kelola akun dan peran petugas desa.</p>
            </div>
            <a href="{{ route('admin.pengguna.create') }}" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white transition hover:bg-primary-800">Tambah Petugas</a>
        </div>

        <x-card padding="p-5">
            <form method="GET" action="{{ route('admin.pengguna.index') }}" class="flex flex-col gap-3 sm:flex-row">
                <input name="q" value="{{ request('q') }}" placeholder="Cari nama atau email petugas" class="min-w-0 flex-1 rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                <button type="submit" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white">Cari</button>
                <a href="{{ route('admin.pengguna.index') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-center text-sm font-semibold text-slate-600">Reset</a>
            </form>
        </x-card>

        <x-card padding="p-0">
            <x-table>
                <thead>
                    <tr class="bg-soft text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-5 py-4 font-bold">Nama</th>
                        <th class="px-5 py-4 font-bold">Email</th>
                        <th class="px-5 py-4 font-bold">Nomor HP</th>
                        <th class="px-5 py-4 font-bold">Peran</th>
                        <th class="px-5 py-4 text-right font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pengguna as $item)
                        <tr>
                            <td class="px-5 py-4 font-semibold text-slate-800">{{ $item->name }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $item->email }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $item->no_hp ?: '—' }}</td>
                            <td class="px-5 py-4">
                                @foreach ($item->roles as $role)
                                    <span class="rounded-full bg-primary-50 px-2.5 py-1 text-xs font-bold capitalize text-primary">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.pengguna.edit', $item) }}" class="text-sm font-bold text-primary hover:underline">Kelola</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-slate-500">Belum ada akun petugas.</td></tr>
                    @endforelse
                </tbody>
            </x-table>
            @if ($pengguna->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">{{ $pengguna->links() }}</div>
            @endif
        </x-card>
    </div>
</x-app-layout>
