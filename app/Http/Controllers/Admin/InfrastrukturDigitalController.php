<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CctvKecamatan;
use App\Models\JakWifiKecamatan;
use App\Models\Kecamatan;

class InfrastrukturDigitalController extends Controller
{
    public function index()
    {
        $jakWifi = JakWifiKecamatan::with('kecamatan')->orderByDesc('tahun')->get();
        $cctv    = CctvKecamatan::with('kecamatan')->orderByDesc('tahun')->get();

        $kecamatan = Kecamatan::orderBy('nama_kecamatan')->get();

        return view('admin.infrastruktur-digital.index', compact('jakWifi', 'cctv', 'kecamatan'));
    }
}
