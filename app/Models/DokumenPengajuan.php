<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenPengajuan extends Model
{
    use HasFactory;

    protected $table = 'dokumen_pengajuan';
    protected $fillable = ['pengajuan_surat_id', 'nama_dokumen', 'file_path'];

    public function pengajuanSurat(): BelongsTo
    {
        return $this->belongsTo(PengajuanSurat::class);
    }
}
