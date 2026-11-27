<?php

namespace App\Http\Requests;

use App\Models\JenisSurat;
use App\Models\PengajuanSurat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PengajuanSuratRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $pengajuan = $this->route('pengajuan');

        if (! $user?->hasRole('warga')) {
            return false;
        }

        return $pengajuan instanceof PengajuanSurat
            ? $user->can('update', $pengajuan)
            : $user->can('create', PengajuanSurat::class);
    }

    public function rules(): array
    {
        $pengajuan = $this->route('pengajuan');
        $jenisSuratId = $pengajuan instanceof PengajuanSurat
            ? $pengajuan->jenis_surat_id
            : $this->input('jenis_surat_id');
        $jenisSurat = JenisSurat::with('syaratSurat')->find($jenisSuratId);
        $aksi = $this->input('aksi');
        $fieldTambahan = $jenisSurat?->field_tambahan ?? [];
        $syaratSurat = $jenisSurat?->syaratSurat ?? collect();
        $idsSyarat = $syaratSurat->pluck('id')->map(fn ($id): string => (string) $id)->all();

        $rules = [
            'jenis_surat_id' => [
                'required',
                $pengajuan instanceof PengajuanSurat
                    ? Rule::in([$pengajuan->jenis_surat_id])
                    : Rule::exists('jenis_surat', 'id')->where('aktif', true),
            ],
            'keperluan' => ['required', 'string', 'max:255'],
            'aksi' => ['required', Rule::in(['draft', 'ajukan'])],
            'data_tambahan' => $fieldTambahan
                ? ['nullable', 'array:'.implode(',', array_column($fieldTambahan, 'name'))]
                : ['nullable', 'array', 'size:0'],
            'dokumen' => $idsSyarat
                ? ['nullable', 'array:'.implode(',', $idsSyarat)]
                : ['nullable', 'array', 'size:0'],
        ];

        foreach ($fieldTambahan as $field) {
            $required = $aksi === 'ajukan' && ($field['required'] ?? false);
            $typeRules = match ($field['type']) {
                'number' => ['numeric'],
                'date' => ['date'],
                'email' => ['email', 'max:255'],
                default => ['string', 'max:2000'],
            };

            $rules['data_tambahan.'.$field['name']] = [
                $required ? 'required' : 'nullable',
                ...$typeRules,
            ];
        }

        foreach ($syaratSurat as $syarat) {
            $pathTersimpan = $pengajuan instanceof PengajuanSurat
                ? $pengajuan->dokumenPengajuan()
                    ->where('nama_dokumen', $syarat->nama_dokumen)
                    ->value('file_path')
                : null;
            $sudahDiunggah = $pathTersimpan && Storage::disk('local')->exists($pathTersimpan);
            $wajibDiunggah = $aksi === 'ajukan' && $syarat->wajib && ! $sudahDiunggah;

            $rules['dokumen.'.$syarat->id] = [
                $wajibDiunggah ? 'required' : 'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'mimetypes:application/pdf,image/jpeg,image/png',
                'max:2048',
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        $pengajuan = $this->route('pengajuan');
        $jenisSuratId = $pengajuan instanceof PengajuanSurat
            ? $pengajuan->jenis_surat_id
            : $this->input('jenis_surat_id');
        $syaratSurat = JenisSurat::with('syaratSurat')
            ->find($jenisSuratId)
            ?->syaratSurat ?? collect();

        return $syaratSurat
            ->mapWithKeys(fn ($syarat): array => [
                'dokumen.'.$syarat->id => $syarat->nama_dokumen,
            ])
            ->all();
    }
}
