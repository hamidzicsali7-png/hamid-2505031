@props([
    'title',
    'value',
    'trend' => null,
    'direction' => 'up',
    'caption' => 'dari minggu lalu',
    'icon' => 'chart',
])

@php
    $trendColor = $direction === 'down' ? 'text-danger bg-red-50' : 'text-success bg-emerald-50';
    $chartPath = $direction === 'down'
        ? 'M2 5c8 2 10 1 16 7s10 2 16 7 11 2 16 5'
        : 'M2 20c8-1 10-10 16-8s10 5 16-1 10-5 16-7';
@endphp

<x-card class="overflow-hidden" padding="p-5">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ $title }}</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ $value }}</p>
        </div>
        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-primary-50 text-primary">
            @if ($icon === 'users')
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m16 0v-2a4 4 0 0 0-3-3.87M14 3.13a4 4 0 0 1 0 7.75M14 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/></svg>
            @elseif ($icon === 'document')
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 4h7l5 5v11H8a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm7 0v5h5M10 14h6m-6 3h6"/></svg>
            @elseif ($icon === 'check')
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/></svg>
            @else
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17 9 11l4 4 8-9M15 6h6v6"/></svg>
            @endif
        </span>
    </div>

    <div class="mt-4 flex items-center gap-2">
        @if ($trend)
            <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-semibold {{ $trendColor }}">
                @if ($direction === 'down')
                    <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="m3 5 5 5 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                @else
                    <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="m3 11 5-5 5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                @endif
                {{ $trend }}
            </span>
            <span class="text-xs text-slate-400">{{ $caption }}</span>
        @endif
    </div>

    <svg class="-mb-5 mt-3 h-10 w-full" viewBox="0 0 56 24" preserveAspectRatio="none" aria-hidden="true">
        <path d="{{ $chartPath }}" fill="none" stroke="{{ $direction === 'down' ? '#d64550' : '#2E2E8F' }}" stroke-width="2" stroke-linecap="round"/>
    </svg>
</x-card>
