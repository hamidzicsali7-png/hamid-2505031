@props(['status'])

@php
    $value = $status instanceof \BackedEnum ? $status->value : (string) $status;
    [$label, $classes] = match ($value) {
        'draft' => ['Draft', 'bg-slate-100 text-slate-600'],
        'diajukan' => ['Diajukan', 'bg-amber-50 text-warning'],
        'diverifikasi' => ['Menunggu TTD', 'bg-blue-50 text-blue-700'],
        'disetujui' => ['Disetujui', 'bg-indigo-50 text-primary'],
        'selesai' => ['Selesai', 'bg-emerald-50 text-success'],
        'ditolak' => ['Ditolak', 'bg-red-50 text-danger'],
        'revisi' => ['Perlu Revisi', 'bg-orange-50 text-orange-700'],
        default => [ucfirst($value), 'bg-slate-100 text-slate-600'],
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold', $classes]) }}>
    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-current"></span>
    {{ $label }}
</span>
