<?php

namespace App\Imports;

use App\Models\Penduduk;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PendudukImport implements ToModel, WithHeadingRow, WithValidation
{
    public function prepareForValidation($data, $index): array
    {
        if (isset($data['tgl_lahir']) && is_numeric($data['tgl_lahir'])) {
            $data['tgl_lahir'] = Date::excelToDateTimeObject((float) $data['tgl_lahir'])->format('Y-m-d');
        }

        return $data;
    }

    public function model(array $row): Penduduk
    {
        return new Penduduk([
            'nik' => (string) $row['nik'],
            'no_kk' => (string) $row['no_kk'],
            'nama' => $row['nama'],
            'tempat_lahir' => $row['tempat_lahir'],
            'tgl_lahir' => $row['tgl_lahir'],
            'jenis_kelamin' => strtoupper($row['jenis_kelamin']),
            'agama' => $row['agama'],
            'pekerjaan' => $row['pekerjaan'],
            'status_perkawinan' => $row['status_perkawinan'],
            'kewarganegaraan' => $row['kewarganegaraan'] ?? 'WNI',
            'alamat' => $row['alamat'],
            'rt' => (string) $row['rt'],
            'rw' => (string) $row['rw'],
            'dusun' => $row['dusun'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nik' => ['required', 'digits:16', 'distinct', Rule::unique('penduduk', 'nik')],
            'no_kk' => ['required', 'digits:16'],
            'nama' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tgl_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P', 'l', 'p'])],
            'agama' => ['required', 'string', 'max:100'],
            'pekerjaan' => ['required', 'string', 'max:255'],
            'status_perkawinan' => ['required', 'string', 'max:100'],
            'kewarganegaraan' => ['nullable', 'string', 'max:100'],
            'alamat' => ['required', 'string', 'max:2000'],
            'rt' => ['required', 'digits_between:1,3'],
            'rw' => ['required', 'digits_between:1,3'],
            'dusun' => ['nullable', 'string', 'max:255'],
        ];
    }
}
