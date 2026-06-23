<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataGeografis;
use App\Models\Kecamatan;
use App\Models\LuasKecamatan;
use Illuminate\Http\Request;

class GeografisController extends Controller
{
    public function index()
    {
        $items = DataGeografis::orderByDesc('tahun')->get();
        $luas  = LuasKecamatan::with('kecamatan', 'dataGeografis')->get();

        return view('admin.geografis.index', compact('items', 'luas'));
    }

    public function create()
    {
        return view('admin.geografis.form', ['item' => new DataGeografis()]);
    }

    public function store(Request $request)
    {
        DataGeografis::create($this->validated($request));

        return redirect()->route('admin.geografis.index')->with('success', 'Data geografis ditambahkan.');
    }

    public function edit(DataGeografis $geografi)
    {
        return view('admin.geografis.form', ['item' => $geografi]);
    }

    public function update(Request $request, DataGeografis $geografi)
    {
        $geografi->update($this->validated($request));

        return redirect()->route('admin.geografis.index')->with('success', 'Data geografis diperbarui.');
    }

    public function destroy(DataGeografis $geografi)
    {
        $geografi->delete();

        return redirect()->route('admin.geografis.index')->with('success', 'Data geografis dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'tahun'           => ['required', 'integer', 'min:1900', 'max:2100'],
            'luas_kota_km2'   => ['required', 'numeric', 'min:0'],
            'ketinggian_mdpl' => ['required', 'integer'],
            'sumber'          => ['nullable', 'string', 'max:255'],
        ]);
    }
}
