<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CctvKecamatan;
use App\Models\DataGeografis;
use App\Models\DataIklim;
use App\Models\DataBencana;
use App\Models\DataKemiskinan;
use App\Models\DataKependudukan;
use App\Models\DataKesehatan;
use App\Models\DataPendidikan;
use App\Models\DataPerekonomian;
use App\Models\JakWifiKecamatan;
use App\Models\Kecamatan;

class DashboardController extends Controller
{
    /**
     * Daftarnya mengikuti urutan menu samping supaya dashboard terbaca sebagai
     * ringkasan menu, bukan kumpulan angka acak. Kemiskinan, Perekonomian, dan
     * Infrastruktur Digital sempat tertinggal di sini padahal sudah lama ada di
     * menu — dashboard jadi menyesatkan soal modul apa saja yang terisi.
     */
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
            'kemiskinan'   => DataKemiskinan::count(),
            'perekonomian' => DataPerekonomian::count(),
            // Modul ini tidak punya tabel ringkasan; yang dihitung baris per
            // kecamatan dari kedua jenis perangkat.
            'infrastruktur digital' => JakWifiKecamatan::count() + CctvKecamatan::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
