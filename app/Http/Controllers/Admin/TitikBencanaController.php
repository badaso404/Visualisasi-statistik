<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TitikBencana;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TitikBencanaController extends Controller
{
    public function index()
    {
        $items = TitikBencana::with('kecamatan')
            ->orderBy('kategori')
            ->orderBy('level')
            ->orderBy('nama')
            ->get();

        return view('admin.titik-bencana.index', compact('items'));
    }

    public function create()
    {
        return view('admin.titik-bencana.form', [
            'item'         => new TitikBencana(),
            'kecamatan'    => Kecamatan::orderBy('nama_kecamatan')->get(),
            'kategoriList' => TitikBencana::KATEGORI,
        ]);
    }

    public function store(Request $request)
    {
        TitikBencana::create($this->validated($request));

        return redirect()->route('admin.titik-bencana.index')->with('success', 'Titik bencana ditambahkan.');
    }

    public function edit(TitikBencana $titikBencana)
    {
        return view('admin.titik-bencana.form', [
            'item'         => $titikBencana,
            'kecamatan'    => Kecamatan::orderBy('nama_kecamatan')->get(),
            'kategoriList' => TitikBencana::KATEGORI,
        ]);
    }

    public function update(Request $request, TitikBencana $titikBencana)
    {
        $titikBencana->update($this->validated($request));

        return redirect()->route('admin.titik-bencana.index')->with('success', 'Titik bencana diperbarui.');
    }

    public function destroy(TitikBencana $titikBencana)
    {
        $titikBencana->delete();

        return redirect()->route('admin.titik-bencana.index')->with('success', 'Titik bencana dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'kecamatan_id' => ['nullable', 'exists:kecamatan,id'],
            'kategori'     => ['required', Rule::in(array_keys(TitikBencana::KATEGORI))],
            'level'        => ['nullable', 'integer', 'between:1,3'],
            'nama'         => ['required', 'string', 'max:255'],
            'latitude'     => ['required', 'numeric', 'between:-90,90'],
            'longitude'    => ['required', 'numeric', 'between:-180,180'],
            'link_maps'    => ['nullable', 'url', 'max:1000'],
            'keterangan'   => ['nullable', 'string'],
        ]);

        if ($data['kategori'] === 'banjir_rawan') {
            // Banjir tidak butuh link Maps (hanya info lokasi rawan)
            $data['link_maps'] = null;
        } else {
            // Level hanya relevan untuk zona rawan banjir
            $data['level'] = null;
        }

        return $data;
    }
}
