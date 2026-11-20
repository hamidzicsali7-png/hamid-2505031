<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SIKADES') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-soft px-4 py-10 font-sans text-slate-800">
    <main class="mx-auto flex min-h-[calc(100vh-5rem)] w-full max-w-md flex-col justify-center">
        <a href="/" class="mb-7 flex flex-col items-center text-center">
            <span class="grid h-14 w-14 place-items-center rounded-2xl bg-primary text-xl font-extrabold text-white shadow-lg shadow-primary/20">S</span>
            <span class="mt-3 text-xl font-extrabold tracking-tight text-slate-900">SIKADES</span>
            <span class="mt-1 text-sm text-slate-500">Layanan Surat Keterangan Desa</span>
        </a>

        <section class="rounded-3xl bg-white p-6 shadow-[0_20px_70px_rgba(46,46,143,.10)] ring-1 ring-slate-900/5 sm:p-8">
            {{ $slot }}
        </section>
        <p class="mt-6 text-center text-xs text-slate-400">Pelayanan administrasi desa, lebih mudah dan transparan.</p>
    </main>
</body>
</html>
