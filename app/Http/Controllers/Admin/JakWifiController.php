<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\CsvPerKecamatan;
use App\Http\Controllers\Admin\Concerns\IsiMassalPerKecamatan;
use App\Http\Controllers\Admin\Concerns\TahunMengikutiInduk;
use App\Http\Controllers\Admin\Concerns\ValidasiPeriodeUnik;
use App\Http\Controllers\Controller;
use App\Models\JakWifiKecamatan;
use Illuminate\Http\Request;

class JakWifiController extends Controller
{
    use ValidasiPeriodeUnik;
    use TahunMengikutiInduk;

    use IsiMassalPerKecamatan;
    use CsvPerKecamatan;

    protected function tabelInduk(): ?string
    {
        return null;
    }

    protected function csvNama(): string
    {
        return 'jak-wifi';
    }

    protected function batchModel(): string
    {
        return JakWifiKecamatan::class;
    }

    protected function batchFields(): array
    {
        return [
            'jumlah_titik'    => 'Jumlah Titik',
            'titik_aktif'     => 'Titik Aktif',
            'jumlah_pengguna' => 'Jumlah Pengguna',
        ];
    }

    protected function batchJudul(): string
    {
        return 'Isi Massal JakWiFi per Kecamatan';
    }

    protected function batchRoutePrefix(): string
    {
        return 'admin.jak-wifi';
    }

    protected function batchRedirect(): string
    {
        return 'admin.infrastruktur-digital.index';
    }

    public function store(Request $request)
    {
        JakWifiKecamatan::create($this->validated($request));

        return redirect()->route('admin.infrastruktur-digital.index')->with('success', 'Data JakWiFi ditambahkan.');
    }

    public function update(Request $request, JakWifiKecamatan $jakWifi)
    {
        $jakWifi->update($this->validated($request, $jakWifi));

        return redirect()->route('admin.infrastruktur-digital.index')->with('success', 'Data JakWiFi diperbarui.');
    }

    public function destroy(JakWifiKecamatan $jakWifi)
    {
        $jakWifi->delete();

        return redirect()->route('admin.infrastruktur-digital.index')->with('success', 'Data JakWiFi dihapus.');
    }

    private function validated(Request $request, ?\Illuminate\Database\Eloquent\Model $item = null): array
    {
        return $request->validate([
            'kecamatan_id'    => ['required', 'exists:kecamatan,id'],
            'tahun'           => array_merge($this->aturanTahunInduk(), [
                $this->unikPerPeriode('jak_wifi_kecamatan', ['kecamatan_id' => $request->input('kecamatan_id')], $item),
            ]),
            'jumlah_titik'    => ['required', 'integer', 'min:0'],
            'titik_aktif'     => ['required', 'integer', 'min:0'],
            'jumlah_pengguna' => ['required', 'integer', 'min:0'],
            'keterangan'      => ['nullable', 'string', 'max:255'],
        ], $this->pesanPeriodeUnik('kecamatan ini untuk tahun tersebut') + $this->pesanTahunInduk());
    }
}
