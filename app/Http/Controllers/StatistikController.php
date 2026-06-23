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
use App\Models\DataBencana;

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

    public function kependudukan(Request $request)
    {
        $availableTahun = [2024, 2025, 2026];
        $tahun = (int) $request->get('tahun', 2024);
        if (!in_array($tahun, $availableTahun)) {
            $tahun = 2024;
        }

        $summary = DataKependudukan::where('tahun', $tahun)->first();

        $perKecamatan = PendudukKecamatan::with('kecamatan')
            ->where('tahun', $tahun)
            ->orderByDesc('jumlah_penduduk')
            ->get();

        $perKelurahan = PendudukKelurahan::with('kecamatan')
            ->where('tahun', $tahun)
            ->orderByDesc('jumlah_penduduk')
            ->get();

        $kelurahanPerKecamatan = $perKelurahan->groupBy('kecamatan.nama_kecamatan')
            ->map(fn($items) => [
                'labels' => $items->pluck('nama_kelurahan'),
                'data'   => $items->pluck('jumlah_penduduk')->map(fn($v) => (int)$v),
            ]);

        return view('statistik.kependudukan', compact(
            'summary',
            'perKecamatan',
            'perKelurahan',
            'kelurahanPerKecamatan',
            'tahun',
            'availableTahun'
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

    public function bencana(Request $request)
    {
        $availableTahun = DataBencana::query()
            ->select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        $tahun = (int) $request->get('tahun', $availableTahun->first() ?? date('Y'));

        $items = DataBencana::with('kecamatan')
            ->where('tahun', $tahun)
            ->orderByDesc('tanggal_kejadian')
            ->get();

        // Perbandingan jenis bencana (untuk pie chart): total kejadian per jenis
        $perJenis = $items->groupBy('jenis_bencana')
            ->map(fn($rows) => $rows->sum('jumlah_kejadian'))
            ->sortDesc();

        $ringkasan = [
            'total_kejadian'  => $items->sum('jumlah_kejadian'),
            'total_korban'    => $items->sum('jumlah_korban'),
            'total_terdampak' => $items->sum('jumlah_terdampak'),
            'jenis_terbanyak' => $perJenis->keys()->first() ?? '-',
        ];

        // Warna konsisten per jenis bencana (dipakai pie chart, peta, & tabel)
        $warnaJenis = [
            'Banjir'        => '#1e88e5',
            'Kebakaran'     => '#e53935',
            'Tanah Longsor' => '#8d6e63',
            'Angin Kencang' => '#26a69a',
            'Pohon Tumbang' => '#7cb342',
            'Gempa Bumi'    => '#8e24aa',
            'Lainnya'       => '#9e9e9e',
        ];

        return view('statistik.bencana', compact(
            'items', 'perJenis', 'ringkasan', 'tahun', 'availableTahun', 'warnaJenis'
        ));
    }
}
