<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use Illuminate\Http\Request;

class KecamatanController extends Controller
{
    public function index()
    {
        $items = Kecamatan::orderBy('nama_kecamatan')->get();

        return view('admin.kecamatan.index', compact('items'));
    }

    public function create()
    {
        return view('admin.kecamatan.form', ['item' => new Kecamatan()]);
    }

    public function store(Request $request)
    {
        Kecamatan::create($this->validated($request));

        return redirect()->route('admin.kecamatan.index')->with('success', 'Kecamatan ditambahkan.');
    }

    public function edit(Kecamatan $kecamatan)
    {
        return view('admin.kecamatan.form', ['item' => $kecamatan]);
    }

    public function update(Request $request, Kecamatan $kecamatan)
    {
        $kecamatan->update($this->validated($request));

        return redirect()->route('admin.kecamatan.index')->with('success', 'Kecamatan diperbarui.');
    }

    public function destroy(Kecamatan $kecamatan)
    {
        $kecamatan->delete();

        return redirect()->route('admin.kecamatan.index')->with('success', 'Kecamatan dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nama_kecamatan' => ['required', 'string', 'max:255'],
        ]);
    }
}
