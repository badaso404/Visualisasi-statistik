<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\CsvPerPeriode;
use App\Http\Controllers\Admin\Concerns\ValidasiPeriodeUnik;
use App\Http\Controllers\Controller;
use App\Models\DataPerekonomian;
use App\Models\PdrbSektor;
use Illuminate\Http\Request;

class PerekonomianController extends Controller
{
    use CsvPerPeriode;
    use ValidasiPeriodeUnik;

    /* ── CSV: ringkasan tahunan, satu baris per tahun ─────────────────── */

    protected function csvNama(): string
    {
        return 'perekonomian';
    }

    protected function csvModel(): string
    {
        return DataPerekonomian::class;
    }

    protected function csvKunci(): array
    {
        return ['tahun'];
    }

    protected function csvKolom(): array
    {
        return [
            'pdrb_adhb'        => 'desimal',
            'pdrb_adhk'        => 'desimal',
            'laju_pertumbuhan' => 'desimal',
            'sumber'           => 'teks',
        ];
    }

    /** `sumber` boleh kosong (nullable), sisanya NOT NULL. */
    protected function csvKolomWajib(): array
    {
        return ['pdrb_adhb', 'pdrb_adhk', 'laju_pertumbuhan'];
    }

    protected function csvContoh(): array
    {
        return [
            'pdrb_adhb'        => 627869621.19,
            'pdrb_adhk'        => 383113079.03,
            'laju_pertumbuhan' => 5.27,
            'sumber'           => 'BPS Kota Jakarta Barat',
        ];
    }

    protected function csvRedirect(): string
    {
        return 'admin.perekonomian.index';
    }

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
