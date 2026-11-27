<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratTerbit extends Model
{
    use HasFactory;

    protected $table = 'surat_terbit';
    protected $fillable = [
        'pengajuan_surat_id',
        'nomor_surat',
        'tgl_terbit',
        'pejabat_id',
        'file_pdf',
        'kode_verifikasi',
    ];

    protected function casts(): array
    {
        return ['tgl_terbit' => 'date'];
    }

    public function pengajuanSurat(): BelongsTo
    {
        return $this->belongsTo(PengajuanSurat::class);
    }

    public function pejabatDesa(): BelongsTo
    {
        return $this->belongsTo(PejabatDesa::class, 'pejabat_id');
    }
}
