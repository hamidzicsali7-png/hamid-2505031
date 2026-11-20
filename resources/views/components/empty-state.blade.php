@props([
    'title' => 'Belum ada data',
    'description' => 'Data akan muncul di sini setelah tersedia.',
    'actionLabel' => null,
    'actionUrl' => null,
])

<div {{ $attributes->class(['flex flex-col items-center justify-center px-6 py-12 text-center']) }}>
    <span class="grid h-14 w-14 place-items-center rounded-2xl bg-primary-50 text-primary">
        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 4h7l5 5v11H8a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm7 0v5h5M10 14h6m-6 3h4"/></svg>
    </span>
    <h3 class="mt-4 font-bold text-slate-800">{{ $title }}</h3>
    <p class="mt-1 max-w-sm text-sm text-slate-500">{{ $description }}</p>
    @if ($actionLabel && $actionUrl)
        <a href="{{ $actionUrl }}" class="mt-5 text-sm font-semibold text-primary hover:underline">{{ $actionLabel }}</a>
    @endif
</div>
