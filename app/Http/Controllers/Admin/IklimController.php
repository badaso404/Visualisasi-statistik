<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\CsvPerPeriode;
use App\Http\Controllers\Admin\Concerns\ValidasiPeriodeUnik;
use App\Http\Controllers\Controller;
use App\Models\DataIklim;
use Illuminate\Http\Request;

/**
 * Data iklim bulanan BMKG.
 *
 * Modul dengan beban entri manual paling berat di portal: 12 baris x 6 besaran
 * setiap tahun. Karena itu CSV di sini bukan pelengkap melainkan jalur utama —
 * rekap BMKG biasanya memang sudah berbentuk tabel bulanan di Excel.
 */
class IklimController extends Controller
{
    use CsvPerPeriode;
    use ValidasiPeriodeUnik;

    /* ── CSV: satu baris per (tahun, bulan) ───────────────────────────── */

    protected function csvNama(): string
    {
        return 'iklim';
    }

    protected function csvModel(): string
    {
        return DataIklim::class;
    }

    protected function csvKunci(): array
    {
        return ['tahun', 'bulan'];
    }

    protected function csvKolom(): array
    {
        return [
            'hari_hujan'          => 'desimal',
            'tekanan_udara'       => 'desimal',
            'suhu_udara'          => 'desimal',
            'kecepatan_angin'     => 'desimal',
            'kelembaban_udara'    => 'desimal',
            'penyinaran_matahari' => 'desimal',
            'sumber'              => 'teks',
        ];
    }

    protected function csvKunciValid(array $kunci): ?string
    {
        return ($kunci['bulan'] < 1 || $kunci['bulan'] > 12)
            ? "bulan '{$kunci['bulan']}' di luar 1-12"
            : null;
    }

    /** `sumber` nullable; enam besaran iklim lainnya NOT NULL. */
    protected function csvKolomWajib(): array
    {
        return [
            'hari_hujan', 'tekanan_udara', 'suhu_udara',
            'kecepatan_angin', 'kelembaban_udara', 'penyinaran_matahari',
        ];
    }

    protected function csvContoh(): array
    {
        return [
            'bulan'               => 1,
            'hari_hujan'          => 18,
            'tekanan_udara'       => 1009.20,
            'suhu_udara'          => 27.60,
            'kecepatan_angin'     => 2.40,
            'kelembaban_udara'    => 81.00,
            'penyinaran_matahari' => 45.00,
            'sumber'              => 'BMKG Stasiun Kemayoran',
        ];
    }

    protected function csvRedirect(): string
    {
        return 'admin.iklim.index';
    }

    public function index()
    {
        $items = DataIklim::orderByDesc('tahun')->orderBy('bulan')->get();

        return view('admin.iklim.index', compact('items'));
    }

    public function store(Request $request)
    {
        DataIklim::create($this->validated($request));

        return redirect()->route('admin.iklim.index')->with('success', 'Data iklim ditambahkan.');
    }

    public function update(Request $request, DataIklim $iklim)
    {
        $iklim->update($this->validated($request, $iklim));

        return redirect()->route('admin.iklim.index')->with('success', 'Data iklim diperbarui.');
    }

    public function destroy(DataIklim $iklim)
    {
        $iklim->delete();

        return redirect()->route('admin.iklim.index')->with('success', 'Data iklim dihapus.');
    }

    private function validated(Request $request, ?\Illuminate\Database\Eloquent\Model $item = null): array
    {
        return $request->validate([
            'tahun'               => ['required', 'integer', 'min:1900', 'max:2100',
                $this->unikPerPeriode('data_iklim', ['bulan' => $request->input('bulan')], $item),
            ],
            'bulan'               => ['required', 'integer', 'min:1', 'max:12'],
            'hari_hujan'          => ['required', 'numeric', 'min:0'],
            'tekanan_udara'       => ['required', 'numeric', 'min:0'],
            'suhu_udara'          => ['required', 'numeric'],
            'kecepatan_angin'     => ['required', 'numeric', 'min:0'],
            'kelembaban_udara'    => ['required', 'numeric', 'min:0'],
            'penyinaran_matahari' => ['required', 'numeric', 'min:0'],
            'sumber'              => ['nullable', 'string', 'max:255'],
        ], $this->pesanPeriodeUnik('iklim untuk bulan & tahun ini'));
    }
}
