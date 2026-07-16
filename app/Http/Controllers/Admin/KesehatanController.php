<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataKesehatan;
use App\Models\FasilitasKesehatanKecamatan;
use App\Models\TenagaKesehatanKecamatan;
use Illuminate\Http\Request;

class KesehatanController extends Controller
{
    public function index()
    {
        $items     = DataKesehatan::orderByDesc('tahun')->get();
        $tenaga    = TenagaKesehatanKecamatan::with('kecamatan')->orderByDesc('tahun')->get();
        $fasilitas = FasilitasKesehatanKecamatan::with('kecamatan')->orderByDesc('tahun')->get();

        return view('admin.kesehatan.index', compact('items', 'tenaga', 'fasilitas'));
    }

    public function create()
    {
        return view('admin.kesehatan.form', ['item' => new DataKesehatan()]);
    }

    public function store(Request $request)
    {
        DataKesehatan::create($this->validated($request));

        return redirect()->route('admin.kesehatan.index')->with('success', 'Data kesehatan ditambahkan.');
    }

    public function edit(DataKesehatan $kesehatan)
    {
        return view('admin.kesehatan.form', ['item' => $kesehatan]);
    }

    public function update(Request $request, DataKesehatan $kesehatan)
    {
        $kesehatan->update($this->validated($request));

        return redirect()->route('admin.kesehatan.index')->with('success', 'Data kesehatan diperbarui.');
    }

    public function destroy(DataKesehatan $kesehatan)
    {
        $kesehatan->delete();

        return redirect()->route('admin.kesehatan.index')->with('success', 'Data kesehatan dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'tahun'                   => ['required', 'integer', 'min:1900', 'max:2100'],
            'jumlah_tempat_tidur_rs'  => ['required', 'integer', 'min:0'],
            'cakupan_imunisasi_dasar' => ['nullable', 'numeric', 'min:0'],
            'sumber'                  => ['nullable', 'string', 'max:255'],
        ]);
    }
}
