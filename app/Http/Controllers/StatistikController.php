<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Statistik\SatuDataClient;
use App\Services\Statistik\DsdaClient;
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
use App\Models\DataKemiskinan;
use App\Models\KemiskinanKecamatan;

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
        // Kelurahan/RW/RT bersumber BPS (var 155) di kolom luas_kecamatan;
        // jika null, kelurahan fallback ke jumlah baris penduduk_kelurahan.
        $kecStats = $luas->mapWithKeys(function ($row) use ($pendudukKec, $kelurahanCount) {
            $nama     = strtoupper($row->kecamatan->nama_kecamatan);
            $penduduk = optional($pendudukKec->get($nama))->jumlah_penduduk;
            $kepadatan = ($penduduk && $row->luas_km2) ? $penduduk / $row->luas_km2 : null;

            return [$nama => [
                'nama'       => $row->kecamatan->nama_kecamatan,
                'luas'       => (float) $row->luas_km2,
                'persentase' => (float) $row->persentase,
                'kelurahan'  => (int) ($row->jumlah_kelurahan ?? $kelurahanCount->get($nama, 0)),
                'rw'         => $row->jumlah_rw !== null ? (int) $row->jumlah_rw : null,
                'rt'         => $row->jumlah_rt !== null ? (int) $row->jumlah_rt : null,
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
        $availableTahun = DataKependudukan::query()
            ->select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun')->all();

        $tahun = (int) $request->get('tahun', $availableTahun[0] ?? date('Y'));
        if (!in_array($tahun, $availableTahun)) {
            $tahun = (int) ($availableTahun[0] ?? $tahun);
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

    /**
     * UJI COBA — Piramida penduduk dari API Satu Data Jakarta
     * (jumlah penduduk per kelompok usia & jenis kelamin, 2013-2016).
     */
    public function kependudukanApi(Request $request, SatuDataClient $satuData)
    {
        // Data ~60rb baris → di-cache di dalam client biar tidak fetch tiap request
        $rows = $satuData->pendudukPerUsia();

        // Label usia rusak/tidak konsisten antar tahun (Excel + variasi ejaan)
        $fixUsia = fn($u) => match (trim($u)) {
            '00-04'                     => '0-4',
            '9-May'                     => '5-9',
            '14-Oct', '10-Jan'          => '10-14',
            '75+', '75-ke atas'         => '>75',
            default                     => trim($u),
        };
        // Jenis kelamin: "Laki laki" / "Laki-laki" / "Laki-Laki"
        $fixJk = fn($jk) => str_starts_with(strtolower(trim($jk)), 'laki') ? 'L' : 'P';
        // Kolom jumlah bisa bernama 'jumlah' atau 'jumlah_penduduk'
        $getJumlah = fn($r) => (int) ($r['jumlah'] ?? $r['jumlah_penduduk'] ?? 0);

        $urutanUsia = ['0-4','5-9','10-14','15-19','20-24','25-29','30-34','35-39',
                       '40-44','45-49','50-54','55-59','60-64','65-69','70-74','>75'];

        // Fokus Jakarta Barat saja
        $kota = 'JAKARTA BARAT';

        $availableTahun = collect($rows)
            ->where('nama_kabupaten_kota', $kota)
            ->pluck('tahun')->unique()->sort()->values(); // 2013-2016 saja

        // Default ke tahun terbaru (2021) — 2013 dilewati karena inflasi
        $tahun = (string) $request->get('tahun', $availableTahun->last() ?? '2021');
        if (!$availableTahun->contains($tahun)) {
            $tahun = (string) ($availableTahun->last() ?? '2021');
        }

        // Baris Jakarta Barat pada tahun terpilih
        $rowsJb = collect($rows)->filter(fn($r) =>
            ($r['nama_kabupaten_kota'] ?? null) === $kota && ($r['tahun'] ?? null) === $tahun
        );

        // 1) Piramida: usia => ['L' => n, 'P' => n]
        $agg = [];
        foreach ($rowsJb as $r) {
            $u  = $fixUsia($r['usia'] ?? '');
            $jk = $fixJk($r['jenis_kelamin'] ?? '');
            $agg[$u][$jk] = ($agg[$u][$jk] ?? 0) + $getJumlah($r);
        }
        $labels    = $urutanUsia;
        $laki      = array_map(fn($u) => -($agg[$u]['L'] ?? 0), $urutanUsia); // negatif utk piramida
        $perempuan = array_map(fn($u) =>  ($agg[$u]['P'] ?? 0), $urutanUsia);
        $totalL = array_sum(array_map('abs', $laki));
        $totalP = array_sum($perempuan);

        // 2) Total penduduk per kecamatan (urut desc)
        $perKecamatan = $rowsJb
            ->groupBy('nama_kecamatan')
            ->map(fn($items, $nama) => [
                'nama'   => $nama,
                'jumlah' => $items->sum($getJumlah),
            ])
            ->sortByDesc('jumlah')
            ->values();

        // 3) Total penduduk per kelurahan, dikelompokkan per kecamatan (utk drill-down)
        $kelurahanPerKecamatan = $rowsJb
            ->groupBy('nama_kecamatan')
            ->map(function ($items) use ($getJumlah) {
                $perKel = $items->groupBy('nama_kelurahan')
                    ->map(fn($k) => $k->sum($getJumlah))
                    ->sortDesc();
                return [
                    'labels' => $perKel->keys()->values(),
                    'data'   => $perKel->values(),
                ];
            });

        // 2013 angkanya inflasi ~1.7x di sumber → beri penanda di view
        $inflated = ($tahun === '2013');

        return view('statistik.kependudukan-api', compact(
            'labels', 'laki', 'perempuan', 'totalL', 'totalP',
            'tahun', 'kota', 'availableTahun', 'inflated',
            'perKecamatan', 'kelurahanPerKecamatan'
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

    public function bencana(Request $request, DsdaClient $dsda)
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

        // Semua stasiun Tinggi Muka Air (TMA) — live DSDA DKI, cache 5 menit.
        // Dipakai untuk: (a) layer titik pantau air, (b) status siaga titik prioritas banjir.
        $tmaAll = $dsda->tinggiMukaAir();

        // Cari status stasiun TMA terdekat (radius maks 5 km) dari sebuah koordinat
        $statusTerdekat = function ($la, $lo) use ($tmaAll) {
            $best = null; $bestD = INF;
            foreach ($tmaAll as $s) {
                $dLa = deg2rad($s['lat'] - $la);
                $dLo = deg2rad($s['lng'] - $lo);
                $a = sin($dLa / 2) ** 2 + cos(deg2rad($la)) * cos(deg2rad($s['lat'])) * sin($dLo / 2) ** 2;
                $d = 6371 * 2 * asin(sqrt($a));
                if ($d < $bestD) { $bestD = $d; $best = $s; }
            }
            if (!$best || $bestD > 5) return null;
            return ['status' => $best['status'], 'tinggi' => $best['tinggi'], 'dari' => $best['name'], 'jarak' => round($bestD, 1)];
        };

        // Titik referensi peta: zona rawan banjir (+ status TMA terdekat), pos damkar, zona aman
        $titikBencana = \App\Models\TitikBencana::all()->groupBy(function ($t) {
            return match ($t->kategori) {
                'banjir_rawan' => 'banjir-p' . ($t->level ?? 3),
                'pos_damkar'   => 'pos-damkar',
                'zona_aman'    => 'zona-aman',
                default        => 'lainnya',
            };
        })->map(function ($rows) use ($statusTerdekat) {
            return $rows->map(function ($t) use ($statusTerdekat) {
                $data = [
                    'lat'  => (float) $t->latitude,
                    'lng'  => (float) $t->longitude,
                    'name' => $t->nama,
                    'ket'  => $t->keterangan,
                    'link' => $t->link_maps,
                ];
                if ($t->kategori === 'banjir_rawan') {
                    $near = $statusTerdekat((float) $t->latitude, (float) $t->longitude);
                    $data['status'] = $near['status'] ?? null;
                    $data['tinggi'] = $near['tinggi'] ?? null;
                    $data['dari']   = $near['dari'] ?? null;
                    $data['jarak']  = $near['jarak'] ?? null;
                }
                return $data;
            })->values();
        });

        // 6 titik pantau air terpilih untuk layer banjir (dari data TMA yang sama)
        $whitelist = [
            'angke hulu'       => 'rumah-pompa',
            'kaliduri'         => 'pintu-air',
            'cengkareng drain' => 'pintu-air',
            'karet'            => 'pintu-air',
            'palmerah'         => 'posko',
            'kamal muara'      => 'rumah-pompa',
        ];
        $banjirAir = [];
        foreach ($tmaAll as $s) {
            $low = strtolower($s['name']);
            $kind = null;
            foreach ($whitelist as $kw => $k) {
                if (str_contains($low, $kw)) { $kind = $k; break; }
            }
            if (!$kind) continue;
            $banjirAir[] = [
                'lat' => $s['lat'], 'lng' => $s['lng'], 'name' => $s['name'],
                'kind' => $kind, 'status' => $s['status'], 'tinggi' => $s['tinggi'], 'tanggal' => $s['tanggal'],
            ];
        }
        $tmaTitik = ['banjir-air' => $banjirAir];

        return view('statistik.bencana', compact(
            'items', 'perJenis', 'ringkasan', 'tahun', 'availableTahun', 'warnaJenis', 'kecamatanNames', 'titikBencana', 'tmaTitik'
        ));
    }

    public function kemiskinan(Request $request)
    {
        $availableTahun = DataKemiskinan::query()
            ->select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        $tahun = (int) $request->get('tahun', $availableTahun->first() ?? date('Y'));
        if (!$availableTahun->contains($tahun)) {
            $tahun = (int) ($availableTahun->first() ?? $tahun);
        }

        $summary = DataKemiskinan::where('tahun', $tahun)->first();

        // Riwayat seluruh tahun (asc) untuk grafik tren antar-tahun — semua dari BPS
        $riwayat = DataKemiskinan::orderBy('tahun')->get();

        // Tren jumlah penduduk miskin vs tahun sebelumnya (untuk penanda naik/turun)
        $prev = DataKemiskinan::where('tahun', $tahun - 1)->first();
        $tren = ($prev && $prev->jumlah_penduduk_miskin > 0)
            ? round(($summary?->jumlah_penduduk_miskin - $prev->jumlah_penduduk_miskin) / $prev->jumlah_penduduk_miskin * 100, 1)
            : null;

        return view('statistik.kemiskinan', compact('summary', 'riwayat', 'tren', 'tahun', 'availableTahun'));
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
