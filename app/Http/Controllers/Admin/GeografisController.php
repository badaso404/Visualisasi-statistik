<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ValidasiPeriodeUnik;
use App\Http\Controllers\Controller;
use App\Models\DataGeografis;
use App\Models\Kecamatan;
use App\Models\LuasKecamatan;
use Illuminate\Http\Request;

class GeografisController extends Controller
{
    use ValidasiPeriodeUnik;

    public function index()
    {
        $items = DataGeografis::orderByDesc('tahun')->get();
        $luas  = LuasKecamatan::with('kecamatan', 'dataGeografis')->get();

        $kecamatan = Kecamatan::orderBy('nama_kecamatan')->get();

        return view('admin.geografis.index', compact('items', 'luas', 'kecamatan'));
    }

    public function store(Request $request)
    {
        DataGeografis::create($this->validated($request));

        return redirect()->route('admin.geografis.index')->with('success', 'Data geografis ditambahkan.');
    }

    public function update(Request $request, DataGeografis $geografi)
    {
        $geografi->update($this->validated($request, $geografi));

        return redirect()->route('admin.geografis.index')->with('success', 'Data geografis diperbarui.');
    }

    public function destroy(DataGeografis $geografi)
    {
        $geografi->delete();

        return redirect()->route('admin.geografis.index')->with('success', 'Data geografis dihapus.');
    }

    private function validated(Request $request, ?\Illuminate\Database\Eloquent\Model $item = null): array
    {
        return $request->validate([
            'tahun'           => ['required', 'integer', 'min:1900', 'max:2100',
                $this->unikPerPeriode('data_geografis', [], $item),
            ],
            'luas_kota_km2'   => ['required', 'numeric', 'min:0'],
            'ketinggian_mdpl' => ['required', 'integer'],
            'sumber'          => ['nullable', 'string', 'max:255'],
        ], $this->pesanPeriodeUnik('geografis untuk tahun ini'));
    }
}
