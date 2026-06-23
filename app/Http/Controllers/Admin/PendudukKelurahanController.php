<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\PendudukKelurahan;
use Illuminate\Http\Request;

class PendudukKelurahanController extends Controller
{
    public function create()
    {
        return view('admin.kependudukan.kelurahan-form', [
            'item'      => new PendudukKelurahan(),
            'kecamatan' => Kecamatan::orderBy('nama_kecamatan')->get(),
        ]);
    }

    public function store(Request $request)
    {
        PendudukKelurahan::create($this->validated($request));

        return redirect()->route('admin.kependudukan.index')->with('success', 'Data penduduk kelurahan ditambahkan.');
    }

    public function edit(PendudukKelurahan $pendudukKelurahan)
    {
        return view('admin.kependudukan.kelurahan-form', [
            'item'      => $pendudukKelurahan,
            'kecamatan' => Kecamatan::orderBy('nama_kecamatan')->get(),
        ]);
    }

    public function update(Request $request, PendudukKelurahan $pendudukKelurahan)
    {
        $pendudukKelurahan->update($this->validated($request));

        return redirect()->route('admin.kependudukan.index')->with('success', 'Data penduduk kelurahan diperbarui.');
    }

    public function destroy(PendudukKelurahan $pendudukKelurahan)
    {
        $pendudukKelurahan->delete();

        return redirect()->route('admin.kependudukan.index')->with('success', 'Data penduduk kelurahan dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'kecamatan_id'    => ['required', 'exists:kecamatan,id'],
            'tahun'           => ['required', 'integer', 'min:1900', 'max:2100'],
            'nama_kelurahan'  => ['required', 'string', 'max:255'],
            'latitude'        => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'       => ['nullable', 'numeric', 'between:-180,180'],
            'jumlah_penduduk' => ['required', 'integer', 'min:0'],
        ]);
    }
}
