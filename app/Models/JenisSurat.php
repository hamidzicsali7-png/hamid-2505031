<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisSurat extends Model
{
    use HasFactory;

    protected $table = 'jenis_surat';
    protected $fillable = ['kode', 'nama', 'deskripsi', 'template_view', 'aktif', 'field_tambahan'];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
            'field_tambahan' => 'array',
        ];
    }

    public function syaratSurat(): HasMany
    {
        return $this->hasMany(SyaratSurat::class);
    }

    public function pengajuanSurat(): HasMany
    {
        return $this->hasMany(PengajuanSurat::class);
    }

    public function nomorSuratCounter(): HasMany
    {
        return $this->hasMany(NomorSuratCounter::class);
    }
}
