<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataGeografis;
use App\Models\DataIklim;
use App\Models\DataBencana;
use App\Models\DataKependudukan;
use App\Models\DataKesehatan;
use App\Models\DataPendidikan;
use App\Models\Kecamatan;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'kecamatan'    => Kecamatan::count(),
            'geografis'    => DataGeografis::count(),
            'iklim'        => DataIklim::count(),
            'kependudukan' => DataKependudukan::count(),
            'pendidikan'   => DataPendidikan::count(),
            'kesehatan'    => DataKesehatan::count(),
            'bencana'      => DataBencana::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
