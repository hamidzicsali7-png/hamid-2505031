<x-app-layout>
    <div class="mx-auto max-w-4xl space-y-5">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <a href="{{ route('admin.penduduk.index') }}" class="text-sm font-semibold text-primary hover:underline">← Kembali ke penduduk</a>
                <h2 class="mt-3 text-2xl font-extrabold text-slate-900">{{ $penduduk->nama }}</h2>
                <p class="mt-1 text-sm text-slate-500">NIK {{ $penduduk->nik }}</p>
            </div>
            @can('update', $penduduk)
                <a href="{{ route('admin.penduduk.edit', $penduduk) }}" class="inline-flex rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white">Ubah Data</a>
            @endcan
        </div>
        <x-card padding="p-5 sm:p-7">
            <dl class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                @foreach ([
                    'Nomor KK' => $penduduk->no_kk,
                    'Tempat, tanggal lahir' => $penduduk->tempat_lahir.', '.$penduduk->tgl_lahir->locale('id')->translatedFormat('d F Y'),
                    'Jenis kelamin' => $penduduk->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                    'Agama' => $penduduk->agama,
                    'Pekerjaan' => $penduduk->pekerjaan,
                    'Status perkawinan' => $penduduk->status_perkawinan,
                    'Kewarganegaraan' => $penduduk->kewarganegaraan,
                    'RT / RW' => $penduduk->rt.' / '.$penduduk->rw,
                    'Dusun' => $penduduk->dusun ?: '—',
                    'Akun warga' => $penduduk->user?->email ?: 'Belum ditautkan',
                ] as $label => $value)
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $label }}</dt><dd class="mt-1 font-semibold text-slate-800">{{ $value }}</dd></div>
                @endforeach
                <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Alamat</dt><dd class="mt-1 font-semibold text-slate-800">{{ $penduduk->alamat }}</dd></div>
            </dl>
        </x-card>
        @can('delete', $penduduk)
            <form method="POST" action="{{ route('admin.penduduk.destroy', $penduduk) }}" onsubmit="return confirm('Hapus data penduduk ini?')">
                @csrf @method('DELETE')
                <x-button type="submit" variant="danger">Hapus Penduduk</x-button>
            </form>
        @endcan
    </div>
</x-app-layout>
