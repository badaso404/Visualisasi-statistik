<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ValidasiPeriodeUnik;
use App\Http\Controllers\Controller;
use App\Models\LuasKecamatan;
use Illuminate\Http\Request;

class LuasKecamatanController extends Controller
{
    use ValidasiPeriodeUnik;

    public function store(Request $request)
    {
        LuasKecamatan::create($this->validated($request));

        return redirect()->route('admin.geografis.index')->with('success', 'Luas kecamatan ditambahkan.');
    }

    public function update(Request $request, LuasKecamatan $luasKecamatan)
    {
        $luasKecamatan->update($this->validated($request, $luasKecamatan));

        return redirect()->route('admin.geografis.index')->with('success', 'Luas kecamatan diperbarui.');
    }

    public function destroy(LuasKecamatan $luasKecamatan)
    {
        $luasKecamatan->delete();

        return redirect()->route('admin.geografis.index')->with('success', 'Luas kecamatan dihapus.');
    }

    private function validated(Request $request, ?\Illuminate\Database\Eloquent\Model $item = null): array
    {
        return $request->validate([
            'kecamatan_id'      => ['required', 'exists:kecamatan,id'],
            'data_geografis_id' => ['required', 'exists:data_geografis,id',
                $this->unikPerPeriode('luas_kecamatan', ['kecamatan_id' => $request->input('kecamatan_id')], $item),
            ],
            'luas_km2'          => ['required', 'numeric', 'min:0'],
            'persentase'        => ['required', 'numeric', 'min:0', 'max:100'],
        ], $this->pesanPeriodeUnik('kecamatan ini untuk tahun geografis tersebut'));
    }
}
