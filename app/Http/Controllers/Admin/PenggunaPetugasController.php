<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PenggunaPetugasRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenggunaPetugasController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $query = User::query()
            ->role(PenggunaPetugasRequest::ROLES)
            ->with('roles');

        if ($filter['q'] ?? false) {
            $kataKunci = '%'.$filter['q'].'%';
            $query->where(function ($query) use ($kataKunci): void {
                $query->where('name', 'like', $kataKunci)
                    ->orWhere('email', 'like', $kataKunci);
            });
        }

        return view('admin.pengguna.index', [
            'pengguna' => $query->orderBy('name')->paginate(15)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.pengguna.create', ['pengguna' => new User]);
    }

    public function store(PenggunaPetugasRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $role = $data['role'];
        unset($data['role'], $data['password_confirmation']);

        $pengguna = User::create($data);
        $pengguna->assignRole($role);

        return to_route('admin.pengguna.index')->with('success', 'Akun petugas berhasil dibuat.');
    }

    public function edit(User $pengguna): View
    {
        abort_unless($pengguna->hasAnyRole(PenggunaPetugasRequest::ROLES), 404);

        return view('admin.pengguna.edit', compact('pengguna'));
    }

    public function update(PenggunaPetugasRequest $request, User $pengguna): RedirectResponse
    {
        $data = $request->validated();
        $role = $data['role'];

        if ($pengguna->hasRole('admin') && $role !== 'admin' && User::role('admin')->count() === 1) {
            return back()->withInput()->withErrors([
                'role' => 'Peran admin terakhir tidak dapat diubah.',
            ]);
        }

        $dataAkun = [
            'name' => $data['name'],
            'nik' => $data['nik'],
            'email' => $data['email'],
            'no_hp' => $data['no_hp'] ?? null,
        ];

        if (! empty($data['password'])) {
            $dataAkun['password'] = $data['password'];
        }

        if ($pengguna->email !== $dataAkun['email']) {
            $pengguna->email_verified_at = null;
        }

        $pengguna->fill($dataAkun)->save();
        $pengguna->syncRoles([$role]);

        return to_route('admin.pengguna.index')->with('success', 'Akun petugas berhasil diperbarui.');
    }
}
