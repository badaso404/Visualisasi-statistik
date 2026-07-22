<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ValidasiPeriodeUnik;
use App\Http\Controllers\Controller;
use App\Models\DataPerekonomian;
use App\Models\PdrbSektor;
use Illuminate\Http\Request;

/**
 * Ringkasan PDRB tingkat kota — satu baris per tahun.
 *
 * Sengaja TANPA export/import CSV: satu tahun hanya berisi tiga angka, jadi
 * mengunduh template lalu mengunggah berkas justru lebih lama daripada mengetik
 * langsung di modal. Rincian per lapangan usaha (17 baris per tahun) yang butuh
 * CSV, dan itu ditangani PdrbSektorController.
 */
class PerekonomianController extends Controller
{
    use ValidasiPeriodeUnik;

    public function index()
    {
        $items = DataPerekonomian::orderByDesc('tahun')->get();

        // Dikelompokkan per tahun supaya tabel 17-baris-per-tahun bisa dilipat;
        // tanpa itu satu halaman memuat seluruh tahun sekaligus.
        $sektorPerTahun = PdrbSektor::orderByDesc('tahun')
            ->orderByDesc('distribusi')
            ->get()
            ->groupBy('tahun');

        // Portal menampilkan semua tahun, situs publik hanya beberapa terakhir —
        // ambang ini dipakai untuk menandai baris mana yang dilihat pengunjung.
        $batasPublik = DataPerekonomian::batasTahunPublik();

        return view('admin.perekonomian.index', compact('items', 'sektorPerTahun', 'batasPublik'));
    }

    public function store(Request $request)
    {
        DataPerekonomian::create($this->validated($request));

        return redirect()->route('admin.perekonomian.index')->with('success', 'Data perekonomian ditambahkan.');
    }

    public function update(Request $request, DataPerekonomian $perekonomian)
    {
        $perekonomian->update($this->validated($request, $perekonomian));

        return redirect()->route('admin.perekonomian.index')->with('success', 'Data perekonomian diperbarui.');
    }

    public function destroy(DataPerekonomian $perekonomian)
    {
        $perekonomian->delete();

        return redirect()->route('admin.perekonomian.index')->with('success', 'Data perekonomian dihapus.');
    }

    private function validated(Request $request, ?\Illuminate\Database\Eloquent\Model $item = null): array
    {
        return $request->validate([
            'tahun'            => ['required', 'integer', 'min:1900', 'max:2100',
                $this->unikPerPeriode('data_perekonomian', [], $item),
            ],
            'pdrb_adhb'        => ['required', 'numeric', 'min:0'],
            'pdrb_adhk'        => ['required', 'numeric', 'min:0'],
            // Pertumbuhan boleh negatif (mis. 2020 = -0,86%), jadi tanpa min:0.
            'laju_pertumbuhan' => ['required', 'numeric', 'between:-100,100'],
            'sumber'           => ['nullable', 'string', 'max:255'],
        ], $this->pesanPeriodeUnik('perekonomian untuk tahun ini'));
    }
}
