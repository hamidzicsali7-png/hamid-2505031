<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanDesa extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_desa';
    protected $fillable = [
        'nama_desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'alamat',
        'kode_pos',
        'telepon',
        'email',
        'logo_path',
        'nama_kades',
        'format_nomor_surat',
    ];
}
