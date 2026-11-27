<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PendudukTemplateExport implements FromArray, WithColumnFormatting, WithHeadings
{
    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return [
            'nik',
            'no_kk',
            'nama',
            'tempat_lahir',
            'tgl_lahir',
            'jenis_kelamin',
            'agama',
            'pekerjaan',
            'status_perkawinan',
            'kewarganegaraan',
            'alamat',
            'rt',
            'rw',
            'dusun',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => '@',
            'B' => '@',
            'L' => '@',
            'M' => '@',
        ];
    }
}
