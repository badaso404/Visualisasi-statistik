<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\TenagaKesehatanKecamatan;
use Illuminate\Http\Request;

class TenagaKesehatanController extends Controller
{
    public function create()
    {
        return view('admin.kesehatan.tenaga-form', [
            'item'      => new TenagaKesehatanKecamatan(),
            'kecamatan' => Kecamatan::orderBy('nama_kecamatan')->get(),
        ]);
    }

    public function store(Request $request)
    {
        TenagaKesehatanKecamatan::create($this->validated($request));

        return redirect()->route('admin.kesehatan.index')->with('success', 'Data tenaga kesehatan ditambahkan.');
    }

    public function edit(TenagaKesehatanKecamatan $tenagaKesehatan)
    {
        return view('admin.kesehatan.tenaga-form', [
            'item'      => $tenagaKesehatan,
            'kecamatan' => Kecamatan::orderBy('nama_kecamatan')->get(),
        ]);
    }

    public function update(Request $request, TenagaKesehatanKecamatan $tenagaKesehatan)
    {
        $tenagaKesehatan->update($this->validated($request));

        return redirect()->route('admin.kesehatan.index')->with('success', 'Data tenaga kesehatan diperbarui.');
    }

    public function destroy(TenagaKesehatanKecamatan $tenagaKesehatan)
    {
        $tenagaKesehatan->delete();

        return redirect()->route('admin.kesehatan.index')->with('success', 'Data tenaga kesehatan dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'kecamatan_id' => ['required', 'exists:kecamatan,id'],
            'tahun'        => ['required', 'integer', 'min:1900', 'max:2100'],
            'jumlah_total' => ['required', 'integer', 'min:0'],
            'dokter'       => ['required', 'integer', 'min:0'],
            'perawat'      => ['required', 'integer', 'min:0'],
            'bidan'        => ['required', 'integer', 'min:0'],
            'ahli_gizi'    => ['required', 'integer', 'min:0'],
            'farmasi'      => ['required', 'integer', 'min:0'],
        ]);
    }
}
