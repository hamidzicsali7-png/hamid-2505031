@props(['variant' => 'primary', 'type' => 'button'])

@php
    $variantClasses = match ($variant) {
        'secondary' => 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50',
        'danger' => 'bg-danger text-white hover:bg-red-700',
        default => 'bg-primary text-white hover:bg-primary-800',
    };
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->class(['inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold shadow-sm transition focus:outline-none focus:ring-2 focus:ring-primary/30 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60', $variantClasses]) }}
>
    {{ $slot }}
</button>
