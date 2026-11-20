<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $ringkasan = [
            'diajukan' => \App\Models\PengajuanSurat::where('status', 'diajukan')->count(),
            'diproses' => \App\Models\PengajuanSurat::whereIn('status', ['diverifikasi', 'disetujui'])->count(),
            'selesai' => \App\Models\PengajuanSurat::where('status', 'selesai')->count(),
            'penduduk' => \App\Models\Penduduk::count(),
        ];
        $terbaru = \App\Models\PengajuanSurat::with(['penduduk', 'jenisSurat'])->latest()->limit(6)->get();
        $bulanan = \App\Models\PengajuanSurat::selectRaw("strftime('%m', created_at) as bulan, count(*) as total")
            ->whereRaw("strftime('%Y', created_at) = ?", [now()->format('Y')])
            ->groupBy('bulan')->orderBy('bulan')->pluck('total', 'bulan');
        return view('admin.dashboard', compact('ringkasan', 'terbaru', 'bulanan'));
    }
}
