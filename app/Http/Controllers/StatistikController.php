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
use App\Models\JakWifiKecamatan;
use App\Models\CctvKecamatan;

class StatistikController extends Controller
{
    public function geografis(Request $request)
    {
        $availableTahun = DataGeografis::query()
            ->select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        $tahun = (int) $request->get('tahun', $availableTahun->first() ?? date('Y'));
        if (!$availableTahun->contains($tahun)) {
            $tahun = (int) ($availableTahun->first() ?? $tahun);
        }

        $geo = DataGeografis::where('tahun', $tahun)->first();
        $luas = LuasKecamatan::with('kecamatan')
            ->where('data_geografis_id', $geo->id)
            ->get();

        // Penduduk per kecamatan (untuk kepadatan) & jumlah kelurahan per kecamatan
        $pendudukKec = PendudukKecamatan::with('kecamatan')
            ->where('tahun', $tahun)->get()
            ->keyBy(fn($p) => strtoupper($p->kecamatan->nama_kecamatan));

        $kelurahanCount = PendudukKelurahan::with('kecamatan')
            ->where('tahun', $tahun)->get()
            ->groupBy(fn($k) => strtoupper($k->kecamatan->nama_kecamatan))
            ->map->count();

        // Statistik per kecamatan untuk interaksi dinamis pada card
        $kecStats = $luas->mapWithKeys(function ($row) use ($pendudukKec, $kelurahanCount) {
            $nama     = strtoupper($row->kecamatan->nama_kecamatan);
            $penduduk = optional($pendudukKec->get($nama))->jumlah_penduduk;
            $kepadatan = ($penduduk && $row->luas_km2) ? $penduduk / $row->luas_km2 : null;

            return [$nama => [
                'nama'       => $row->kecamatan->nama_kecamatan,
                'luas'       => (float) $row->luas_km2,
                'persentase' => (float) $row->persentase,
                'kelurahan'  => (int) $kelurahanCount->get($nama, 0),
                'penduduk'   => $penduduk ? (int) $penduduk : null,
                'kepadatan'  => $kepadatan ? round($kepadatan) : null,
            ]];
        });

        return view('statistik.geografis', compact('geo', 'luas', 'kecStats', 'tahun', 'availableTahun'));
    }

