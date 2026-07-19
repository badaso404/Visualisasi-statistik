<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\CsvPerKecamatan;
use App\Http\Controllers\Admin\Concerns\IsiMassalPerKecamatan;
use App\Http\Controllers\Admin\Concerns\ValidasiPeriodeUnik;
use App\Http\Controllers\Controller;
use App\Models\CctvKecamatan;
use Illuminate\Http\Request;

class CctvController extends Controller
{
    use ValidasiPeriodeUnik;

    use IsiMassalPerKecamatan;
    use CsvPerKecamatan;

    protected function csvNama(): string
    {
        return 'cctv';
    }

    protected function batchModel(): string
    {
        return CctvKecamatan::class;
    }

    protected function batchFields(): array
    {
        return [
            'jumlah_unit'  => 'Jumlah Unit',
            'unit_aktif'   => 'Unit Aktif',
            'terintegrasi' => 'Terintegrasi',
        ];
    }

    protected function batchJudul(): string
    {
        return 'Isi Massal CCTV per Kecamatan';
    }

    protected function batchRoutePrefix(): string
    {
        return 'admin.cctv';
    }

    protected function batchRedirect(): string
    {
        return 'admin.infrastruktur-digital.index';
    }

    public function store(Request $request)
    {
        CctvKecamatan::create($this->validated($request));

        return redirect()->route('admin.infrastruktur-digital.index')->with('success', 'Data CCTV ditambahkan.');
    }

    public function update(Request $request, CctvKecamatan $cctv)
    {
        $cctv->update($this->validated($request, $cctv));

        return redirect()->route('admin.infrastruktur-digital.index')->with('success', 'Data CCTV diperbarui.');
    }

    public function destroy(CctvKecamatan $cctv)
    {
        $cctv->delete();

        return redirect()->route('admin.infrastruktur-digital.index')->with('success', 'Data CCTV dihapus.');
    }

    private function validated(Request $request, ?\Illuminate\Database\Eloquent\Model $item = null): array
    {
        return $request->validate([
            'kecamatan_id' => ['required', 'exists:kecamatan,id'],
            'tahun'        => ['required', 'integer', 'min:1900', 'max:2100',
                $this->unikPerPeriode('cctv_kecamatan', ['kecamatan_id' => $request->input('kecamatan_id')], $item),
            ],
            'jumlah_unit'  => ['required', 'integer', 'min:0'],
            'unit_aktif'   => ['required', 'integer', 'min:0'],
            'terintegrasi' => ['required', 'integer', 'min:0'],
            'keterangan'   => ['nullable', 'string', 'max:255'],
        ], $this->pesanPeriodeUnik('kecamatan ini untuk tahun tersebut'));
    }
}
