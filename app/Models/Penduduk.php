<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penduduk extends Model
{
    use HasFactory;

    protected $table = 'penduduk';
    protected $fillable = [
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
        'user_id',
    ];

    protected function casts(): array
    {
        return ['tgl_lahir' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pengajuanSurat(): HasMany
    {
        return $this->hasMany(PengajuanSurat::class);
    }
}
