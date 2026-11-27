<?php

namespace App\Models;

use App\Enums\StatusPengajuan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PengajuanSurat extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_surat';
    protected $fillable = [
        'kode_pengajuan',
        'user_id',
        'penduduk_id',
        'jenis_surat_id',
        'keperluan',
        'data_tambahan',
        'status',
        'catatan',
        'diverifikasi_oleh',
        'disetujui_oleh',
        'tgl_pengajuan',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusPengajuan::class,
            'data_tambahan' => 'array',
            'tgl_pengajuan' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function penduduk(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class);
    }

    public function jenisSurat(): BelongsTo
    {
        return $this->belongsTo(JenisSurat::class);
    }

    public function diverifikasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function dokumenPengajuan(): HasMany
    {
        return $this->hasMany(DokumenPengajuan::class);
    }

    public function suratTerbit(): HasOne
    {
        return $this->hasOne(SuratTerbit::class);
    }
}
