<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penduduk extends Model
{
    use HasFactory;

    protected $table = 'penduduk';
    protected $fillable = ['user_id', 'nik', 'nama', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'no_hp'];

    protected function casts(): array
    {
        return ['tanggal_lahir' => 'date'];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function pengajuanSurat() { return $this->hasMany(PengajuanSurat::class, 'penduduk_id'); }
}
