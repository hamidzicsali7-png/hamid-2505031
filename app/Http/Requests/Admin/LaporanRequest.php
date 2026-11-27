<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['admin', 'kades', 'sekretaris']) ?? false;
    }

    public function rules(): array
    {
        return [
            'periode' => ['nullable', Rule::in(['bulanan', 'tahunan', 'rentang'])],
            'bulan' => ['nullable', 'required_if:periode,bulanan', 'integer', 'between:1,12'],
            'tahun' => ['nullable', 'required_if:periode,bulanan,tahunan', 'integer', 'between:2000,2100'],
            'tanggal_mulai' => ['nullable', 'required_if:periode,rentang', 'date'],
            'tanggal_selesai' => ['nullable', 'required_if:periode,rentang', 'date', 'after_or_equal:tanggal_mulai'],
            'jenis_surat_id' => ['nullable', 'integer', Rule::exists('jenis_surat', 'id')],
        ];
    }
}
