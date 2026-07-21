<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
use App\Models\DataPerekonomian;
use App\Models\PdrbSektor;

class StatistikController extends Controller
{
    /**
     * Halaman pengganti saat sebuah modul belum punya record induk untuk tahun
     * yang diminta. Tanpa ini view langsung membaca properti dari null dan
     * pengunjung mendapat error 500.
     */
    private function dataKosong(string $modul, int $tahun, $availableTahun)
    {
        return response()->view('statistik.data-kosong', [
            'modul'          => $modul,
            'tahun'          => $tahun,
            'availableTahun' => $availableTahun instanceof \Illuminate\Support\Collection
                ? $availableTahun->all()
                : (array) $availableTahun,
        ]);
    }


    /**
     * Ringkasan lintas modul. Tiap modul dibaca pada TAHUN TERBARUNYA SENDIRI,
     * bukan satu tahun bersama: cakupan rilis antar-sumber tidak sama (PDRB BPS
     * biasanya tertinggal setahun dari data kependudukan), sehingga memaksakan
     * satu tahun untuk semuanya akan mengosongkan kartu yang datanya ada.
     * Konsekuensinya tiap kartu membawa label tahunnya masing-masing.
     *
     * Urutan blok di bawah = urutan modul di sidebar, dan kartu serta grafik di
     * view mengikuti urutan yang sama supaya halaman terbaca berurutan.
     */
    public function overview()
    {
        $kartu = [];   // kartu indikator kunci, urut sesuai modul

        // ── 1. Geografis ──────────────────────────────────────────
        $tahunGeo = (int) DataGeografis::max('tahun');
        $geo      = $tahunGeo ? DataGeografis::where('tahun', $tahunGeo)->first() : null;
        $luasKec  = $geo
            ? LuasKecamatan::with('kecamatan')->where('data_geografis_id', $geo->id)->get()
            : collect();
        if ($geo) {
            $kartu[] = [
                'modul' => 'Geografis', 'route' => 'statistik.geografis', 'tahun' => $tahunGeo,
                'icon' => 'fa-map', 'warna' => 'ic-teal',
                'label' => 'Luas Wilayah',
                'nilai' => number_format($geo->luas_kota_km2, 2, ',', '.'),
                'satuan' => 'km²',
                'sub' => $luasKec->count() . ' kecamatan &middot; '
                       . number_format((int) $luasKec->sum('jumlah_kelurahan'), 0, ',', '.') . ' kelurahan',
            ];
        }

        // ── 2. Kependudukan ───────────────────────────────────────
        $tahunPenduduk = (int) DataKependudukan::max('tahun');
        $penduduk = $tahunPenduduk ? DataKependudukan::where('tahun', $tahunPenduduk)->first() : null;
        if ($penduduk) {
            $kartu[] = [
                'modul' => 'Kependudukan', 'route' => 'statistik.kependudukan', 'tahun' => $tahunPenduduk,
                'icon' => 'fa-users', 'warna' => 'ic-blue',
                'label' => 'Jumlah Penduduk',
                'nilai' => number_format($penduduk->jumlah_total, 0, ',', '.'),
                'satuan' => 'jiwa',
                'sub' => 'L ' . number_format($penduduk->jumlah_laki_laki, 0, ',', '.')
                       . ' &middot; P ' . number_format($penduduk->jumlah_perempuan, 0, ',', '.'),
            ];
        }

        // ── 3. Pendidikan ─────────────────────────────────────────
        $tahunDidik = (int) DataPendidikan::max('tahun');
        $didik      = $tahunDidik ? DataPendidikan::where('tahun', $tahunDidik)->first() : null;
        $didikKec   = PendidikanKecamatan::with('kecamatan')->where('tahun', $tahunDidik)->get();
        if ($didik) {
            $kartu[] = [
                'modul' => 'Pendidikan', 'route' => 'statistik.pendidikan', 'tahun' => $tahunDidik,
                'icon' => 'fa-graduation-cap', 'warna' => 'ic-amber',
                'label' => 'Jumlah Pelajar',
                'nilai' => number_format((int) $didikKec->sum('jumlah_pelajar'), 0, ',', '.'),
                'satuan' => 'siswa',
                'sub' => number_format((int) $didikKec->sum('jumlah_pendidik'), 0, ',', '.') . ' tenaga pendidik',
            ];
        }

        // Angka Partisipasi Murni vs Kasar per jenjang. Dua indikator ini hanya
        // berarti kalau disandingkan: selisihnya menunjukkan siswa yang usianya
        // di luar jenjang, jadi digambar sebagai satu grafik dua seri.
        $pendidikanIndikator = $didik ? [
            'jenjang' => ['SD/MI', 'SMP/MTs', 'SMA/SMK/MA'],
            'apm'     => [(float) $didik->apm_sd_mi, (float) $didik->apm_smp_mts, (float) $didik->apm_sma_smk_man],
            'apk'     => [(float) $didik->apk_sd_mi, (float) $didik->apk_smp_mts, (float) $didik->apk_sma_smk_man],
            'tahun'   => $tahunDidik,
        ] : null;

        // ── 4. Kesehatan ──────────────────────────────────────────
        $tahunSehat = (int) DataKesehatan::max('tahun');
        $sehat      = $tahunSehat ? DataKesehatan::where('tahun', $tahunSehat)->first() : null;
        $faskes     = FasilitasKesehatanKecamatan::with('kecamatan')->where('tahun', $tahunSehat)->get();
        $nakes      = TenagaKesehatanKecamatan::with('kecamatan')->where('tahun', $tahunSehat)->get();
        if ($sehat) {
            $kartu[] = [
                'modul' => 'Kesehatan', 'route' => 'statistik.kesehatan', 'tahun' => $tahunSehat,
                'icon' => 'fa-plus-circle', 'warna' => 'ic-violet',
                'label' => 'Fasilitas Kesehatan',
                'nilai' => number_format((int) $faskes->sum('jumlah_total'), 0, ',', '.'),
                'satuan' => 'unit',
                'sub' => number_format((int) $nakes->sum('jumlah_total'), 0, ',', '.') . ' tenaga kesehatan',
            ];
        }

        // Komposisi fasilitas & tenaga kesehatan. Kolom nol dibuang supaya
        // potongan kosong tidak muncul di donut saat sebuah jenis belum diisi.
        $faskesJenis = collect([
            'Posyandu'   => (int) $faskes->sum('posyandu'),
            'Klinik'     => (int) $faskes->sum('klinik_kesehatan'),
            'Puskesmas'  => (int) $faskes->sum('puskesmas'),
            'Rumah Sakit'=> (int) $faskes->sum('rumah_sakit'),
        ])->filter()->sortDesc();

        $nakesJenis = collect([
            'Perawat'   => (int) $nakes->sum('perawat'),
            'Dokter'    => (int) $nakes->sum('dokter'),
            'Farmasi'   => (int) $nakes->sum('farmasi'),
            'Bidan'     => (int) $nakes->sum('bidan'),
            'Ahli Gizi' => (int) $nakes->sum('ahli_gizi'),
        ])->filter()->sortDesc();

        // ── 5. Kebencanaan ────────────────────────────────────────
        // Hanya rekap resmi Jakarta Barat (baris berperiode) yang dihitung,
        // sama seperti halaman modulnya.
        $rekapBencana  = fn() => DataBencana::whereNotNull('periode_data')
            ->where('wilayah', DataBencana::WILAYAH_JAKBAR);
        $tahunBencana  = (int) $rekapBencana()->max('tahun');
        $bencanaItems  = $tahunBencana ? $rekapBencana()->where('tahun', $tahunBencana)->get() : collect();
        $bencanaJenis  = $bencanaItems->groupBy('jenis_bencana')
            ->map(fn($rows) => (int) $rows->sum('jumlah_kejadian'))->sortDesc();
        if ($bencanaItems->isNotEmpty()) {
            $kartu[] = [
                'modul' => 'Kebencanaan', 'route' => 'statistik.bencana', 'tahun' => $tahunBencana,
                'icon' => 'fa-house-flood-water', 'warna' => 'ic-orange',
                'label' => 'Kejadian Bencana',
                'nilai' => number_format((int) $bencanaItems->sum('jumlah_kejadian'), 0, ',', '.'),
                'satuan' => 'kejadian',
                'sub' => 'terbanyak: ' . ($bencanaJenis->keys()->first() ?? '-'),
            ];
        }

        // ── 6. Kemiskinan ─────────────────────────────────────────
        $tahunMiskin = (int) DataKemiskinan::max('tahun');
        $miskin      = $tahunMiskin ? DataKemiskinan::where('tahun', $tahunMiskin)->first() : null;
        if ($miskin) {
            $prevMiskin = DataKemiskinan::where('tahun', $tahunMiskin - 1)->first();
            $kartu[] = [
                'modul' => 'Kemiskinan', 'route' => 'statistik.kemiskinan', 'tahun' => $tahunMiskin,
                'icon' => 'fa-hand-holding-heart', 'warna' => 'ic-red',
                'label' => 'Penduduk Miskin',
                'nilai' => number_format($miskin->persentase_penduduk_miskin, 2, ',', '.') . '%',
                'satuan' => '',
                'sub' => number_format($miskin->jumlah_penduduk_miskin, 0, ',', '.') . ' jiwa',
                // Kemiskinan turun = kabar baik, jadi arah trennya dibalik saat
                // diwarnai di view (lihat 'tren_baik').
                'tren' => $prevMiskin && $prevMiskin->persentase_penduduk_miskin > 0
                    ? round($miskin->persentase_penduduk_miskin - $prevMiskin->persentase_penduduk_miskin, 2)
                    : null,
                'tren_baik' => 'turun',
            ];
        }

        // ── 7. Perekonomian ───────────────────────────────────────
        $tahunEkon = (int) DataPerekonomian::max('tahun');
        $ekon      = $tahunEkon ? DataPerekonomian::where('tahun', $tahunEkon)->first() : null;
        if ($ekon) {
            $kartu[] = [
                'modul' => 'Perekonomian', 'route' => 'statistik.perekonomian', 'tahun' => $tahunEkon,
                'icon' => 'fa-sack-dollar', 'warna' => 'ic-green',
                'label' => 'PDRB Harga Berlaku',
                'nilai' => 'Rp ' . number_format($ekon->pdrb_adhb / 1000000, 2, ',', '.'),
                'satuan' => 'triliun',
                'sub' => 'pertumbuhan ' . number_format($ekon->laju_pertumbuhan, 2, ',', '.') . '%',
                'tren' => (float) $ekon->laju_pertumbuhan,
            ];
        }

        // ── 8. Infrastruktur digital ──────────────────────────────
        $tahunWifi = (int) JakWifiKecamatan::max('tahun');
        $tahunCctv = (int) CctvKecamatan::max('tahun');
        $wifi      = JakWifiKecamatan::with('kecamatan')->where('tahun', $tahunWifi)->get();
        $cctv      = CctvKecamatan::with('kecamatan')->where('tahun', $tahunCctv)->get();
        if ($wifi->isNotEmpty() || $cctv->isNotEmpty()) {
            $kartu[] = [
                'modul' => 'Infrastruktur Digital', 'route' => 'statistik.infrastruktur-digital',
                'tahun' => max($tahunWifi, $tahunCctv),
                'icon' => 'fa-wifi', 'warna' => 'ic-pink',
                'label' => 'Titik JakWiFi & CCTV',
                'nilai' => number_format((int) $wifi->sum('jumlah_titik') + (int) $cctv->sum('jumlah_unit'), 0, ',', '.'),
                'satuan' => 'unit',
                'sub' => number_format((int) $wifi->sum('jumlah_titik'), 0, ',', '.') . ' JakWiFi &middot; '
                       . number_format((int) $cctv->sum('jumlah_unit'), 0, ',', '.') . ' CCTV',
            ];
        }

        // ── Tabel lintas modul per kecamatan ──────────────────────
        // Setiap kolom berasal dari modul berbeda pada tahun terbarunya masing-
        // masing; dijahit lewat kecamatan_id agar tetap sinkron walau nama
        // kecamatan ditulis berbeda di sumber aslinya.
        $pendudukKec = PendudukKecamatan::where('tahun', $tahunPenduduk)->get()->keyBy('kecamatan_id');
        $miskinKec   = KemiskinanKecamatan::where('tahun', $tahunMiskin)->get()->keyBy('kecamatan_id');
        $luasById    = $luasKec->keyBy('kecamatan_id');

        $perKecamatan = \App\Models\Kecamatan::orderBy('nama_kecamatan')->get()->map(function ($kec) use (
            $luasById, $pendudukKec, $miskinKec, $didikKec, $faskes, $wifi, $cctv
        ) {
            $luas = optional($luasById->get($kec->id))->luas_km2;
            $jiwa = optional($pendudukKec->get($kec->id))->jumlah_penduduk;

            return [
                'nama'      => $kec->nama_kecamatan,
                'luas'      => $luas ? (float) $luas : null,
                'penduduk'  => $jiwa ? (int) $jiwa : null,
                'kepadatan' => ($jiwa && $luas) ? (int) round($jiwa / $luas) : null,
                'pelajar'   => (int) $didikKec->where('kecamatan_id', $kec->id)->sum('jumlah_pelajar') ?: null,
                'faskes'    => (int) $faskes->where('kecamatan_id', $kec->id)->sum('jumlah_total') ?: null,
                'miskin'    => optional($miskinKec->get($kec->id))->jumlah_penduduk_miskin,
                'digital'   => (int) $wifi->where('kecamatan_id', $kec->id)->sum('jumlah_titik')
                             + (int) $cctv->where('kecamatan_id', $kec->id)->sum('jumlah_unit') ?: null,
            ];
        })->filter(fn($r) => $r['penduduk'] || $r['luas'])->values();

        // ── Tren ekonomi vs kemiskinan ────────────────────────────
        // Dua modul, dua satuan; ditumpuk di satu grafik dua sumbu karena justru
        // hubungan keduanya yang menarik dilihat di halaman ringkasan.
        //
        // Rentangnya sengaja DIPOTONG ke tahun yang dipunyai KEDUA modul. Rentang
        // penuh PDRB (mulai 2019) membuat garis kemiskinan (mulai 2022) menggantung
        // dengan separuh sumbu kosong — terbaca seperti data hilang, padahal BPS
        // memang belum merilisnya. Lebih jujur menampilkan periode yang benar-benar
        // bisa dibandingkan.
        $trenGabungan = $this->trenEkonomiKemiskinan();

        return view('statistik.overview', compact(
            'kartu', 'perKecamatan', 'trenGabungan', 'bencanaJenis',
            'pendidikanIndikator', 'faskesJenis', 'nakesJenis'
        ));
    }

