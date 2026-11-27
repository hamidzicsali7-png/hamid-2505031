<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PengaturanDesaRequest;
use App\Models\PengaturanDesa;
use App\Services\PengaturanDesaService;
use Illuminate\Support\Facades\Storage;

class PengaturanDesaController extends Controller
{
    public function index(PengaturanDesaService $service)
    {
        $this->authorize('viewAny', PengaturanDesa::class);
        $pengaturan = $service->ambilOpsional() ?? new PengaturanDesa;

        return view('admin.pengaturan-desa.index', compact('pengaturan'));
    }

    public function update(PengaturanDesaRequest $request, PengaturanDesaService $service)
    {
        $pengaturan = PengaturanDesa::query()->first();
        $this->authorize($pengaturan ? 'update' : 'create', $pengaturan ?? PengaturanDesa::class);

        $data = $request->safe()->except('logo');
        $logoLama = $pengaturan?->logo_path;

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logo-desa', 'public');
        }

        $pengaturan
            ? $pengaturan->update($data)
            : $pengaturan = PengaturanDesa::create($data);

        if ($request->hasFile('logo') && $logoLama) {
            Storage::disk('public')->delete($logoLama);
        }

        $service->hapusCache();

        return to_route('admin.pengaturan-desa.index')->with('success', 'Profil desa berhasil diperbarui.');
    }
}
