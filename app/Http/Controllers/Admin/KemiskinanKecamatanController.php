<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\KemiskinanKecamatan;
use Illuminate\Http\Request;

class KemiskinanKecamatanController extends Controller
{
    public function create()
    {
        return view('admin.kemiskinan.kecamatan-form', [
            'item'      => new KemiskinanKecamatan(),
            'kecamatan' => Kecamatan::orderBy('nama_kecamatan')->get(),
        ]);
    }

    public function store(Request $request)
    {
        KemiskinanKecamatan::create($this->validated($request));

        return redirect()->route('admin.kemiskinan.index')->with('success', 'Data kemiskinan kecamatan ditambahkan.');
    }

    public function edit(KemiskinanKecamatan $kemiskinanKecamatan)
    {
        return view('admin.kemiskinan.kecamatan-form', [
            'item'      => $kemiskinanKecamatan,
            'kecamatan' => Kecamatan::orderBy('nama_kecamatan')->get(),
        ]);
    }

    public function update(Request $request, KemiskinanKecamatan $kemiskinanKecamatan)
    {
        $kemiskinanKecamatan->update($this->validated($request));

        return redirect()->route('admin.kemiskinan.index')->with('success', 'Data kemiskinan kecamatan diperbarui.');
    }

    public function destroy(KemiskinanKecamatan $kemiskinanKecamatan)
    {
        $kemiskinanKecamatan->delete();

        return redirect()->route('admin.kemiskinan.index')->with('success', 'Data kemiskinan kecamatan dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'kecamatan_id'           => ['required', 'exists:kecamatan,id'],
            'tahun'                  => ['required', 'integer', 'min:1900', 'max:2100'],
            'jumlah_penduduk_miskin' => ['required', 'integer', 'min:0'],
            'jumlah_keluarga_miskin' => ['required', 'integer', 'min:0'],
            'penerima_bantuan'       => ['required', 'integer', 'min:0'],
            'persentase'             => ['required', 'numeric', 'min:0'],
        ]);
    }
}
