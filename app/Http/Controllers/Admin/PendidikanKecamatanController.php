<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\PendidikanKecamatan;
use Illuminate\Http\Request;

class PendidikanKecamatanController extends Controller
{
    public function create()
    {
        return view('admin.pendidikan.kecamatan-form', [
            'item'      => new PendidikanKecamatan(),
            'kecamatan' => Kecamatan::orderBy('nama_kecamatan')->get(),
        ]);
    }

    public function store(Request $request)
    {
        PendidikanKecamatan::create($this->validated($request));

        return redirect()->route('admin.pendidikan.index')->with('success', 'Data pendidikan kecamatan ditambahkan.');
    }

    public function edit(PendidikanKecamatan $pendidikanKecamatan)
    {
        return view('admin.pendidikan.kecamatan-form', [
            'item'      => $pendidikanKecamatan,
            'kecamatan' => Kecamatan::orderBy('nama_kecamatan')->get(),
        ]);
    }

    public function update(Request $request, PendidikanKecamatan $pendidikanKecamatan)
    {
        $pendidikanKecamatan->update($this->validated($request));

        return redirect()->route('admin.pendidikan.index')->with('success', 'Data pendidikan kecamatan diperbarui.');
    }

    public function destroy(PendidikanKecamatan $pendidikanKecamatan)
    {
        $pendidikanKecamatan->delete();

        return redirect()->route('admin.pendidikan.index')->with('success', 'Data pendidikan kecamatan dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'kecamatan_id'          => ['required', 'exists:kecamatan,id'],
            'tahun'                 => ['required', 'integer', 'min:1900', 'max:2100'],
            'jumlah_pelajar'        => ['required', 'integer', 'min:0'],
            'jumlah_pendidik'       => ['required', 'integer', 'min:0'],
            'jumlah_sekolah_negeri' => ['required', 'integer', 'min:0'],
            'jumlah_sekolah_swasta' => ['required', 'integer', 'min:0'],
        ]);
    }
}
