<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PejabatDesaRequest;
use App\Models\PejabatDesa;
use Illuminate\Support\Facades\Storage;

class PejabatDesaController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(PejabatDesa::class, 'pejabat_desa');
    }

    public function index()
    {
        $pejabat = PejabatDesa::orderBy('jabatan')->orderBy('nama')->paginate(15);

        return view('admin.pejabat-desa.index', compact('pejabat'));
    }

    public function create()
    {
        return view('admin.pejabat-desa.create', ['pejabatDesa' => new PejabatDesa(['aktif' => true])]);
    }

    public function store(PejabatDesaRequest $request)
    {
        $data = $request->safe()->except('tanda_tangan');

        if ($request->hasFile('tanda_tangan')) {
            $data['tanda_tangan_path'] = $request->file('tanda_tangan')->store('tanda-tangan', 'public');
        }

        PejabatDesa::create($data);

        return to_route('admin.pejabat-desa.index')->with('success', 'Pejabat desa berhasil ditambahkan.');
    }

    public function show(PejabatDesa $pejabatDesa)
    {
        return view('admin.pejabat-desa.show', compact('pejabatDesa'));
    }

    public function edit(PejabatDesa $pejabatDesa)
    {
        return view('admin.pejabat-desa.edit', compact('pejabatDesa'));
    }

    public function update(PejabatDesaRequest $request, PejabatDesa $pejabatDesa)
    {
        $data = $request->safe()->except('tanda_tangan');
        $tandaTanganLama = $pejabatDesa->tanda_tangan_path;

        if ($request->hasFile('tanda_tangan')) {
            $data['tanda_tangan_path'] = $request->file('tanda_tangan')->store('tanda-tangan', 'public');
        }

        $pejabatDesa->update($data);

        if ($request->hasFile('tanda_tangan') && $tandaTanganLama) {
            Storage::disk('public')->delete($tandaTanganLama);
        }

        return to_route('admin.pejabat-desa.show', $pejabatDesa)->with('success', 'Data pejabat desa berhasil diperbarui.');
    }

    public function destroy(PejabatDesa $pejabatDesa)
    {
        if ($pejabatDesa->suratTerbit()->exists()) {
            return back()->with('error', 'Pejabat tidak dapat dihapus karena sudah tercatat pada surat terbit.');
        }

        $tandaTangan = $pejabatDesa->tanda_tangan_path;
        $pejabatDesa->delete();

        if ($tandaTangan) {
            Storage::disk('public')->delete($tandaTangan);
        }

        return to_route('admin.pejabat-desa.index')->with('success', 'Pejabat desa berhasil dihapus.');
    }
}
