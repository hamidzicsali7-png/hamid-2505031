@php
    $message = session('success') ?? session('status') ?? session('error');
    $isError = session()->has('error');
@endphp

@if ($message)
    <div
        x-data="{ visible: true }"
        x-show="visible"
        role="status"
        class="mb-5 flex items-start justify-between gap-4 rounded-2xl border px-4 py-3 text-sm {{ $isError ? 'border-red-200 bg-red-50 text-danger' : 'border-emerald-200 bg-emerald-50 text-success' }}"
    >
        <p>{{ $message }}</p>
        <button type="button" @click="visible = false" class="font-bold" aria-label="Tutup pesan">×</button>
    </div>
@endif