    public function iklim(Request $request)
    {
        $availableTahun = DataIklim::query()
            ->select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        $tahun = (int) $request->get('tahun', $availableTahun->first() ?? date('Y'));
        if (!$availableTahun->contains($tahun)) {
            $tahun = (int) ($availableTahun->first() ?? $tahun);
        }

        $iklim = DataIklim::where('tahun', $tahun)->orderBy('bulan')->get();

        return view('statistik.iklim', compact('iklim', 'tahun', 'availableTahun'));
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

    public function pendidikan(Request $request)
    {
        $availableTahun = DataPendidikan::query()
            ->select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        $tahun = (int) $request->get('tahun', $availableTahun->first() ?? date('Y'));
        if (!$availableTahun->contains($tahun)) {
            $tahun = (int) ($availableTahun->first() ?? $tahun);
        }

        $summary = DataPendidikan::where('tahun', $tahun)->first();
        $perKecamatan = PendidikanKecamatan::with('kecamatan')
            ->where('tahun', $tahun)
            ->orderByDesc('jumlah_pelajar')
            ->get();

        return view('statistik.pendidikan', compact('summary', 'perKecamatan', 'tahun', 'availableTahun'));
    }

    public function kesehatan(Request $request)
    {
        $availableTahun = DataKesehatan::query()
            ->select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        $tahun = (int) $request->get('tahun', $availableTahun->first() ?? date('Y'));
        if (!$availableTahun->contains($tahun)) {
            $tahun = (int) ($availableTahun->first() ?? $tahun);
        }

        $summary = DataKesehatan::where('tahun', $tahun)->first();
        $tenaga = TenagaKesehatanKecamatan::with('kecamatan')
            ->where('tahun', $tahun)
            ->orderByDesc('jumlah_total')
            ->get();
        $fasilitas = FasilitasKesehatanKecamatan::with('kecamatan')
            ->where('tahun', $tahun)
            ->orderByDesc('jumlah_total')
            ->get();

        return view('statistik.kesehatan', compact('summary', 'tenaga', 'fasilitas', 'tahun', 'availableTahun'));
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

        // Daftar kecamatan Jakarta Barat untuk overlay batas wilayah pada peta
        $kecamatanNames = \App\Models\Kecamatan::orderBy('nama_kecamatan')->pluck('nama_kecamatan');

        // Titik referensi peta: zona rawan banjir (level), pos damkar, zona aman
        $titikBencana = \App\Models\TitikBencana::all()->groupBy(function ($t) {
            return match ($t->kategori) {
                'banjir_rawan' => 'banjir-p' . ($t->level ?? 3),
                'pos_damkar'   => 'pos-damkar',
                'zona_aman'    => 'zona-aman',
                default        => 'lainnya',
            };
        })->map(function ($rows) {
            return $rows->map(fn($t) => [
                'lat'  => (float) $t->latitude,
                'lng'  => (float) $t->longitude,
                'name' => $t->nama,
                'ket'  => $t->keterangan,
                'link' => $t->link_maps,
            ])->values();
        });

        return view('statistik.bencana', compact(
            'items', 'perJenis', 'ringkasan', 'tahun', 'availableTahun', 'warnaJenis', 'kecamatanNames', 'titikBencana'
        ));
    }

    public function infrastrukturDigital(Request $request)
    {
        // Gabungan tahun dari kedua sumber data (JakWiFi & CCTV)
        $availableTahun = JakWifiKecamatan::query()->select('tahun')
            ->union(CctvKecamatan::query()->select('tahun'))
            ->pluck('tahun')->unique()->sortDesc()->values();

        $tahun = (int) $request->get('tahun', $availableTahun->first() ?? date('Y'));
        if ($availableTahun->isNotEmpty() && !$availableTahun->contains($tahun)) {
            $tahun = (int) $availableTahun->first();
        }

        $jakWifi = JakWifiKecamatan::with('kecamatan')
            ->where('tahun', $tahun)
            ->orderByDesc('jumlah_titik')
            ->get();
        $cctv = CctvKecamatan::with('kecamatan')
            ->where('tahun', $tahun)
            ->orderByDesc('jumlah_unit')
            ->get();

        $ringkasan = [
            'total_titik_wifi' => $jakWifi->sum('jumlah_titik'),
            'wifi_aktif'       => $jakWifi->sum('titik_aktif'),
            'total_pengguna'   => $jakWifi->sum('jumlah_pengguna'),
            'total_cctv'       => $cctv->sum('jumlah_unit'),
            'cctv_aktif'       => $cctv->sum('unit_aktif'),
            'cctv_terintegrasi'=> $cctv->sum('terintegrasi'),
        ];

        // Tren titik WiFi dibanding tahun sebelumnya
        $prevWifi = JakWifiKecamatan::where('tahun', $tahun - 1)->sum('jumlah_titik');
        $ringkasan['tren_wifi'] = $prevWifi > 0
            ? round(($ringkasan['total_titik_wifi'] - $prevWifi) / $prevWifi * 100, 1)
            : null;

        // Persentase online & keseluruhan perangkat aktif
        $ringkasan['wifi_online_pct'] = $ringkasan['total_titik_wifi'] > 0
            ? round($ringkasan['wifi_aktif'] / $ringkasan['total_titik_wifi'] * 100) : 0;
        $ringkasan['cctv_online_pct'] = $ringkasan['total_cctv'] > 0
            ? round($ringkasan['cctv_aktif'] / $ringkasan['total_cctv'] * 100) : 0;

        $totalUnit  = $ringkasan['total_titik_wifi'] + $ringkasan['total_cctv'];
        $totalAktif = $ringkasan['wifi_aktif'] + $ringkasan['cctv_aktif'];
        $ringkasan['perangkat_aktif_pct'] = $totalUnit > 0
            ? round($totalAktif / $totalUnit * 100, 1) : 0;

        // Distribusi gabungan per kecamatan (untuk chart & tabel rincian)
        $distribusi = [];
        foreach ($jakWifi as $w) {
            $distribusi[$w->kecamatan_id] = [
                'nama'       => $w->kecamatan->nama_kecamatan ?? '-',
                'wifi'       => (int) $w->jumlah_titik,
                'wifi_aktif' => (int) $w->titik_aktif,
                'cctv'       => 0,
                'cctv_aktif' => 0,
            ];
        }
        foreach ($cctv as $c) {
            $row = $distribusi[$c->kecamatan_id] ?? [
                'nama' => $c->kecamatan->nama_kecamatan ?? '-',
                'wifi' => 0, 'wifi_aktif' => 0, 'cctv' => 0, 'cctv_aktif' => 0,
            ];
            $row['cctv']       = (int) $c->jumlah_unit;
            $row['cctv_aktif'] = (int) $c->unit_aktif;
            $distribusi[$c->kecamatan_id] = $row;
        }
        $distribusi = collect($distribusi)
            ->sortByDesc(fn ($r) => $r['wifi'] + $r['cctv'])
            ->values();

        // Baris rincian unit (per kecamatan per jenis) untuk tabel
        $unitRows = collect();
        foreach ($jakWifi as $w) {
            $unitRows->push([
                'kecamatan' => $w->kecamatan->nama_kecamatan ?? '-',
                'tipe'      => 'JAKWIFI',
                'total'     => (int) $w->jumlah_titik,
                'aktif'     => (int) $w->titik_aktif,
                'pengguna'  => (int) $w->jumlah_pengguna,
            ]);
        }
        foreach ($cctv as $c) {
            $unitRows->push([
                'kecamatan' => $c->kecamatan->nama_kecamatan ?? '-',
                'tipe'      => 'CCTV',
                'total'     => (int) $c->jumlah_unit,
                'aktif'     => (int) $c->unit_aktif,
                'pengguna'  => null,
            ]);
        }
        // Kelompokkan per jenis dulu (CCTV lalu JakWiFi), baru urut total desc,
        // supaya baris JakWiFi tidak "nyempil" di tengah barisan CCTV.
        $unitRows = $unitRows->sort(function ($a, $b) {
            return [$a['tipe'], -$a['total']] <=> [$b['tipe'], -$b['total']];
        })->values();

        // Jumlah titik ilustratif per kecamatan untuk peta sebaran.
        // Kepadatan mengikuti data agregat asli (skala 1:8 agar peta ringan);
        // posisi tiap titik digenerate di sisi klien DI DALAM polygon kecamatan
        // (batas wilayah Jakarta Barat) supaya rapi & tidak keluar wilayah.
        $sebaranKec = $distribusi->map(fn ($r) => [
            'nama' => $r['nama'],
            'wifi' => $r['wifi'] > 0 ? max(1, (int) round($r['wifi'] / 8)) : 0,
            'cctv' => $r['cctv'] > 0 ? max(1, (int) round($r['cctv'] / 8)) : 0,
        ])->values();

        return view('statistik.infrastruktur-digital', compact(
            'jakWifi', 'cctv', 'ringkasan', 'distribusi', 'unitRows', 'sebaranKec', 'tahun', 'availableTahun'
        ));
    }
}
