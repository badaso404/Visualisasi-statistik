<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ValidasiPeriodeUnik;
use App\Http\Controllers\Controller;
use App\Models\DataKesehatan;
use App\Models\FasilitasKesehatanKecamatan;
use App\Models\Kecamatan;
use App\Models\TenagaKesehatanKecamatan;
use Illuminate\Http\Request;

class KesehatanController extends Controller
{
    use ValidasiPeriodeUnik;

    public function index()
    {
        $items     = DataKesehatan::orderByDesc('tahun')->get();
        $tenaga    = TenagaKesehatanKecamatan::with('kecamatan')->orderByDesc('tahun')->get();
        $fasilitas = FasilitasKesehatanKecamatan::with('kecamatan')->orderByDesc('tahun')->get();

        $kecamatan = Kecamatan::orderBy('nama_kecamatan')->get();

        return view('admin.kesehatan.index', compact('items', 'tenaga', 'fasilitas', 'kecamatan'));
    }

    public function store(Request $request)
    {
        DataKesehatan::create($this->validated($request));

        return redirect()->route('admin.kesehatan.index')->with('success', 'Data kesehatan ditambahkan.');
    }

    public function update(Request $request, DataKesehatan $kesehatan)
    {
        $kesehatan->update($this->validated($request, $kesehatan));

        return redirect()->route('admin.kesehatan.index')->with('success', 'Data kesehatan diperbarui.');
    }

    public function destroy(DataKesehatan $kesehatan)
    {
        $kesehatan->delete();

        return redirect()->route('admin.kesehatan.index')->with('success', 'Data kesehatan dihapus.');
    }

    private function validated(Request $request, ?\Illuminate\Database\Eloquent\Model $item = null): array
    {
        return $request->validate([
            'tahun'                   => ['required', 'integer', 'min:1900', 'max:2100',
                $this->unikPerPeriode('data_kesehatan', [], $item),
            ],
            'jumlah_tempat_tidur_rs'  => ['required', 'integer', 'min:0'],
            'cakupan_imunisasi_dasar' => ['nullable', 'numeric', 'min:0'],
            'sumber'                  => ['nullable', 'string', 'max:255'],
        ], $this->pesanPeriodeUnik('kesehatan untuk tahun ini'));
    }
}
