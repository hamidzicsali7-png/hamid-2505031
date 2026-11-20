@props(['padding' => 'p-5'])

<section {{ $attributes->class(['rounded-3xl bg-white shadow-sm ring-1 ring-slate-900/5', $padding]) }}>
    {{ $slot }}
</section>
