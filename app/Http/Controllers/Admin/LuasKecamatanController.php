<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataGeografis;
use App\Models\Kecamatan;
use App\Models\LuasKecamatan;
use Illuminate\Http\Request;

class LuasKecamatanController extends Controller
{
    public function create()
    {
        return view('admin.luas-kecamatan.form', [
            'item'       => new LuasKecamatan(),
            'kecamatan'  => Kecamatan::orderBy('nama_kecamatan')->get(),
            'geografis'  => DataGeografis::orderByDesc('tahun')->get(),
        ]);
    }

    public function store(Request $request)
    {
        LuasKecamatan::create($this->validated($request));

        return redirect()->route('admin.geografis.index')->with('success', 'Luas kecamatan ditambahkan.');
    }

    public function edit(LuasKecamatan $luasKecamatan)
    {
        return view('admin.luas-kecamatan.form', [
            'item'       => $luasKecamatan,
            'kecamatan'  => Kecamatan::orderBy('nama_kecamatan')->get(),
            'geografis'  => DataGeografis::orderByDesc('tahun')->get(),
        ]);
    }

    public function update(Request $request, LuasKecamatan $luasKecamatan)
    {
        $luasKecamatan->update($this->validated($request));

        return redirect()->route('admin.geografis.index')->with('success', 'Luas kecamatan diperbarui.');
    }

    public function destroy(LuasKecamatan $luasKecamatan)
    {
        $luasKecamatan->delete();

        return redirect()->route('admin.geografis.index')->with('success', 'Luas kecamatan dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'kecamatan_id'      => ['required', 'exists:kecamatan,id'],
            'data_geografis_id' => ['required', 'exists:data_geografis,id'],
            'luas_km2'          => ['required', 'numeric', 'min:0'],
            'persentase'        => ['required', 'numeric', 'min:0', 'max:100'],
        ]);
    }
}
