<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIKADES</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-[#f2efff] text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif">
<div class="min-h-screen flex">
    <aside class="fixed inset-y-4 left-4 w-20 rounded-3xl bg-[#172554] text-white flex flex-col items-center py-5 shadow-xl z-20">
        <div class="w-11 h-11 rounded-2xl bg-indigo-500 grid place-items-center font-bold mb-8">S</div>
        <nav class="flex-1 space-y-3">
            <a href="{{ route('dashboard') }}" title="Beranda" class="w-11 h-11 rounded-2xl grid place-items-center hover:bg-white/10 {{ request()->routeIs('dashboard')?'bg-indigo-500':'' }}">⌂</a>
            @if(auth()->user()->hasAnyRole(['admin','kades','sekretaris']))
            <a href="{{ route('admin.pengajuan.index') }}" title="Pengajuan" class="w-11 h-11 rounded-2xl grid place-items-center hover:bg-white/10 {{ request()->is('admin/*')?'bg-indigo-500':'' }}">▤</a>
            @else
            <a href="{{ route('warga.pengajuan.index') }}" title="Pengajuan" class="w-11 h-11 rounded-2xl grid place-items-center hover:bg-white/10 {{ request()->is('warga/*')?'bg-indigo-500':'' }}">▤</a>
            @endif
        </nav>
        <form method="POST" action="{{ route('logout') }}">@csrf<button title="Keluar" class="w-11 h-11 rounded-2xl grid place-items-center hover:bg-red-400/20">↪</button></form>
    </aside>
    <main class="flex-1 ml-28 px-6 py-6 md:px-10">
        {{ $slot }}
    </main>
</div>
</body>
</html>