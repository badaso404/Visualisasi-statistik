<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ValidasiPeriodeUnik;
use App\Http\Controllers\Controller;
use App\Models\DataKependudukan;
use App\Models\Kecamatan;
use App\Models\PendudukKecamatan;
use App\Models\PendudukKelurahan;
use Illuminate\Http\Request;

class KependudukanController extends Controller
{
    use ValidasiPeriodeUnik;

    public function index()
    {
        $items        = DataKependudukan::orderByDesc('tahun')->get();
        $perKecamatan = PendudukKecamatan::with('kecamatan')->orderByDesc('tahun')->orderByDesc('jumlah_penduduk')->get();
        $perKelurahan = PendudukKelurahan::with('kecamatan')->orderByDesc('tahun')->orderBy('nama_kelurahan')->get();
        $kecamatan    = Kecamatan::orderBy('nama_kecamatan')->get();

        return view('admin.kependudukan.index', compact('items', 'perKecamatan', 'perKelurahan', 'kecamatan'));
    }

    public function store(Request $request)
    {
        DataKependudukan::create($this->validated($request));

        return redirect()->route('admin.kependudukan.index')->with('success', 'Data kependudukan ditambahkan.');
    }

    public function update(Request $request, DataKependudukan $kependudukan)
    {
        $kependudukan->update($this->validated($request, $kependudukan));

        return redirect()->route('admin.kependudukan.index')->with('success', 'Data kependudukan diperbarui.');
    }

    public function destroy(DataKependudukan $kependudukan)
    {
        $kependudukan->delete();

        return redirect()->route('admin.kependudukan.index')->with('success', 'Data kependudukan dihapus.');
    }

    private function validated(Request $request, ?\Illuminate\Database\Eloquent\Model $item = null): array
    {
        return $request->validate([
            'tahun'            => ['required', 'integer', 'min:1900', 'max:2100',
                $this->unikPerPeriode('data_kependudukan', [], $item),
            ],
            'jumlah_laki_laki' => ['required', 'integer', 'min:0'],
            'jumlah_perempuan' => ['required', 'integer', 'min:0'],
            'jumlah_total'     => ['required', 'integer', 'min:0'],
            'sumber'           => ['nullable', 'string', 'max:255'],
        ], $this->pesanPeriodeUnik('kependudukan untuk tahun ini'));
    }
}
