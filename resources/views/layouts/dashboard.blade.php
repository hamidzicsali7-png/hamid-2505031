@php
    $hour = now()->hour;
    $greeting = match (true) {
        $hour < 11 => 'Selamat Pagi',
        $hour < 15 => 'Selamat Siang',
        $hour < 18 => 'Selamat Sore',
        default => 'Selamat Malam',
    };

    $isStaff = auth()->user()->hasAnyRole(['admin', 'kades', 'sekretaris']);
    $menu = $isStaff
        ? [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'home'],
            ['label' => 'Pengajuan', 'route' => 'admin.pengajuan.index', 'icon' => 'document'],
            ['label' => 'Penduduk', 'route' => 'admin.penduduk.index', 'icon' => 'users'],
            ['label' => 'Jenis Surat', 'route' => 'admin.jenis-surat.index', 'icon' => 'document'],
            ['label' => 'Pejabat', 'route' => 'admin.pejabat-desa.index', 'icon' => 'users'],
            ['label' => 'Pengaturan', 'route' => 'admin.pengaturan-desa.index', 'icon' => 'settings'],
        ]
        : [
            ['label' => 'Beranda', 'route' => 'warga.dashboard', 'icon' => 'home'],
            ['label' => 'Pengajuan', 'route' => 'warga.pengajuan.index', 'icon' => 'document'],
            ['label' => 'Ajukan Surat', 'route' => 'warga.pengajuan.create', 'icon' => 'plus'],
        ];

    if (auth()->user()->hasRole('kades')) {
        array_splice($menu, 2, 0, [[
            'label' => 'Antrean Persetujuan',
            'route' => 'admin.antrean-persetujuan.index',
            'icon' => 'approval',
        ]]);
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'SIKADES') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-soft font-sans text-slate-800 antialiased">
    <aside class="fixed bottom-4 left-4 right-4 z-40 flex h-[72px] items-center justify-between rounded-[26px] bg-slate-950 px-3 text-white shadow-xl shadow-primary/20 md:bottom-auto md:left-5 md:right-auto md:top-1/2 md:h-auto md:w-[76px] md:-translate-y-1/2 md:flex-col md:rounded-[30px] md:px-0 md:py-4">
        <a href="{{ route('dashboard') }}" class="hidden h-12 w-12 place-items-center rounded-2xl bg-primary text-lg font-extrabold text-white md:grid" aria-label="SIKADES">
            S
        </a>
        <nav class="flex w-full items-center justify-around md:mt-7 md:flex-col md:gap-3" aria-label="Navigasi utama">
            @foreach ($menu as $item)
                @php($active = request()->routeIs($item['route'], str_replace('.index', '.*', $item['route'])))
                <a
                    href="{{ route($item['route']) }}"
                    @if ($active) aria-current="page" @endif
                    title="{{ $item['label'] }}"
                    class="group relative flex min-w-0 flex-1 flex-col items-center gap-1 rounded-2xl px-1 py-2 text-white/65 transition hover:bg-white/10 hover:text-white md:h-12 md:w-12 md:flex-none md:justify-center md:px-0 {{ $active ? 'bg-white/15 !text-white ring-1 ring-white/10' : '' }}"
                >
                    <span class="grid h-6 w-6 place-items-center">
                        @if ($item['icon'] === 'home')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m3 10 9-7 9 7v9a2 2 0 0 1-2 2h-4v-7H9v7H5a2 2 0 0 1-2-2v-9Z"/></svg>
                        @elseif ($item['icon'] === 'document')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 3h7l5 5v11a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 0v5h5M10 13h6m-6 4h6"/></svg>
                        @elseif ($item['icon'] === 'users')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm10 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        @elseif ($item['icon'] === 'settings')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m19.4 15 .1.1a1.7 1.7 0 0 1-2.4 2.4l-.1-.1a1.7 1.7 0 0 0-2.9 1.2v.2a1.7 1.7 0 0 1-3.4 0v-.2A1.7 1.7 0 0 0 7.8 17l-.1.1a1.7 1.7 0 0 1-2.4-2.4l.1-.1a1.7 1.7 0 0 0-1.2-2.9H4a1.7 1.7 0 0 1 0-3.4h.2A1.7 1.7 0 0 0 5.4 6l-.1-.1a1.7 1.7 0 0 1 2.4-2.4l.1.1a1.7 1.7 0 0 0 2.9-1.2v-.2a1.7 1.7 0 0 1 3.4 0v.2A1.7 1.7 0 0 0 17 3.4l.1-.1a1.7 1.7 0 0 1 2.4 2.4l-.1.1a1.7 1.7 0 0 0 1.2 2.9h.2a1.7 1.7 0 0 1 0 3.4h-.2a1.7 1.7 0 0 0-1.2 2.9Z"/></svg>
                        @elseif ($item['icon'] === 'approval')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.5 11 14l4-4m5 2a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14"/></svg>
                        @endif
                    </span>
                    <span class="text-[8px] font-medium leading-none md:hidden">{{ $item['label'] }}</span>
                    <span class="pointer-events-none absolute left-[calc(100%+0.75rem)] top-1/2 hidden -translate-y-1/2 whitespace-nowrap rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white opacity-0 shadow-lg transition group-hover:opacity-100 md:block">
                        {{ $item['label'] }}
                    </span>
                </a>
            @endforeach
        </nav>
    </aside>

    <div class="min-h-screen pb-28 md:pl-28 md:pr-7 md:pt-2">
        <header class="mx-auto flex max-w-[1500px] items-center justify-between gap-4 px-5 py-5 sm:px-8 md:px-0 md:py-7">
            <div class="min-w-0">
                <p class="text-xs font-semibold capitalize tracking-wide text-slate-400">
                    {{ now()->locale('id')->translatedFormat('l, d F Y') }}
                </p>
                <h1 class="mt-1 truncate text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">
                    {{ $greeting }}, {{ auth()->user()->name }}!
                </h1>
            </div>

            <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                <div class="relative" x-data="{ notificationsOpen: false }" @click.outside="notificationsOpen = false" @keydown.escape.window="notificationsOpen = false">
                    <button type="button" @click="notificationsOpen = !notificationsOpen" :aria-expanded="notificationsOpen" class="relative grid h-11 w-11 place-items-center rounded-2xl bg-white text-slate-500 shadow-sm ring-1 ring-slate-900/5 transition hover:text-primary" aria-label="Notifikasi">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9m-8 12h4"/></svg>
                        @if ($jumlahNotifikasiBelumDibaca > 0)
                            <span class="absolute -right-1 -top-1 grid min-h-5 min-w-5 place-items-center rounded-full bg-danger px-1 text-[10px] font-bold text-white ring-2 ring-white">{{ $jumlahNotifikasiBelumDibaca > 9 ? '9+' : $jumlahNotifikasiBelumDibaca }}</span>
                        @endif
                    </button>

                    <div x-cloak x-show="notificationsOpen" x-transition class="absolute right-0 z-50 mt-3 w-[min(22rem,calc(100vw-2rem))] overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-slate-900/5" style="display: none;">
                        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                            <p class="text-sm font-bold text-slate-800">Notifikasi</p>
                            <span class="text-xs text-slate-400">{{ $jumlahNotifikasiBelumDibaca }} belum dibaca</span>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            @forelse ($notifikasiBelumDibaca as $notifikasi)
                                <form method="POST" action="{{ route('notifikasi.baca', $notifikasi->id) }}" class="border-b border-slate-50 last:border-0">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-3 text-left transition hover:bg-soft">
                                        <span class="block text-xs font-bold text-slate-800">{{ $notifikasi->data['title'] ?? 'Pembaruan pengajuan' }}</span>
                                        <span class="mt-1 block text-xs leading-relaxed text-slate-500">{{ $notifikasi->data['message'] ?? 'Ada pembaruan pada pengajuan Anda.' }}</span>
                                        <span class="mt-1 block text-[10px] text-slate-400">{{ $notifikasi->created_at->locale('id')->diffForHumans() }} · klik untuk membaca</span>
                                    </button>
                                </form>
                            @empty
                                <p class="px-4 py-6 text-center text-xs text-slate-500">Belum ada notifikasi baru.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                    <button type="button" @click="open = !open" :aria-expanded="open" class="flex items-center gap-2 rounded-2xl bg-white p-1.5 pr-2.5 shadow-sm ring-1 ring-slate-900/5 sm:gap-3 sm:pr-3">
                        <span class="grid h-9 w-9 place-items-center rounded-xl bg-primary-100 text-sm font-bold text-primary">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="hidden text-left sm:block">
                            <span class="block max-w-32 truncate text-xs font-bold text-slate-800">{{ auth()->user()->name }}</span>
                            <span class="block text-[10px] capitalize text-slate-400">{{ auth()->user()->getRoleNames()->first() }}</span>
                        </span>
                        <svg class="hidden h-4 w-4 text-slate-400 sm:block" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 7.47a.75.75 0 0 1 1.06 0L10 11.19l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                    </button>

                    <div x-cloak x-show="open" x-transition class="absolute right-0 z-50 mt-3 w-48 rounded-2xl bg-white p-2 shadow-xl ring-1 ring-slate-900/5" style="display: none;">
                        <a href="{{ route('profile.edit') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 hover:bg-soft hover:text-primary">Profil saya</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl px-3 py-2.5 text-left text-sm font-medium text-danger hover:bg-red-50">Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-[1500px] px-5 sm:px-8 md:px-0">
            <x-flash-message />
            {{ $slot }}
        </main>
    </div>
</body>
</html>
