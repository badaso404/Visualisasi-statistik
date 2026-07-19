<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\CsvPerKecamatan;
use App\Http\Controllers\Admin\Concerns\IsiMassalPerKecamatan;
use App\Http\Controllers\Admin\Concerns\ValidasiPeriodeUnik;
use App\Http\Controllers\Controller;
use App\Models\PendidikanKecamatan;
use Illuminate\Http\Request;

class PendidikanKecamatanController extends Controller
{
    use ValidasiPeriodeUnik;

    use IsiMassalPerKecamatan;
    use CsvPerKecamatan;

    protected function csvNama(): string
    {
        return 'pendidikan-kecamatan';
    }

    protected function batchModel(): string
    {
        return PendidikanKecamatan::class;
    }

    protected function batchFields(): array
    {
        return [
            'jumlah_pelajar'        => 'Pelajar',
            'jumlah_pendidik'       => 'Pendidik',
            'jumlah_sekolah_negeri' => 'Sekolah Negeri',
            'jumlah_sekolah_swasta' => 'Sekolah Swasta',
        ];
    }

    protected function batchJudul(): string
    {
        return 'Isi Massal Pendidikan per Kecamatan';
    }

    protected function batchRoutePrefix(): string
    {
        return 'admin.pendidikan-kecamatan';
    }

    protected function batchRedirect(): string
    {
        return 'admin.pendidikan.index';
    }

    public function store(Request $request)
    {
        PendidikanKecamatan::create($this->validated($request));

        return redirect()->route('admin.pendidikan.index')->with('success', 'Data pendidikan kecamatan ditambahkan.');
    }

    public function update(Request $request, PendidikanKecamatan $pendidikanKecamatan)
    {
        $pendidikanKecamatan->update($this->validated($request, $pendidikanKecamatan));

        return redirect()->route('admin.pendidikan.index')->with('success', 'Data pendidikan kecamatan diperbarui.');
    }

    public function destroy(PendidikanKecamatan $pendidikanKecamatan)
    {
        $pendidikanKecamatan->delete();

        return redirect()->route('admin.pendidikan.index')->with('success', 'Data pendidikan kecamatan dihapus.');
    }

    private function validated(Request $request, ?\Illuminate\Database\Eloquent\Model $item = null): array
    {
        return $request->validate([
            'kecamatan_id'          => ['required', 'exists:kecamatan,id'],
            'tahun'                 => ['required', 'integer', 'min:1900', 'max:2100',
                $this->unikPerPeriode('pendidikan_kecamatan', ['kecamatan_id' => $request->input('kecamatan_id')], $item),
            ],
            'jumlah_pelajar'        => ['required', 'integer', 'min:0'],
            'jumlah_pendidik'       => ['required', 'integer', 'min:0'],
            'jumlah_sekolah_negeri' => ['required', 'integer', 'min:0'],
            'jumlah_sekolah_swasta' => ['required', 'integer', 'min:0'],
        ], $this->pesanPeriodeUnik('kecamatan ini untuk tahun tersebut'));
    }
}
