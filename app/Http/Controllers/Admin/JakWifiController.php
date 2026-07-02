<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JakWifiKecamatan;
use App\Models\Kecamatan;
use Illuminate\Http\Request;

class JakWifiController extends Controller
{
    public function create()
    {
        return view('admin.infrastruktur-digital.jak-wifi-form', [
            'item'      => new JakWifiKecamatan(),
            'kecamatan' => Kecamatan::orderBy('nama_kecamatan')->get(),
        ]);
    }

    public function store(Request $request)
    {
        JakWifiKecamatan::create($this->validated($request));

        return redirect()->route('admin.infrastruktur-digital.index')->with('success', 'Data JakWiFi ditambahkan.');
    }

    public function edit(JakWifiKecamatan $jakWifi)
    {
        return view('admin.infrastruktur-digital.jak-wifi-form', [
            'item'      => $jakWifi,
            'kecamatan' => Kecamatan::orderBy('nama_kecamatan')->get(),
        ]);
    }

    public function update(Request $request, JakWifiKecamatan $jakWifi)
    {
        $jakWifi->update($this->validated($request));

        return redirect()->route('admin.infrastruktur-digital.index')->with('success', 'Data JakWiFi diperbarui.');
    }

    public function destroy(JakWifiKecamatan $jakWifi)
    {
        $jakWifi->delete();

        return redirect()->route('admin.infrastruktur-digital.index')->with('success', 'Data JakWiFi dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'kecamatan_id'    => ['required', 'exists:kecamatan,id'],
            'tahun'           => ['required', 'integer', 'min:1900', 'max:2100'],
            'jumlah_titik'    => ['required', 'integer', 'min:0'],
            'titik_aktif'     => ['required', 'integer', 'min:0'],
            'jumlah_pengguna' => ['required', 'integer', 'min:0'],
            'keterangan'      => ['nullable', 'string', 'max:255'],
        ]);
    }
}
