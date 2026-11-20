@props(['name', 'label' => null, 'required' => false])

<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ $label }}</label>
    @endif
    <select
        {{ $attributes->merge([
            'id' => $name,
            'name' => $name,
            'class' => 'block w-full rounded-xl border-slate-200 bg-white text-sm shadow-sm focus:border-primary focus:ring-primary/20',
        ]) }}
        @required($required)
    >
        {{ $slot }}
    </select>
    <x-input-error :messages="$errors->get($name)" class="mt-1.5" />
</div>
