<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CctvKecamatan;
use App\Models\JakWifiKecamatan;

class InfrastrukturDigitalController extends Controller
{
    public function index()
    {
        $jakWifi = JakWifiKecamatan::with('kecamatan')->orderByDesc('tahun')->get();
        $cctv    = CctvKecamatan::with('kecamatan')->orderByDesc('tahun')->get();

        return view('admin.infrastruktur-digital.index', compact('jakWifi', 'cctv'));
    }
}
