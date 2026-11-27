<?php

namespace App\Enums;

enum StatusPengajuan: string
{
    case Draft = 'draft';
    case Diajukan = 'diajukan';
    case Diverifikasi = 'diverifikasi';
    case Disetujui = 'disetujui';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';
    case Revisi = 'revisi';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Diajukan => 'Diajukan',
            self::Diverifikasi => 'Diverifikasi',
            self::Disetujui => 'Disetujui',
            self::Selesai => 'Selesai',
            self::Ditolak => 'Ditolak',
            self::Revisi => 'Perlu Revisi',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Diajukan, self::Revisi => 'warning',
            self::Diverifikasi => 'info',
            self::Disetujui => 'primary',
            self::Selesai => 'success',
            self::Ditolak => 'danger',
        };
    }
}
