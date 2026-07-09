<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataKemiskinan;
use App\Models\KemiskinanKecamatan;
use Illuminate\Http\Request;

class KemiskinanController extends Controller
{
    public function index()
    {
        $items        = DataKemiskinan::orderByDesc('tahun')->get();
        $perKecamatan = KemiskinanKecamatan::with('kecamatan')
            ->orderByDesc('tahun')->orderByDesc('jumlah_penduduk_miskin')->get();

        return view('admin.kemiskinan.index', compact('items', 'perKecamatan'));
    }

    public function create()
    {
        return view('admin.kemiskinan.form', ['item' => new DataKemiskinan()]);
    }

    public function store(Request $request)
    {
        DataKemiskinan::create($this->validated($request));

        return redirect()->route('admin.kemiskinan.index')->with('success', 'Data kemiskinan ditambahkan.');
    }

    public function edit(DataKemiskinan $kemiskinan)
    {
        return view('admin.kemiskinan.form', ['item' => $kemiskinan]);
    }

    public function update(Request $request, DataKemiskinan $kemiskinan)
    {
        $kemiskinan->update($this->validated($request));

        return redirect()->route('admin.kemiskinan.index')->with('success', 'Data kemiskinan diperbarui.');
    }

    public function destroy(DataKemiskinan $kemiskinan)
    {
        $kemiskinan->delete();

        return redirect()->route('admin.kemiskinan.index')->with('success', 'Data kemiskinan dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'tahun'                      => ['required', 'integer', 'min:1900', 'max:2100'],
            'jumlah_penduduk_miskin'     => ['required', 'integer', 'min:0'],
            'persentase_penduduk_miskin' => ['required', 'numeric', 'min:0'],
            'garis_kemiskinan'           => ['required', 'numeric', 'min:0'],
            'indeks_kedalaman'           => ['required', 'numeric', 'min:0'],
            'indeks_keparahan'           => ['required', 'numeric', 'min:0'],
            'sumber'                     => ['nullable', 'string', 'max:255'],
        ]);
    }
}
