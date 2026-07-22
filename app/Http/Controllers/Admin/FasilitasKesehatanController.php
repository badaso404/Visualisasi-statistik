<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\CsvPerKecamatan;
use App\Http\Controllers\Admin\Concerns\IsiMassalPerKecamatan;
use App\Http\Controllers\Admin\Concerns\TahunMengikutiInduk;
use App\Http\Controllers\Admin\Concerns\ValidasiPeriodeUnik;
use App\Http\Controllers\Controller;
use App\Models\FasilitasKesehatanKecamatan;
use Illuminate\Http\Request;

class FasilitasKesehatanController extends Controller
{
    use ValidasiPeriodeUnik;
    use TahunMengikutiInduk;

    use IsiMassalPerKecamatan;
    use CsvPerKecamatan;

    protected function tabelInduk(): ?string
    {
        return 'data_kesehatan';
    }

    protected function sebutanInduk(): string
    {
        return 'ringkasan kesehatan';
    }

    protected function csvNama(): string
    {
        return 'fasilitas-kesehatan';
    }

    protected function batchModel(): string
    {
        return FasilitasKesehatanKecamatan::class;
    }

    protected function batchFields(): array
    {
        return [
            'jumlah_total'     => 'Total',
            'klinik_kesehatan' => 'Klinik',
            'posyandu'         => 'Posyandu',
            'puskesmas'        => 'Puskesmas',
            'rumah_sakit'      => 'Rumah Sakit',
        ];
    }

    protected function batchJudul(): string
    {
        return 'Isi Massal Fasilitas Kesehatan per Kecamatan';
    }

    protected function batchRoutePrefix(): string
    {
        return 'admin.fasilitas-kesehatan';
    }

    protected function batchRedirect(): string
    {
        return 'admin.kesehatan.index';
    }

    public function store(Request $request)
    {
        FasilitasKesehatanKecamatan::create($this->validated($request));

        return redirect()->route('admin.kesehatan.index')->with('success', 'Data fasilitas kesehatan ditambahkan.');
    }

    public function update(Request $request, FasilitasKesehatanKecamatan $fasilitasKesehatan)
    {
        $fasilitasKesehatan->update($this->validated($request, $fasilitasKesehatan));

        return redirect()->route('admin.kesehatan.index')->with('success', 'Data fasilitas kesehatan diperbarui.');
    }

    public function destroy(FasilitasKesehatanKecamatan $fasilitasKesehatan)
    {
        $fasilitasKesehatan->delete();

        return redirect()->route('admin.kesehatan.index')->with('success', 'Data fasilitas kesehatan dihapus.');
    }

    private function validated(Request $request, ?\Illuminate\Database\Eloquent\Model $item = null): array
    {
        return $request->validate([
            'kecamatan_id'     => ['required', 'exists:kecamatan,id'],
            'tahun'            => array_merge($this->aturanTahunInduk(), [
                $this->unikPerPeriode('fasilitas_kesehatan_kecamatan', ['kecamatan_id' => $request->input('kecamatan_id')], $item),
            ]),
            'jumlah_total'     => ['required', 'integer', 'min:0'],
            'klinik_kesehatan' => ['required', 'integer', 'min:0'],
            'posyandu'         => ['required', 'integer', 'min:0'],
            'puskesmas'        => ['required', 'integer', 'min:0'],
            'rumah_sakit'      => ['required', 'integer', 'min:0'],
        ], $this->pesanPeriodeUnik('kecamatan ini untuk tahun tersebut') + $this->pesanTahunInduk());
    }
}
