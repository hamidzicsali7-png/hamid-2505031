<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LaporanSuratExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LaporanRequest;
use App\Models\JenisSurat;
use App\Services\LaporanSuratService;
use App\Services\PengaturanDesaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanController extends Controller
{
    public function index(LaporanRequest $request, LaporanSuratService $service): View
    {
        $laporan = $service->buatLaporan($request->validated());

        return view('admin.laporan.index', [
            ...$laporan,
            'jenisSurat' => JenisSurat::query()->orderBy('nama')->get(),
        ]);
    }

    public function excel(
        LaporanRequest $request,
        LaporanSuratService $service,
        PengaturanDesaService $pengaturanDesa,
    ): BinaryFileResponse {
        $laporan = $service->buatLaporan($request->validated());
        $pengaturan = $pengaturanDesa->ambil();
        $namaFile = 'laporan-surat-'.$laporan['tanggalMulai']->format('Ymd').'-'.$laporan['tanggalSelesai']->format('Ymd').'.xlsx';

        return Excel::download(new LaporanSuratExport($laporan, $pengaturan), $namaFile);
    }

    public function pdf(
        LaporanRequest $request,
        LaporanSuratService $service,
        PengaturanDesaService $pengaturanDesa,
    ): Response {
        $laporan = $service->buatLaporan($request->validated());
        $laporan['pengaturan'] = $pengaturanDesa->ambil();
        $pdf = Pdf::loadView('admin.laporan.pdf', $laporan)->setPaper('a4', 'portrait');
        $namaFile = 'laporan-surat-'.$laporan['tanggalMulai']->format('Ymd').'-'.$laporan['tanggalSelesai']->format('Ymd').'.pdf';

        return $pdf->download($namaFile);
    }
}
