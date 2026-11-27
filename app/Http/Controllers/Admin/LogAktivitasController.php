<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LogAktivitasRequest;
use App\Models\LogAktivitas;
use Illuminate\View\View;

class LogAktivitasController extends Controller
{
    public function index(LogAktivitasRequest $request): View
    {
        $filter = $request->validated();
        $query = LogAktivitas::query()->with('user');

        if ($filter['aksi'] ?? false) {
            $query->where('aksi', $filter['aksi']);
        }

        if ($filter['q'] ?? false) {
            $kataKunci = '%'.$filter['q'].'%';
            $query->where(function ($query) use ($kataKunci): void {
                $query->where('keterangan', 'like', $kataKunci)
                    ->orWhere('aksi', 'like', $kataKunci)
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', $kataKunci));
            });
        }

        if ($filter['tanggal_mulai'] ?? false) {
            $query->whereDate('created_at', '>=', $filter['tanggal_mulai']);
        }

        if ($filter['tanggal_selesai'] ?? false) {
            $query->whereDate('created_at', '<=', $filter['tanggal_selesai']);
        }

        return view('admin.log-aktivitas.index', [
            'logAktivitas' => $query->latest()->paginate(20)->withQueryString(),
            'daftarAksi' => LogAktivitas::query()->select('aksi')->distinct()->orderBy('aksi')->pluck('aksi'),
        ]);
    }
}
