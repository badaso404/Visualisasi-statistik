<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\PendudukKecamatan;
use Illuminate\Http\Request;

class PendudukKecamatanController extends Controller
{
    public function create()
    {
        return view('admin.kependudukan.kecamatan-form', [
            'item'      => new PendudukKecamatan(),
            'kecamatan' => Kecamatan::orderBy('nama_kecamatan')->get(),
        ]);
    }

    public function store(Request $request)
    {
        PendudukKecamatan::create($this->validated($request));

        return redirect()->route('admin.kependudukan.index')->with('success', 'Data penduduk kecamatan ditambahkan.');
    }

    public function edit(PendudukKecamatan $pendudukKecamatan)
    {
        return view('admin.kependudukan.kecamatan-form', [
            'item'      => $pendudukKecamatan,
            'kecamatan' => Kecamatan::orderBy('nama_kecamatan')->get(),
        ]);
    }

    public function update(Request $request, PendudukKecamatan $pendudukKecamatan)
    {
        $pendudukKecamatan->update($this->validated($request));

        return redirect()->route('admin.kependudukan.index')->with('success', 'Data penduduk kecamatan diperbarui.');
    }

    public function destroy(PendudukKecamatan $pendudukKecamatan)
    {
        $pendudukKecamatan->delete();

        return redirect()->route('admin.kependudukan.index')->with('success', 'Data penduduk kecamatan dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'kecamatan_id'    => ['required', 'exists:kecamatan,id'],
            'tahun'           => ['required', 'integer', 'min:1900', 'max:2100'],
            'jumlah_penduduk' => ['required', 'integer', 'min:0'],
        ]);
    }
}
