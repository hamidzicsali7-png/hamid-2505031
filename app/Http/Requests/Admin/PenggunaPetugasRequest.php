<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PenggunaPetugasRequest extends FormRequest
{
    public const ROLES = ['admin', 'kades', 'sekretaris'];

    public function authorize(): bool
    {
        $pengguna = $this->route('pengguna');

        return $this->user()?->hasRole('admin')
            && (! $pengguna instanceof User || $pengguna->hasAnyRole(self::ROLES));
    }

    public function rules(): array
    {
        $pengguna = $this->route('pengguna');

        return [
            'name' => ['required', 'string', 'max:255'],
            'nik' => [
                'required',
                'digits:16',
                Rule::unique('users', 'nik')->ignore($pengguna instanceof User ? $pengguna->id : null),
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($pengguna instanceof User ? $pengguna->id : null),
            ],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(self::ROLES)],
            'password' => [
                $this->isMethod('POST') ? 'required' : 'nullable',
                'confirmed',
                Password::defaults(),
            ],
        ];
    }
}
