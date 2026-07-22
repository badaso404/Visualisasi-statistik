<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\CsvPerKecamatan;
use App\Http\Controllers\Admin\Concerns\IsiMassalPerKecamatan;
use App\Http\Controllers\Admin\Concerns\TahunMengikutiInduk;
use App\Http\Controllers\Admin\Concerns\ValidasiPeriodeUnik;
use App\Http\Controllers\Controller;
use App\Models\TenagaKesehatanKecamatan;
use Illuminate\Http\Request;

class TenagaKesehatanController extends Controller
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
        return 'tenaga-kesehatan';
    }

    protected function batchModel(): string
    {
        return TenagaKesehatanKecamatan::class;
    }

    protected function batchFields(): array
    {
        return [
            'jumlah_total' => 'Total',
            'dokter'       => 'Dokter',
            'perawat'      => 'Perawat',
            'bidan'        => 'Bidan',
            'ahli_gizi'    => 'Ahli Gizi',
            'farmasi'      => 'Farmasi',
        ];
    }

    protected function batchJudul(): string
    {
        return 'Isi Massal Tenaga Kesehatan per Kecamatan';
    }

    protected function batchRoutePrefix(): string
    {
        return 'admin.tenaga-kesehatan';
    }

    protected function batchRedirect(): string
    {
        return 'admin.kesehatan.index';
    }

    public function store(Request $request)
    {
        TenagaKesehatanKecamatan::create($this->validated($request));

        return redirect()->route('admin.kesehatan.index')->with('success', 'Data tenaga kesehatan ditambahkan.');
    }

    public function update(Request $request, TenagaKesehatanKecamatan $tenagaKesehatan)
    {
        $tenagaKesehatan->update($this->validated($request, $tenagaKesehatan));

        return redirect()->route('admin.kesehatan.index')->with('success', 'Data tenaga kesehatan diperbarui.');
    }

    public function destroy(TenagaKesehatanKecamatan $tenagaKesehatan)
    {
        $tenagaKesehatan->delete();

        return redirect()->route('admin.kesehatan.index')->with('success', 'Data tenaga kesehatan dihapus.');
    }

    private function validated(Request $request, ?\Illuminate\Database\Eloquent\Model $item = null): array
    {
        return $request->validate([
            'kecamatan_id' => ['required', 'exists:kecamatan,id'],
            'tahun'        => array_merge($this->aturanTahunInduk(), [
                $this->unikPerPeriode('tenaga_kesehatan_kecamatan', ['kecamatan_id' => $request->input('kecamatan_id')], $item),
            ]),
            'jumlah_total' => ['required', 'integer', 'min:0'],
            'dokter'       => ['required', 'integer', 'min:0'],
            'perawat'      => ['required', 'integer', 'min:0'],
            'bidan'        => ['required', 'integer', 'min:0'],
            'ahli_gizi'    => ['required', 'integer', 'min:0'],
            'farmasi'      => ['required', 'integer', 'min:0'],
        ], $this->pesanPeriodeUnik('kecamatan ini untuk tahun tersebut') + $this->pesanTahunInduk());
    }
}
