<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2E2E8F">
    <title>@yield('title') - {{ config('app.name', 'SIKADES') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-soft px-4 py-10 font-sans text-slate-800">
    <main class="mx-auto flex min-h-[70vh] max-w-xl flex-col items-center justify-center rounded-3xl bg-white p-8 text-center shadow-xl shadow-primary/5 ring-1 ring-slate-900/5 sm:p-12">
        <div class="grid h-16 w-16 place-items-center rounded-2xl bg-primary-50 text-2xl font-extrabold text-primary">S</div>
        <p class="mt-6 text-xs font-extrabold uppercase tracking-[0.2em] text-primary">SIKADES</p>
        <p class="mt-2 text-6xl font-extrabold tracking-tight text-primary-800">@yield('code')</p>
        <h1 class="mt-4 text-2xl font-extrabold text-slate-900">@yield('title')</h1>
        <p class="mt-2 max-w-sm text-sm leading-6 text-slate-500">@yield('message')</p>
        <a href="{{ url('/') }}" class="mt-7 rounded-xl bg-primary px-5 py-3 text-sm font-bold text-white transition hover:bg-primary-800">Kembali ke SIKADES</a>
    </main>
</body>
</html>
