<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PendudukTemplateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PendudukRequest;
use App\Imports\PendudukImport;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;

class PendudukController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Penduduk::class, 'penduduk');
    }

    public function index(Request $request)
    {
        $query = Penduduk::query();

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($query) use ($search): void {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('no_kk', 'like', "%{$search}%");
            });
        }

        $query->when($request->filled('rt'), fn ($query) => $query->where('rt', $request->input('rt')));
        $query->when($request->filled('rw'), fn ($query) => $query->where('rw', $request->input('rw')));

        $penduduk = $query->orderBy('nama')->paginate(15)->withQueryString();
        $daftarRt = Penduduk::query()->select('rt')->distinct()->orderBy('rt')->pluck('rt');
        $daftarRw = Penduduk::query()->select('rw')->distinct()->orderBy('rw')->pluck('rw');

        return view('admin.penduduk.index', compact('penduduk', 'daftarRt', 'daftarRw'));
    }

    public function create()
    {
        $users = User::whereDoesntHave('penduduk')->orderBy('name')->get();

        return view('admin.penduduk.create', ['penduduk' => new Penduduk, 'users' => $users]);
    }

    public function store(PendudukRequest $request)
    {
        Penduduk::create($request->validated());

        return to_route('admin.penduduk.index')->with('success', 'Data penduduk berhasil ditambahkan.');
    }

    public function show(Penduduk $penduduk)
    {
        $penduduk->load('user');

        return view('admin.penduduk.show', compact('penduduk'));
    }

    public function edit(Penduduk $penduduk)
    {
        $users = User::whereDoesntHave('penduduk')
            ->orWhereKey($penduduk->user_id)
            ->orderBy('name')
            ->get();

        return view('admin.penduduk.edit', compact('penduduk', 'users'));
    }

    public function update(PendudukRequest $request, Penduduk $penduduk)
    {
        $penduduk->update($request->validated());

        return to_route('admin.penduduk.show', $penduduk)->with('success', 'Data penduduk berhasil diperbarui.');
    }

    public function destroy(Penduduk $penduduk)
    {
        if ($penduduk->pengajuanSurat()->exists()) {
            return back()->with('error', 'Data penduduk tidak dapat dihapus karena sudah memiliki pengajuan surat.');
        }

        $penduduk->delete();

        return to_route('admin.penduduk.index')->with('success', 'Data penduduk berhasil dihapus.');
    }

    public function template()
    {
        $this->authorize('viewAny', Penduduk::class);

        return Excel::download(new PendudukTemplateExport, 'template-impor-penduduk.xlsx');
    }

    public function import(Request $request)
    {
        $this->authorize('create', Penduduk::class);

        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:10240'],
        ]);
        $jumlahSebelum = Penduduk::count();
        $impor = new PendudukImport;

        try {
            DB::transaction(fn () => Excel::import($impor, $validated['file']));
        } catch (ExcelValidationException $exception) {
            $errors = [];

            foreach ($exception->failures() as $failure) {
                foreach ($failure->errors() as $message) {
                    $errors[] = 'Baris '.$failure->row().': '.$message;
                }
            }

            throw ValidationException::withMessages([
                'file' => implode(' ', array_slice($errors, 0, 10)),
            ]);
        } catch (QueryException $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'file' => 'Impor gagal karena ada data yang berbenturan dengan data tersimpan. Periksa NIK dan coba lagi.',
            ]);
        }

        $jumlahDiimpor = Penduduk::count() - $jumlahSebelum;

        return to_route('admin.penduduk.index')
            ->with('success', "{$jumlahDiimpor} data penduduk berhasil diimpor.");
    }
}
