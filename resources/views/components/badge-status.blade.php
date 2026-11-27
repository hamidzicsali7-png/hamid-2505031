@props(['status'])

@php
    $statusEnum = $status instanceof \App\Enums\StatusPengajuan
        ? $status
        : \App\Enums\StatusPengajuan::tryFrom((string) $status);
    $label = $statusEnum?->label() ?? ucfirst((string) $status);
    $classes = match ($statusEnum?->color()) {
        'warning' => 'bg-amber-50 text-warning',
        'info' => 'bg-blue-50 text-blue-700',
        'primary' => 'bg-indigo-50 text-primary',
        'success' => 'bg-emerald-50 text-success',
        'danger' => 'bg-red-50 text-danger',
        default => 'bg-slate-100 text-slate-600',
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold', $classes]) }}>
    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-current"></span>
    {{ $label }}
</span>
