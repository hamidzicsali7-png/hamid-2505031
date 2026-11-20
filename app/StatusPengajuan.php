<?php

namespace App;

enum StatusPengajuan: string
{
    case Draft = 'draft';
    case Diajukan = 'diajukan';
    case Diverifikasi = 'diverifikasi';
    case Disetujui = 'disetujui';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';
    case Revisi = 'revisi';
}
