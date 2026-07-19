<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ValidasiPeriodeUnik;
use App\Http\Controllers\Controller;
use App\Models\DataPendidikan;
use App\Models\Kecamatan;
use App\Models\PendidikanKecamatan;
use Illuminate\Http\Request;

class PendidikanController extends Controller
{
    use ValidasiPeriodeUnik;

    public function index()
    {
        $items        = DataPendidikan::orderByDesc('tahun')->get();
        $perKecamatan = PendidikanKecamatan::with('kecamatan')->orderByDesc('tahun')->get();
        $kecamatan    = Kecamatan::orderBy('nama_kecamatan')->get();

        return view('admin.pendidikan.index', compact('items', 'perKecamatan', 'kecamatan'));
    }

    public function store(Request $request)
    {
        DataPendidikan::create($this->validated($request));

        return redirect()->route('admin.pendidikan.index')->with('success', 'Data pendidikan ditambahkan.');
    }

    public function update(Request $request, DataPendidikan $pendidikan)
    {
        $pendidikan->update($this->validated($request, $pendidikan));

        return redirect()->route('admin.pendidikan.index')->with('success', 'Data pendidikan diperbarui.');
    }

    public function destroy(DataPendidikan $pendidikan)
    {
        $pendidikan->delete();

        return redirect()->route('admin.pendidikan.index')->with('success', 'Data pendidikan dihapus.');
    }

    private function validated(Request $request, ?\Illuminate\Database\Eloquent\Model $item = null): array
    {
        return $request->validate([
            'tahun'           => ['required', 'integer', 'min:1900', 'max:2100',
                $this->unikPerPeriode('data_pendidikan', [], $item),
            ],
            'apm_sd_mi'       => ['required', 'numeric', 'min:0'],
            'apm_smp_mts'     => ['required', 'numeric', 'min:0'],
            'apm_sma_smk_man' => ['required', 'numeric', 'min:0'],
            'apk_sd_mi'       => ['required', 'numeric', 'min:0'],
            'apk_smp_mts'     => ['required', 'numeric', 'min:0'],
            'apk_sma_smk_man' => ['required', 'numeric', 'min:0'],
            'sumber'          => ['nullable', 'string', 'max:255'],
        ], $this->pesanPeriodeUnik('pendidikan untuk tahun ini'));
    }
}
