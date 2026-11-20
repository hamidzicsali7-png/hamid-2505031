@props(['name', 'label' => null, 'type' => 'text', 'required' => false, 'value' => null])

<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ $label }}</label>
    @endif
    <input
        {{ $attributes->merge([
            'id' => $name,
            'name' => $name,
            'type' => $type,
            'value' => $type === 'password' ? '' : old($name, $value),
            'class' => 'block w-full rounded-xl border-slate-200 bg-white text-sm shadow-sm placeholder:text-slate-400 focus:border-primary focus:ring-primary/20',
        ]) }}
        @required($required)
    >
    <x-input-error :messages="$errors->get($name)" class="mt-1.5" />
</div>
