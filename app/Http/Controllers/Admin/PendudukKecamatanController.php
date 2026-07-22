<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\CsvPerKecamatan;
use App\Http\Controllers\Admin\Concerns\IsiMassalPerKecamatan;
use App\Http\Controllers\Admin\Concerns\TahunMengikutiInduk;
use App\Http\Controllers\Admin\Concerns\ValidasiPeriodeUnik;
use App\Http\Controllers\Controller;
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
            'jumlah_penduduk' => ['required', 'integer', 'min:0'],
        ], $this->pesanPeriodeUnik('kecamatan ini untuk tahun tersebut') + $this->pesanTahunInduk());
    }
}
