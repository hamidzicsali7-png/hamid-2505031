<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PejabatDesa extends Model
{
    use HasFactory;

    protected $table = 'pejabat_desa';
    protected $fillable = ['nama', 'jabatan', 'nip', 'tanda_tangan_path', 'aktif'];

    protected function casts(): array
    {
        return ['aktif' => 'boolean'];
    }

    public function suratTerbit(): HasMany
    {
        return $this->hasMany(SuratTerbit::class, 'pejabat_id');
    }
}
