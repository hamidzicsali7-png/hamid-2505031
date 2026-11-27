<?php

namespace App\Http\Requests\Admin;

use App\Models\Penduduk;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PendudukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        $penduduk = $this->route('penduduk');

        return [
            'nik' => ['required', 'digits:16', Rule::unique('penduduk', 'nik')->ignore($penduduk instanceof Penduduk ? $penduduk->id : null)],
            'no_kk' => ['required', 'digits:16'],
            'nama' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tgl_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'agama' => ['required', 'string', 'max:100'],
            'pekerjaan' => ['required', 'string', 'max:255'],
            'status_perkawinan' => ['required', 'string', 'max:100'],
            'kewarganegaraan' => ['required', 'string', 'max:100'],
            'alamat' => ['required', 'string', 'max:2000'],
            'rt' => ['required', 'digits_between:1,3'],
            'rw' => ['required', 'digits_between:1,3'],
            'dusun' => ['nullable', 'string', 'max:255'],
            'user_id' => ['nullable', 'integer', 'exists:users,id', Rule::unique('penduduk', 'user_id')->ignore($penduduk instanceof Penduduk ? $penduduk->id : null)],
        ];
    }
}
