<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataIklim;
use Illuminate\Http\Request;

class IklimController extends Controller
{
    public function index()
    {
        $items = DataIklim::orderByDesc('tahun')->orderBy('bulan')->get();

        return view('admin.iklim.index', compact('items'));
    }

    public function create()
    {
        return view('admin.iklim.form', ['item' => new DataIklim()]);
    }

    public function store(Request $request)
    {
        DataIklim::create($this->validated($request));

        return redirect()->route('admin.iklim.index')->with('success', 'Data iklim ditambahkan.');
    }

    public function edit(DataIklim $iklim)
    {
        return view('admin.iklim.form', ['item' => $iklim]);
    }

    public function update(Request $request, DataIklim $iklim)
    {
        $iklim->update($this->validated($request));

        return redirect()->route('admin.iklim.index')->with('success', 'Data iklim diperbarui.');
    }

    public function destroy(DataIklim $iklim)
    {
        $iklim->delete();

        return redirect()->route('admin.iklim.index')->with('success', 'Data iklim dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'tahun'               => ['required', 'integer', 'min:1900', 'max:2100'],
            'bulan'               => ['required', 'integer', 'min:1', 'max:12'],
            'hari_hujan'          => ['required', 'numeric', 'min:0'],
            'tekanan_udara'       => ['required', 'numeric', 'min:0'],
            'suhu_udara'          => ['required', 'numeric'],
            'kecepatan_angin'     => ['required', 'numeric', 'min:0'],
            'kelembaban_udara'    => ['required', 'numeric', 'min:0'],
            'penyinaran_matahari' => ['required', 'numeric', 'min:0'],
            'sumber'              => ['nullable', 'string', 'max:255'],
        ]);
    }
}
