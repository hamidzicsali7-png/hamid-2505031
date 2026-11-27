<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\JenisSuratRequest;
use App\Models\JenisSurat;
use Illuminate\Support\Facades\DB;

class JenisSuratController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(JenisSurat::class, 'jenis_surat');
    }

    public function index()
    {
        $jenisSurat = JenisSurat::withCount('syaratSurat')->orderBy('nama')->paginate(15);

        return view('admin.jenis-surat.index', compact('jenisSurat'));
    }

    public function create()
    {
        return view('admin.jenis-surat.create', [
            'jenisSurat' => new JenisSurat(['aktif' => true]),
            'syarat' => [],
        ]);
    }

    public function store(JenisSuratRequest $request)
    {
        $data = $request->validated();
        $syarat = $data['syarat'] ?? [];
        unset($data['syarat']);
        $data['field_tambahan'] = $this->normalisasiFieldTambahan($data['field_tambahan'] ?? []);

        DB::transaction(function () use ($data, $syarat): void {
            $jenisSurat = JenisSurat::create($data);
            $jenisSurat->syaratSurat()->createMany(
                array_map(fn (string $nama): array => ['nama_dokumen' => $nama, 'wajib' => true], $syarat),
            );
        });

        return to_route('admin.jenis-surat.index')->with('success', 'Jenis surat berhasil ditambahkan.');
    }

    public function show(JenisSurat $jenisSurat)
    {
        $jenisSurat->load('syaratSurat');

        return view('admin.jenis-surat.show', compact('jenisSurat'));
    }

    public function edit(JenisSurat $jenisSurat)
    {
        $jenisSurat->load('syaratSurat');

        return view('admin.jenis-surat.edit', [
            'jenisSurat' => $jenisSurat,
            'syarat' => $jenisSurat->syaratSurat->pluck('nama_dokumen')->all(),
        ]);
    }

    public function update(JenisSuratRequest $request, JenisSurat $jenisSurat)
    {
        $data = $request->validated();
        $syarat = $data['syarat'] ?? [];
        unset($data['syarat']);
        $data['field_tambahan'] = $this->normalisasiFieldTambahan($data['field_tambahan'] ?? []);

        DB::transaction(function () use ($data, $syarat, $jenisSurat): void {
            $jenisSurat->update($data);
            $jenisSurat->syaratSurat()->delete();
            $jenisSurat->syaratSurat()->createMany(
                array_map(fn (string $nama): array => ['nama_dokumen' => $nama, 'wajib' => true], $syarat),
            );
        });

        return to_route('admin.jenis-surat.show', $jenisSurat)->with('success', 'Jenis surat berhasil diperbarui.');
    }

    public function destroy(JenisSurat $jenisSurat)
    {
        if ($jenisSurat->pengajuanSurat()->exists()) {
            return back()->with('error', 'Jenis surat tidak dapat dihapus karena sudah digunakan pada pengajuan.');
        }

        $jenisSurat->delete();

        return to_route('admin.jenis-surat.index')->with('success', 'Jenis surat berhasil dihapus.');
    }

    /**
     * @param  array<int, array{name: string, label: string, type: string, required?: bool|string}>  $fields
     * @return array<int, array{name: string, label: string, type: string, required: bool}>
     */
    private function normalisasiFieldTambahan(array $fields): array
    {
        return array_map(fn (array $field): array => [
            'name' => $field['name'],
            'label' => $field['label'],
            'type' => $field['type'],
            'required' => (bool) ($field['required'] ?? false),
        ], $fields);
    }
}
