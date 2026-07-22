<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\Statistik\BpsClient;
use App\Models\DataKemiskinan;
use App\Models\KemiskinanKecamatan;
use App\Models\Kecamatan;

class KemiskinanSeeder extends Seeder
{
    /**
     * Ringkasan kemiskinan diambil LANGSUNG dari BPS WebAPI (domain 3174 = Kota
     * Jakarta Barat, var 117 = Indikator Kemiskinan, tahun 122=2022 & 123=2023).
     * BPS hanya merilis sampai level kota, jadi hanya baris Jakarta Barat (vervar=5)
     * yang diambil. Rincian per-kecamatan tidak tersedia di BPS → didistribusikan
     * sebagai ESTIMASI proporsional agar visualisasi per-kecamatan tetap berfungsi.
     */
    public function run(): void
    {
        $summaries = $this->fetchBps();

        if (empty($summaries)) {
            $this->command?->warn('KemiskinanSeeder: gagal mengambil data BPS, dilewati.');
            return;
        }

        // Tidak ada truncate: seeder ini juga dipakai tombol "Sinkronkan BPS" di
        // portal, dan mengosongkan tabel akan melenyapkan tahun yang diisi
        // manual operator. Baris dicocokkan per tahun lewat updateOrCreate.

        /* ── ESTIMASI PER-KECAMATAN (DINONAKTIFKAN) ───────────────────────────
           BPS hanya merilis kemiskinan sampai level kota, tidak ada rincian per
           kecamatan. Distribusi di bawah hanya ESTIMASI proporsional, sehingga
           dinonaktifkan agar aplikasi menampilkan data BPS asli saja.
           Disimpan (tidak dihapus) bila sewaktu-waktu ada sumber per-kecamatan.

        $bobot = [
            'Cengkareng'        => 0.2008,
            'Kalideres'         => 0.1650,
            'Tambora'           => 0.1497,
            'Kembangan'         => 0.1096,
            'Kebon Jeruk'       => 0.1020,
            'Palmerah'          => 0.0998,
            'Grogol Petamburan' => 0.0944,
            'Taman Sari'        => 0.0787,
        ];
        ──────────────────────────────────────────────────────────────────────── */

        foreach ($summaries as $tahun => $ind) {
            // 58 = Jumlah Penduduk Miskin (ribu orang) → dikonversi ke jiwa
            $totalMiskin = (int) round(($ind[58] ?? 0) * 1000);

            DataKemiskinan::updateOrCreate(
                ['tahun' => $tahun],
                [
                    'jumlah_penduduk_miskin'     => $totalMiskin,
                    'persentase_penduduk_miskin' => $ind[59] ?? 0,   // Persentase (%)
                    'garis_kemiskinan'           => $ind[60] ?? 0,   // Garis Kemiskinan (Rp/kapita/bulan)
                    'indeks_kedalaman'           => $ind[61] ?? 0,   // P1
                    'indeks_keparahan'           => $ind[62] ?? 0,   // P2
                    'sumber'                     => 'BPS Kota Jakarta Barat (webapi.bps.go.id), var 117',
                ],
            );

            /* ── Estimasi distribusi per-kecamatan (DINONAKTIFKAN, lihat catatan di atas) ──
            foreach ($bobot as $nama => $w) {
                $kec = Kecamatan::where('nama_kecamatan', $nama)->first();
                if (! $kec) {
                    continue;
                }
                $miskinKec = (int) round($totalMiskin * $w);
                KemiskinanKecamatan::create([
                    'kecamatan_id'           => $kec->id,
                    'tahun'                  => $tahun,
                    'jumlah_penduduk_miskin' => $miskinKec,
                    'jumlah_keluarga_miskin' => (int) round($miskinKec / 3.5),   // asumsi ~3,5 jiwa/KK
                    'penerima_bantuan'       => (int) round($miskinKec * 1.1),   // cakupan bansos ~110%
                    // Persentase kecamatan ≈ persentase kota disesuaikan bobot relatif
                    'persentase'             => round(($ind[59] ?? 0) * ($w / 0.125), 2),
                ]);
            }
            ──────────────────────────────────────────────────────────────────────── */
        }

        $this->command?->info('KemiskinanSeeder: ' . count($summaries) . ' tahun diambil dari BPS ('
            . implode(', ', array_keys($summaries)) . ').');
    }

    /**
     * Kode tahun BPS → tahun asli. Tambahkan entri baru di sini untuk menarik tahun
     * lain (mis. '126' => 2026) begitu BPS merilisnya — dashboard otomatis ikut.
     * Catatan: BPS kadang error bila banyak tahun digabung dalam satu request,
     * jadi tiap tahun di-fetch terpisah lalu digabung (merge).
     */
    private const TAHUN_BPS = [
        '122' => 2022,
        '123' => 2023,
        '124' => 2024,
        '125' => 2025,
    ];

    /**
     * Ambil & parse BPS WebAPI per tahun, gabungkan. Mengembalikan
     * [tahun => [turvar => nilai]] untuk Jakarta Barat.
     * Key datacontent BPS berformat: {vervar}{var}{turvar}{tahun}{turtahun}.
     */
    private function fetchBps(): array
    {
        $bps          = app(BpsClient::class);
        $vervarJakbar = 5;                 // Jakarta Barat (dari daftar vervar BPS)
        $turvars      = [58, 59, 60, 61, 62];

        $out = [];
        foreach (self::TAHUN_BPS as $thKode => $thLabel) {
            // Tahun yang gagal/belum rilis kembali sebagai [] → dilewati,
            // tahun lain tetap jalan.
            $dc = $bps->datacontent(var: 117, thKode: $thKode);

            foreach ($turvars as $tv) {
                // 5 . 117 . {turvar} . {tahun} . 0
                $key = $vervarJakbar . '117' . $tv . $thKode . '0';
                if (isset($dc[$key])) {
                    $out[$thLabel][$tv] = $dc[$key];
                }
            }
        }

        return $out;
    }
}
