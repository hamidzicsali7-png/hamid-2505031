<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NomorSuratCounter extends Model
{
    use HasFactory;

    protected $table = 'nomor_surat_counter';
    protected $fillable = ['jenis_surat_id', 'tahun', 'urutan_terakhir'];

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'urutan_terakhir' => 'integer',
        ];
    }

    public function jenisSurat(): BelongsTo
    {
        return $this->belongsTo(JenisSurat::class);
    }
}
