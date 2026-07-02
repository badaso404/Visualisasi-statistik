<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CctvKecamatan;
use App\Models\Kecamatan;
use Illuminate\Http\Request;

class CctvController extends Controller
{
    public function create()
    {
        return view('admin.infrastruktur-digital.cctv-form', [
            'item'      => new CctvKecamatan(),
            'kecamatan' => Kecamatan::orderBy('nama_kecamatan')->get(),
        ]);
    }

    public function store(Request $request)
    {
        CctvKecamatan::create($this->validated($request));

        return redirect()->route('admin.infrastruktur-digital.index')->with('success', 'Data CCTV ditambahkan.');
    }

    public function edit(CctvKecamatan $cctv)
    {
        return view('admin.infrastruktur-digital.cctv-form', [
            'item'      => $cctv,
            'kecamatan' => Kecamatan::orderBy('nama_kecamatan')->get(),
        ]);
    }

    public function update(Request $request, CctvKecamatan $cctv)
    {
        $cctv->update($this->validated($request));

        return redirect()->route('admin.infrastruktur-digital.index')->with('success', 'Data CCTV diperbarui.');
    }

    public function destroy(CctvKecamatan $cctv)
    {
        $cctv->delete();

        return redirect()->route('admin.infrastruktur-digital.index')->with('success', 'Data CCTV dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'kecamatan_id' => ['required', 'exists:kecamatan,id'],
            'tahun'        => ['required', 'integer', 'min:1900', 'max:2100'],
            'jumlah_unit'  => ['required', 'integer', 'min:0'],
            'unit_aktif'   => ['required', 'integer', 'min:0'],
            'terintegrasi' => ['required', 'integer', 'min:0'],
            'keterangan'   => ['nullable', 'string', 'max:255'],
        ]);
    }
}
