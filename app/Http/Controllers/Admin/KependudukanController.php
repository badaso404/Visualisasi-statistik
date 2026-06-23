<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataKependudukan;
use App\Models\PendudukKecamatan;
use App\Models\PendudukKelurahan;
use Illuminate\Http\Request;

class KependudukanController extends Controller
{
    public function index()
    {
        $items        = DataKependudukan::orderByDesc('tahun')->get();
        $perKecamatan = PendudukKecamatan::with('kecamatan')->orderByDesc('tahun')->orderByDesc('jumlah_penduduk')->get();
        $perKelurahan = PendudukKelurahan::with('kecamatan')->orderByDesc('tahun')->orderBy('nama_kelurahan')->get();

        return view('admin.kependudukan.index', compact('items', 'perKecamatan', 'perKelurahan'));
    }

    public function create()
    {
        return view('admin.kependudukan.form', ['item' => new DataKependudukan()]);
    }

    public function store(Request $request)
    {
        DataKependudukan::create($this->validated($request));

        return redirect()->route('admin.kependudukan.index')->with('success', 'Data kependudukan ditambahkan.');
    }

    public function edit(DataKependudukan $kependudukan)
    {
        return view('admin.kependudukan.form', ['item' => $kependudukan]);
    }

    public function update(Request $request, DataKependudukan $kependudukan)
    {
        $kependudukan->update($this->validated($request));

        return redirect()->route('admin.kependudukan.index')->with('success', 'Data kependudukan diperbarui.');
    }

    public function destroy(DataKependudukan $kependudukan)
    {
        $kependudukan->delete();

        return redirect()->route('admin.kependudukan.index')->with('success', 'Data kependudukan dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'tahun'            => ['required', 'integer', 'min:1900', 'max:2100'],
            'jumlah_laki_laki' => ['required', 'integer', 'min:0'],
            'jumlah_perempuan' => ['required', 'integer', 'min:0'],
            'jumlah_total'     => ['required', 'integer', 'min:0'],
            'sumber'           => ['nullable', 'string', 'max:255'],
        ]);
    }
}
