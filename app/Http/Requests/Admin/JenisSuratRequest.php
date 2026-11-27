<?php

namespace App\Http\Requests\Admin;

use App\Models\JenisSurat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JenisSuratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        $jenisSurat = $this->route('jenis_surat');

        return [
            'kode' => ['required', 'string', 'max:30', Rule::unique('jenis_surat', 'kode')->ignore($jenisSurat instanceof JenisSurat ? $jenisSurat->id : null)],
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
            'aktif' => ['required', 'boolean'],
            'syarat' => ['nullable', 'array'],
            'syarat.*' => ['required', 'string', 'max:255', 'distinct'],
            'field_tambahan' => ['nullable', 'array'],
            'field_tambahan.*.name' => ['required', 'string', 'max:80', 'regex:/^[a-z][a-z0-9_]*$/'],
            'field_tambahan.*.label' => ['required', 'string', 'max:255'],
            'field_tambahan.*.type' => ['required', Rule::in(['text', 'textarea', 'number', 'date', 'email'])],
            'field_tambahan.*.required' => ['nullable', 'boolean'],
        ];
    }
}
