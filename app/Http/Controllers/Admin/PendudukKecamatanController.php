<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\CsvPerKecamatan;
use App\Http\Controllers\Admin\Concerns\IsiMassalPerKecamatan;
use App\Http\Controllers\Admin\Concerns\TahunMengikutiInduk;
use App\Http\Controllers\Admin\Concerns\ValidasiPeriodeUnik;
use App\Http\Controllers\Controller;
use App\Models\DataKependudukan;
use App\Models\Kecamatan;
use App\Models\PendudukKecamatan;
use Illuminate\Http\Request;

class PendudukKecamatanController extends Controller
{
    use ValidasiPeriodeUnik;
    use TahunMengikutiInduk;

    use IsiMassalPerKecamatan;
    use CsvPerKecamatan;

    protected function tabelInduk(): ?string
    {
        return 'data_kependudukan';
    }

    protected function sebutanInduk(): string
    {
        return 'ringkasan kependudukan';
    }

    protected function csvNama(): string
    {
        return 'penduduk-kecamatan';
    }

    protected function batchModel(): string
    {
        return PendudukKecamatan::class;
    }

    protected function batchFields(): array
    {
        return ['jumlah_penduduk' => 'Jumlah Penduduk'];
    }

    protected function batchJudul(): string
    {
        return 'Isi Massal Penduduk per Kecamatan';
    }

    protected function batchRoutePrefix(): string
    {
        return 'admin.penduduk-kecamatan';
    }

    protected function batchRedirect(): string
    {
        return 'admin.kependudukan.index';
    }

    /**
     * Jumlah penduduk semua kecamatan tidak boleh melampaui total ringkasan
     * (induk) tahun tersebut. Total yang akan terbentuk dihitung dari nilai baru
     * bila diisi, selain itu memakai nilai lama yang sudah tersimpan.
     */
    protected function batchPeriksaTambahan(int $tahun, array $data): ?string
    {
        $total = DataKependudukan::where('tahun', $tahun)->value('jumlah_total');
        if ($total === null) {
            return null;
        }

        $lama  = PendudukKecamatan::where('tahun', $tahun)->pluck('jumlah_penduduk', 'kecamatan_id');
        $hasil = 0;
        foreach (Kecamatan::pluck('id') as $kid) {
            $baru = $data[$kid]['jumlah_penduduk'] ?? null;
            $hasil += ($baru !== null && $baru !== '') ? (int) $baru : (int) ($lama[$kid] ?? 0);
        }

        if ($hasil > $total) {
            return 'Total penduduk semua kecamatan (' . number_format($hasil, 0, ',', '.')
                . ') melebihi total ringkasan tahun ' . $tahun . ' (' . number_format($total, 0, ',', '.')
                . '). Perbaiki angkanya, atau sesuaikan ringkasan induk lebih dulu.';
        }

        return null;
    }

    public function store(Request $request)
    {
        PendudukKecamatan::create($this->validated($request));

        return redirect()->route('admin.kependudukan.index')->with('success', 'Data penduduk kecamatan ditambahkan.');
    }

    public function update(Request $request, PendudukKecamatan $pendudukKecamatan)
    {
        $pendudukKecamatan->update($this->validated($request, $pendudukKecamatan));

        return redirect()->route('admin.kependudukan.index')->with('success', 'Data penduduk kecamatan diperbarui.');
    }

    public function destroy(PendudukKecamatan $pendudukKecamatan)
    {
        $pendudukKecamatan->delete();

        return redirect()->route('admin.kependudukan.index')->with('success', 'Data penduduk kecamatan dihapus.');
    }

    private function validated(Request $request, ?\Illuminate\Database\Eloquent\Model $item = null): array
    {
        return $request->validate([
            'kecamatan_id'    => ['required', 'exists:kecamatan,id'],
            'tahun'           => array_merge($this->aturanTahunInduk(), [
                $this->unikPerPeriode('penduduk_kecamatan', ['kecamatan_id' => $request->input('kecamatan_id')], $item),
            ]),
            'jumlah_penduduk' => ['required', 'integer', 'min:0', 'max:2147483647',
                $this->tidakMelebihiTotalInduk($request, $item),
            ],
        ], $this->pesanPeriodeUnik('kecamatan ini untuk tahun tersebut') + $this->pesanTahunInduk() + [
            'jumlah_penduduk.max' => 'Nilai jumlah penduduk terlalu besar (maksimum 2.147.483.647).',
        ]);
    }

    /**
     * Closure: jumlah penduduk kecamatan-kecamatan lain pada tahun yang sama +
     * nilai baru ini tidak boleh melebihi total ringkasan (induk) tahun tersebut.
     */
    private function tidakMelebihiTotalInduk(Request $request, ?\Illuminate\Database\Eloquent\Model $item): \Closure
    {
        return function (string $attribute, $value, \Closure $fail) use ($request, $item) {
            $tahun = (int) $request->input('tahun');
            $total = DataKependudukan::where('tahun', $tahun)->value('jumlah_total');
            if ($total === null) {
                return;   // tanpa induk, aturan tahun-induk yang menolaknya lebih dulu
            }

            $lain = PendudukKecamatan::where('tahun', $tahun)
                ->when($item, fn ($q) => $q->whereKeyNot($item->getKey()))
                ->sum('jumlah_penduduk');

            if ($lain + (int) $value > $total) {
                $fail('Angka ini membuat total semua kecamatan (' . number_format($lain + (int) $value, 0, ',', '.')
                    . ') melebihi total ringkasan tahun ' . $tahun . ' (' . number_format($total, 0, ',', '.') . ').');
            }
        };
    }
}
