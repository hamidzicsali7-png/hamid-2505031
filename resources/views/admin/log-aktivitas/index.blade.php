<x-app-layout>
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">Log Aktivitas</h2>
            <p class="mt-1 text-sm text-slate-500">Riwayat perubahan dan tindakan pada sistem.</p>
        </div>

        <x-card padding="p-5 sm:p-6">
            <form method="GET" action="{{ route('admin.log-aktivitas.index') }}" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="sm:col-span-2">
                    <label for="q" class="mb-1.5 block text-sm font-semibold text-slate-700">Pencarian</label>
                    <input id="q" name="q" value="{{ request('q') }}" placeholder="Nama petugas, aksi, atau keterangan" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label for="aksi" class="mb-1.5 block text-sm font-semibold text-slate-700">Jenis aksi</label>
                    <select id="aksi" name="aksi" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                        <option value="">Semua aksi</option>
                        @foreach ($daftarAksi as $aksi)
                            <option value="{{ $aksi }}" @selected(request('aksi') === $aksi)>{{ $aksi }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tanggal_mulai" class="mb-1.5 block text-sm font-semibold text-slate-700">Dari tanggal</label>
                    <input id="tanggal_mulai" name="tanggal_mulai" type="date" value="{{ request('tanggal_mulai') }}" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                    @error('tanggal_mulai')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="tanggal_selesai" class="mb-1.5 block text-sm font-semibold text-slate-700">Sampai tanggal</label>
                    <input id="tanggal_selesai" name="tanggal_selesai" type="date" value="{{ request('tanggal_selesai') }}" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary">
                    @error('tanggal_selesai')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-end gap-2 sm:col-span-2 xl:col-span-3">
                    <button type="submit" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white">Terapkan filter</button>
                    <a href="{{ route('admin.log-aktivitas.index') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600">Reset</a>
                </div>
            </form>
        </x-card>

        <x-card padding="p-0">
            <x-table>
                <thead>
                    <tr class="bg-soft text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-5 py-4 font-bold">Waktu</th>
                        <th class="px-5 py-4 font-bold">Pengguna</th>
                        <th class="px-5 py-4 font-bold">Aksi</th>
                        <th class="px-5 py-4 font-bold">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logAktivitas as $log)
                        <tr class="align-top">
                            <td class="whitespace-nowrap px-5 py-4 text-xs text-slate-500">{{ $log->created_at->locale('id')->translatedFormat('d M Y H:i') }}</td>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-700">{{ $log->user?->name ?? 'Sistem' }}</td>
                            <td class="px-5 py-4"><span class="rounded-full bg-primary-50 px-2.5 py-1 text-xs font-bold text-primary">{{ $log->aksi }}</span></td>
                            <td class="px-5 py-4 text-sm leading-6 text-slate-600">{{ $log->keterangan }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-12 text-center text-sm text-slate-500">Belum ada aktivitas pada filter ini.</td></tr>
                    @endforelse
                </tbody>
            </x-table>
            @if ($logAktivitas->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">{{ $logAktivitas->links() }}</div>
            @endif
        </x-card>
    </div>
</x-app-layout>
