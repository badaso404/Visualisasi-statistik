<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\CsvPerKecamatan;
use App\Http\Controllers\Admin\Concerns\IsiMassalPerKecamatan;
use App\Http\Controllers\Admin\Concerns\TahunMengikutiInduk;
use App\Http\Controllers\Admin\Concerns\ValidasiPeriodeUnik;
use App\Http\Controllers\Controller;
use App\Models\KemiskinanKecamatan;
use Illuminate\Http\Request;

class KemiskinanKecamatanController extends Controller
{
    use ValidasiPeriodeUnik;
    use TahunMengikutiInduk;

    use IsiMassalPerKecamatan;
    use CsvPerKecamatan;

    protected function tabelInduk(): ?string
    {
        return 'data_kemiskinan';
    }

    protected function sebutanInduk(): string
    {
        return 'ringkasan kemiskinan';
    }

    protected function csvNama(): string
    {
        return 'kemiskinan-kecamatan';
    }

    protected function batchModel(): string
    {
        return KemiskinanKecamatan::class;
    }

    protected function batchFields(): array
    {
        return [
            'jumlah_penduduk_miskin' => 'Penduduk Miskin (jiwa)',
            'jumlah_keluarga_miskin' => 'Keluarga Miskin (KK)',
            'penerima_bantuan'       => 'Penerima Bantuan',
            'persentase'             => ['label' => 'Persentase (%)', 'desimal' => true],
        ];
    }

    protected function batchJudul(): string
    {
        return 'Isi Massal Kemiskinan per Kecamatan';
    }

    protected function batchRoutePrefix(): string
    {
        return 'admin.kemiskinan-kecamatan';
    }

    protected function batchRedirect(): string
    {
        return 'admin.kemiskinan.index';
    }

    public function store(Request $request)
    {
        KemiskinanKecamatan::create($this->validated($request));

        return redirect()->route('admin.kemiskinan.index')->with('success', 'Data kemiskinan kecamatan ditambahkan.');
    }

    public function update(Request $request, KemiskinanKecamatan $kemiskinanKecamatan)
    {
        $kemiskinanKecamatan->update($this->validated($request, $kemiskinanKecamatan));

        return redirect()->route('admin.kemiskinan.index')->with('success', 'Data kemiskinan kecamatan diperbarui.');
    }

    public function destroy(KemiskinanKecamatan $kemiskinanKecamatan)
    {
        $kemiskinanKecamatan->delete();

        return redirect()->route('admin.kemiskinan.index')->with('success', 'Data kemiskinan kecamatan dihapus.');
    }

    private function validated(Request $request, ?\Illuminate\Database\Eloquent\Model $item = null): array
    {
        return $request->validate([
            'kecamatan_id'           => ['required', 'exists:kecamatan,id'],
            'tahun'                  => array_merge($this->aturanTahunInduk(), [
                $this->unikPerPeriode('kemiskinan_kecamatan', ['kecamatan_id' => $request->input('kecamatan_id')], $item),
            ]),
            'jumlah_penduduk_miskin' => ['required', 'integer', 'min:0'],
            'jumlah_keluarga_miskin' => ['required', 'integer', 'min:0'],
            'penerima_bantuan'       => ['required', 'integer', 'min:0'],
            'persentase'             => ['required', 'numeric', 'min:0'],
        ], $this->pesanPeriodeUnik('kecamatan ini untuk tahun tersebut') + $this->pesanTahunInduk());
    }
}
