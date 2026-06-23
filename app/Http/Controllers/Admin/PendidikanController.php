<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataPendidikan;
use App\Models\PendidikanKecamatan;
use Illuminate\Http\Request;

class PendidikanController extends Controller
{
    public function index()
    {
        $items        = DataPendidikan::orderByDesc('tahun')->get();
        $perKecamatan = PendidikanKecamatan::with('kecamatan')->orderByDesc('tahun')->get();

        return view('admin.pendidikan.index', compact('items', 'perKecamatan'));
    }

    public function create()
    {
        return view('admin.pendidikan.form', ['item' => new DataPendidikan()]);
    }

    public function store(Request $request)
    {
        DataPendidikan::create($this->validated($request));

        return redirect()->route('admin.pendidikan.index')->with('success', 'Data pendidikan ditambahkan.');
    }

    public function edit(DataPendidikan $pendidikan)
    {
        return view('admin.pendidikan.form', ['item' => $pendidikan]);
    }

    public function update(Request $request, DataPendidikan $pendidikan)
    {
        $pendidikan->update($this->validated($request));

        return redirect()->route('admin.pendidikan.index')->with('success', 'Data pendidikan diperbarui.');
    }

    public function destroy(DataPendidikan $pendidikan)
    {
        $pendidikan->delete();

        return redirect()->route('admin.pendidikan.index')->with('success', 'Data pendidikan dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'tahun'           => ['required', 'integer', 'min:1900', 'max:2100'],
            'apm_sd_mi'       => ['required', 'numeric', 'min:0'],
            'apm_smp_mts'     => ['required', 'numeric', 'min:0'],
            'apm_sma_smk_man' => ['required', 'numeric', 'min:0'],
            'apk_sd_mi'       => ['required', 'numeric', 'min:0'],
            'apk_smp_mts'     => ['required', 'numeric', 'min:0'],
            'apk_sma_smk_man' => ['required', 'numeric', 'min:0'],
            'sumber'          => ['nullable', 'string', 'max:255'],
        ]);
    }
}
