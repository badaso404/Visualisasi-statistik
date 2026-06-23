<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FasilitasKesehatanKecamatan;
use App\Models\Kecamatan;
use Illuminate\Http\Request;

class FasilitasKesehatanController extends Controller
{
    public function create()
    {
        return view('admin.kesehatan.fasilitas-form', [
            'item'      => new FasilitasKesehatanKecamatan(),
            'kecamatan' => Kecamatan::orderBy('nama_kecamatan')->get(),
        ]);
    }

    public function store(Request $request)
    {
        FasilitasKesehatanKecamatan::create($this->validated($request));

        return redirect()->route('admin.kesehatan.index')->with('success', 'Data fasilitas kesehatan ditambahkan.');
    }

    public function edit(FasilitasKesehatanKecamatan $fasilitasKesehatan)
    {
        return view('admin.kesehatan.fasilitas-form', [
            'item'      => $fasilitasKesehatan,
            'kecamatan' => Kecamatan::orderBy('nama_kecamatan')->get(),
        ]);
    }

    public function update(Request $request, FasilitasKesehatanKecamatan $fasilitasKesehatan)
    {
        $fasilitasKesehatan->update($this->validated($request));

        return redirect()->route('admin.kesehatan.index')->with('success', 'Data fasilitas kesehatan diperbarui.');
    }

    public function destroy(FasilitasKesehatanKecamatan $fasilitasKesehatan)
    {
        $fasilitasKesehatan->delete();

        return redirect()->route('admin.kesehatan.index')->with('success', 'Data fasilitas kesehatan dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'kecamatan_id'     => ['required', 'exists:kecamatan,id'],
            'tahun'            => ['required', 'integer', 'min:1900', 'max:2100'],
            'jumlah_total'     => ['required', 'integer', 'min:0'],
            'klinik_kesehatan' => ['required', 'integer', 'min:0'],
            'posyandu'         => ['required', 'integer', 'min:0'],
            'puskesmas'        => ['required', 'integer', 'min:0'],
            'rumah_sakit'      => ['required', 'integer', 'min:0'],
        ]);
    }
}
