@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-xl bg-emerald-50 px-3 py-2 text-sm font-semibold text-success']) }}>
        {{ $status }}
    </div>
@endif
