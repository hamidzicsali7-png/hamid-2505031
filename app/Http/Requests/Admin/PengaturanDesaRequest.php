<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PengaturanDesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'nama_desa' => ['required', 'string', 'max:255'],
            'kecamatan' => ['required', 'string', 'max:255'],
            'kabupaten' => ['required', 'string', 'max:255'],
            'provinsi' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:2000'],
            'kode_pos' => ['required', 'string', 'max:10'],
            'telepon' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'nama_kades' => ['required', 'string', 'max:255'],
            'format_nomor_surat' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}