    /**
     * Deret PDRB harga konstan & persentase penduduk miskin pada irisan tahun
     * yang dimiliki kedua tabel. Mengembalikan labels kosong bila irisannya
     * kosong, sehingga view cukup memeriksa satu hal sebelum menggambar.
     */
    private function trenEkonomiKemiskinan(): array
    {
        $ekon   = DataPerekonomian::orderBy('tahun')->get(['tahun', 'pdrb_adhk'])->keyBy('tahun');
        $miskin = DataKemiskinan::orderBy('tahun')->get(['tahun', 'persentase_penduduk_miskin'])->keyBy('tahun');

        $tahun = $ekon->keys()->intersect($miskin->keys())->sort()->values();

        return [
            'labels' => $tahun->map(fn($t) => (string) $t)->values(),
            'pdrb'   => $tahun->map(fn($t) => round($ekon[$t]->pdrb_adhk / 1000000, 2))->values(),
            'miskin' => $tahun->map(fn($t) => (float) $miskin[$t]->persentase_penduduk_miskin)->values(),
        ];
    }

    public function geografis(Request $request)
    {
        $availableTahun = DataGeografis::query()
            ->select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        $tahun = (int) $request->get('tahun', $availableTahun->first() ?? date('Y'));
        if (!$availableTahun->contains($tahun)) {
            $tahun = (int) ($availableTahun->first() ?? $tahun);
        }

        $geo = DataGeografis::where('tahun', $tahun)->first();

        // Tanpa record induk tidak ada yang bisa dirangkai; tampilkan halaman
        // "belum ada data" daripada menabrak properti pada null.
        if (!$geo) {
            return $this->dataKosong('Geografis', $tahun, $availableTahun);
        }

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

        if (!$summary) {
            return $this->dataKosong('Kependudukan', $tahun, $availableTahun);
        }

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

        if (!$summary) {
            return $this->dataKosong('Pendidikan', $tahun, $availableTahun);
        }

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
        // Rekap triwulanan Jakarta Barat (cermin API Satu Data Jakarta)
        $rekapQuery = fn() => DataBencana::whereNotNull('periode_data')
            ->where('wilayah', DataBencana::WILAYAH_JAKBAR);

        $availableTahun = $rekapQuery()
            ->select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        $tahun = (int) $request->get('tahun', $availableTahun->first() ?? date('Y'));
        if ($availableTahun->isNotEmpty() && !$availableTahun->contains($tahun)) {
            $tahun = (int) $availableTahun->first();
        }

        $items = $rekapQuery()->where('tahun', $tahun)
            ->orderBy('periode_data')->orderBy('jenis_bencana')->get();

        // Perbandingan jenis bencana (donut): total kejadian per jenis
        $perJenis = $items->groupBy('jenis_bencana')
            ->map(fn($rows) => $rows->sum('jumlah_kejadian'))
            ->sortDesc();

        $ringkasan = [
            'total_kejadian'  => $items->sum('jumlah_kejadian'),
            'total_meninggal' => $items->sum('jumlah_korban_meninggal'),
            'total_luka'      => $items->sum('jumlah_korban_luka'),
            'jenis_terbanyak' => $perJenis->keys()->first() ?? '-',
        ];

        // Bar: jenis bencana per triwulan (tahun terpilih)
        $jenisSemua = $rekapQuery()->distinct()->orderBy('jenis_bencana')->pluck('jenis_bencana');
        $perTriwulan = [
            'labels' => ['TW1', 'TW2', 'TW3', 'TW4'],
            'series' => $jenisSemua->map(function ($jenis) use ($items) {
                $data = collect([1, 2, 3, 4])->map(function ($tw) use ($items, $jenis) {
                    return (int) $items->where('jenis_bencana', $jenis)->where('triwulan', $tw)->sum('jumlah_kejadian');
                });
                return ['name' => $jenis, 'data' => $data->values()];
            })->values(),
        ];

        // Tren: seluruh periode lintas tahun (maksimalkan rentang data)
        $semuaRekap = $rekapQuery()->orderBy('periode_data')->get();
        $trenGrup = $semuaRekap->groupBy('periode_data');
        $tren = [
            'labels' => $trenGrup->keys()->map(fn($p) => substr($p, 0, 4) . ' TW' . (DataBencana::triwulanDariPeriode($p) ?? '?'))->values(),
            'data'   => $trenGrup->map(fn($rows) => (int) $rows->sum('jumlah_kejadian'))->values(),
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
            'items', 'perJenis', 'ringkasan', 'tahun', 'availableTahun', 'warnaJenis',
            'kecamatanNames', 'titikBencana', 'tmaTitik', 'perTriwulan', 'tren'
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

    public function perekonomian(Request $request)
    {
        // Grafik tren memakai rentang tahun PENUH: arah jangka panjang (termasuk
        // jatuhnya 2020) baru terbaca kalau rentangnya tidak dipotong.
        $riwayat = DataPerekonomian::orderBy('tahun')->get();

        if ($riwayat->isEmpty()) {
            return $this->dataKosong('Perekonomian', (int) $request->get('tahun', date('Y')), collect());
        }

        // Selektor tahun & tabel ringkasan dibatasi beberapa tahun terakhir agar
        // halaman tidak padat. Jendelanya dihitung dari tahun TERBARU yang ada,
        // bukan dari tahun yang sedang dipilih, supaya isi dropdown tidak bergeser
        // setiap kali pengunjung berpindah tahun.
        $batasBawah   = $riwayat->max('tahun') - DataPerekonomian::TAHUN_DITAMPILKAN + 1;
        $riwayatTabel = $riwayat->where('tahun', '>=', $batasBawah)->values();

        $availableTahun = $riwayatTabel->pluck('tahun')->sortDesc()->values();

        $tahun = (int) $request->get('tahun', $availableTahun->first());
        if (!$availableTahun->contains($tahun)) {
            $tahun = (int) $availableTahun->first();
        }

        $summary = $riwayat->firstWhere('tahun', $tahun);

        if (!$summary) {
            return $this->dataKosong('Perekonomian', $tahun, $availableTahun);
        }

        // Seluruh 17 sektor tahun terpilih — dipakai utuh oleh grafik struktur
        // ekonomi sekaligus jadi basis agregasi tabel di bawahnya.
        $sektor = PdrbSektor::where('tahun', $tahun)
            ->orderByDesc('distribusi')
            ->get();

        // Tabel hanya menampilkan penyumbang terbesar; sisanya diringkas jadi satu
        // baris "Lainnya" supaya total tetap sama dengan PDRB.
        $sektorUtama   = $sektor->take(self::SEKTOR_DITAMPILKAN);
        $sektorLainnya = $this->ringkasSektorLainnya($sektor->slice(self::SEKTOR_DITAMPILKAN));

        // PDRB riil (ADHK) yang membandingkan tahun ke tahun tanpa efek inflasi,
        // dipakai untuk penanda naik/turun pada kartu ringkasan. Diambil dari
        // $riwayat (rentang penuh) agar tahun pembanding tetap ada walau berada
        // di luar jendela selektor tahun.
        $prev = $riwayat->firstWhere('tahun', $tahun - 1);
        $tren = ($prev && $prev->pdrb_adhk > 0)
            ? round(($summary->pdrb_adhk - $prev->pdrb_adhk) / $prev->pdrb_adhk * 100, 2)
            : null;

        // Selisih ADHB terhadap ADHK = akumulasi kenaikan harga sejak tahun dasar
        // 2010; ditampilkan sebagai indeks implisit (ADHB/ADHK × 100).
        $deflator = $summary->pdrb_adhk > 0
            ? round($summary->pdrb_adhb / $summary->pdrb_adhk * 100, 2)
            : null;

        return view('statistik.perekonomian', compact(
            'summary', 'riwayat', 'riwayatTabel', 'sektor', 'sektorUtama', 'sektorLainnya',
            'tren', 'deflator', 'tahun', 'availableTahun'
        ));
    }

    /** Banyaknya sektor yang tampil satu per satu di tabel; sisanya jadi "Lainnya". */
    private const SEKTOR_DITAMPILKAN = 7;

    /**
     * Gabungkan sektor-sektor kecil jadi satu baris agar total tabel tetap sama
     * dengan PDRB. ADHB & distribusi tinggal dijumlahkan, tetapi laju pertumbuhan
     * TIDAK bisa dijumlahkan — dipakai rata-rata tertimbang menurut ADHB, yaitu
     * pendekatan atas pertumbuhan gabungan, bukan angka resmi BPS.
     *
     * Mengembalikan null bila tidak ada sisa sektor, sehingga view cukup
     * memeriksa satu nilai untuk memutuskan menampilkan barisnya.
     */
    private function ringkasSektorLainnya($sisa): ?array
    {
        if ($sisa->isEmpty()) {
            return null;
        }

        $adhb = $sisa->sum('adhb');

        return [
            'jumlah_sektor'    => $sisa->count(),
            'adhb'             => $adhb,
            'distribusi'       => $sisa->sum('distribusi'),
            'laju_pertumbuhan' => $adhb > 0
                ? $sisa->sum(fn($s) => $s->adhb * $s->laju_pertumbuhan) / $adhb
                : null,
        ];
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
