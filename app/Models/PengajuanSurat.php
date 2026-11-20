<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanSurat extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_surat';
    protected $fillable = ['nomor_surat', 'penduduk_id', 'jenis_surat_id', 'keperluan', 'keterangan', 'status', 'catatan', 'diverifikasi_oleh', 'disetujui_oleh', 'tanggal_pengajuan', 'tanggal_selesai', 'file_pdf'];

    protected function casts(): array
    {
        return [
            'status' => \App\StatusPengajuan::class,
            'tanggal_pengajuan' => 'datetime',
            'tanggal_selesai' => 'datetime',
        ];
    }

    public function penduduk() { return $this->belongsTo(Penduduk::class); }
    public function jenisSurat() { return $this->belongsTo(JenisSurat::class); }
    public function diverifikasiOleh() { return $this->belongsTo(User::class, 'diverifikasi_oleh'); }
    public function disetujuiOleh() { return $this->belongsTo(User::class, 'disetujui_oleh'); }
}
