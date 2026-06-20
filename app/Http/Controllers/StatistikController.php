<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataGeografis;
use App\Models\LuasKecamatan;
use App\Models\DataIklim;
use App\Models\DataKependudukan;
use App\Models\PendudukKecamatan;
use App\Models\PendudukKelurahan;
use App\Models\DataPendidikan;
use App\Models\PendidikanKecamatan;
use App\Models\DataKesehatan;
use App\Models\TenagaKesehatanKecamatan;
use App\Models\FasilitasKesehatanKecamatan;

class StatistikController extends Controller
{
    public function geografis()
    {
        $geo = DataGeografis::where('tahun', 2024)->first();
        $luas = LuasKecamatan::with('kecamatan')
            ->where('data_geografis_id', $geo->id)
            ->get();

        return view('statistik.geografis', compact('geo', 'luas'));
    }

    public function iklim()
    {
        $iklim = DataIklim::where('tahun', 2024)->orderBy('bulan')->get();

        return view('statistik.iklim', compact('iklim'));
    }

    public function kependudukan()
    {
        $summary = DataKependudukan::where('tahun', 2024)->first();

        $perKecamatan = PendudukKecamatan::with('kecamatan')
            ->where('tahun', 2024)
            ->orderByDesc('jumlah_penduduk')
            ->get();

        $perKelurahan = PendudukKelurahan::with('kecamatan')
            ->where('tahun', 2024)
            ->orderByDesc('jumlah_penduduk')
            ->get();

        // Group kelurahan per kecamatan untuk drill-down
        $kelurahanPerKecamatan = $perKelurahan->groupBy('kecamatan.nama_kecamatan')
            ->map(fn($items) => [
                'labels' => $items->pluck('nama_kelurahan'),
                'data'   => $items->pluck('jumlah_penduduk')->map(fn($v) => (int)$v),
            ]);

        return view('statistik.kependudukan', compact(
            'summary',
            'perKecamatan',
            'perKelurahan',
            'kelurahanPerKecamatan'
        ));
    }

    public function pendidikan()
    {
        $summary = DataPendidikan::where('tahun', 2024)->first();
        $perKecamatan = PendidikanKecamatan::with('kecamatan')
            ->where('tahun', 2024)
            ->orderByDesc('jumlah_murid')
            ->get();

        return view('statistik.pendidikan', compact('summary', 'perKecamatan'));
    }

    public function kesehatan()
    {
        $summary = DataKesehatan::where('tahun', 2024)->first();
        $tenaga = TenagaKesehatanKecamatan::with('kecamatan')
            ->where('tahun', 2024)
            ->orderByDesc('jumlah_total')
            ->get();
        $fasilitas = FasilitasKesehatanKecamatan::with('kecamatan')
            ->where('tahun', 2024)
            ->orderByDesc('jumlah_total')
            ->get();

        return view('statistik.kesehatan', compact('summary', 'tenaga', 'fasilitas'));
    }
}
