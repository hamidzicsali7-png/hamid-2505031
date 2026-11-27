<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PengajuanSuratRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'jenis_surat_id' => ['required', 'exists:jenis_surat,id'],
            'keperluan' => ['required', 'string', 'max:255'],
            'data_tambahan' => ['nullable', 'array'],
            'data_tambahan.*' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
