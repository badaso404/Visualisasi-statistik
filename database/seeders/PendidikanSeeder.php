<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\Statistik\BpsClient;
use App\Models\DataPendidikan;
use App\Models\PendidikanKecamatan;
use App\Models\Kecamatan;

class PendidikanSeeder extends Seeder
{
    /**
     * Seluruh data pendidikan diambil dari BPS WebAPI (domain 3174 = Kota Jakarta Barat):
     *
     *  - Ringkasan APM/APK  : var 202 (level kota).
     *  - Rincian kecamatan  : var jumlah sekolah/guru/murid per jenjang, yang di BPS
     *                         memang dipecah per kecamatan (vervar) dan per status
     *                         negeri/swasta (turvar). Nilai 7 jenjang dijumlahkan.
     *
     * Data yang tampil di dashboard tetap dibaca dari DATABASE — seeder ini hanya
     * pengisi/penyegar isinya.
     */
    public function run(): void
    {
        $this->seedRingkasan();
        $this->seedPerKecamatan();
    }

    /* ── Kode tahun BPS → tahun asli ──────────────────────────────────────────
       Tambahkan entri baru begitu BPS merilisnya — dashboard otomatis ikut.
       Catatan: BPS kadang error bila banyak tahun digabung dalam satu request,
       jadi tiap tahun di-fetch terpisah lalu digabung.                        */

    /**
     * Dashboard dibatasi 5 tahun terakhir → mulai 2022. BPS sebenarnya menyimpan
     * APM/APK sejak 2018, tapi tahun lama tidak lagi relevan. 125/126 disiapkan
     * untuk rilis berikutnya — begitu terbit, otomatis ikut tertarik.
     */
    private const TAHUN_RINGKASAN = [
        '122' => 2022, '123' => 2023, '124' => 2024, '125' => 2025, '126' => 2026,
    ];

    /** var sekolah/guru/murid: tersedia 2022–2024. */
    private const TAHUN_KECAMATAN = [
        '122' => 2022, '123' => 2023, '124' => 2024, '125' => 2025, '126' => 2026,
    ];

    /* ── Kode dimensi BPS ──────────────────────────────────────────────────── */

    /** turvar var 202: jenis angka partisipasi */
    private const TURVAR_APM = 192;
    private const TURVAR_APK = 193;

    /** vervar var 202: jenjang → sufiks kolom data_pendidikan */
    private const JENJANG_APM_APK = [
        1 => 'sd_mi',
        2 => 'smp_mts',
        3 => 'sma_smk_man',
    ];

    /** vervar var sekolah/guru/murid: kecamatan (9 = Jakarta Barat, tidak dipakai) */
    private const VERVAR_KECAMATAN = [
        1 => 'Kembangan',
        2 => 'Kebon Jeruk',
        3 => 'Palmerah',
        4 => 'Grogol Petamburan',
        5 => 'Tambora',
        6 => 'Taman Sari',
        7 => 'Cengkareng',
        8 => 'Kalideres',
    ];

    /** turvar var sekolah/guru/murid: status sekolah */
    private const TURVAR_NEGERI = 181;
    private const TURVAR_SWASTA = 182;
    private const TURVAR_JUMLAH = 183;

    /** 7 jenjang: SD, MI, SMP, MTs, SMA, SMK, MA (Kemendikbud + Kemenag) */
    private const VAR_SEKOLAH = [179, 183, 186, 189, 192, 195, 198];
    private const VAR_GURU    = [181, 184, 187, 190, 193, 196, 199];
    private const VAR_MURID   = [182, 185, 188, 191, 194, 197, 200];

    private const SUMBER = 'BPS Kota Jakarta Barat (webapi.bps.go.id)';

    /**
     * Ringkasan APM & APK (var 202). Memakai updateOrCreate (bukan truncate) supaya
     * tahun yang diinput manual lewat portal admin tidak ikut terhapus.
     */
    private function seedRingkasan(): void
    {
        $bps = app(BpsClient::class);
        $n   = 0;

        foreach (self::TAHUN_RINGKASAN as $thKode => $thLabel) {
            // Tahun yang gagal/belum rilis kembali sebagai [] → dilewati.
            $dc    = $bps->datacontent(var: 202, thKode: $thKode);
            $baris = [];

            foreach (self::JENJANG_APM_APK as $vervar => $sufiks) {
                foreach ([self::TURVAR_APM => 'apm', self::TURVAR_APK => 'apk'] as $turvar => $prefix) {
                    $key = $vervar . '202' . $turvar . $thKode . '0';
                    if (isset($dc[$key])) {
                        $baris["{$prefix}_{$sufiks}"] = $dc[$key];
                    }
                }
            }

            // Hanya simpan bila 6 nilai (3 jenjang x APM/APK) lengkap
            if (count($baris) !== 6) {
                continue;
            }

            DataPendidikan::updateOrCreate(
                ['tahun' => $thLabel],
                $baris + ['sumber' => self::SUMBER . ', var 202'],
            );
            $n++;
        }

        $this->command?->info("PendidikanSeeder: ringkasan APM/APK — {$n} tahun dari BPS.");
    }

    /**
     * Rincian per kecamatan: jumlah sekolah negeri/swasta, guru, dan murid —
     * dijumlahkan dari 7 jenjang (SD, MI, SMP, MTs, SMA, SMK, MA).
     */
    private function seedPerKecamatan(): void
    {
        $bps    = app(BpsClient::class);
        $kecMap = Kecamatan::pluck('id', 'nama_kecamatan');
        $n      = 0;

        foreach (self::TAHUN_KECAMATAN as $thKode => $thLabel) {
            // Ambil sekali per var, lalu dipakai untuk semua kecamatan.
            $sekolah = $this->ambilDatacontent($bps, self::VAR_SEKOLAH, $thKode);
            $guru    = $this->ambilDatacontent($bps, self::VAR_GURU, $thKode);
            $murid   = $this->ambilDatacontent($bps, self::VAR_MURID, $thKode);

            if (empty($sekolah)) {
                $this->command?->warn("PendidikanSeeder: data kecamatan {$thLabel} tidak tersedia, dilewati.");
                continue;
            }

            foreach (self::VERVAR_KECAMATAN as $vervar => $nama) {
                $kecId = $kecMap[$nama] ?? null;
                if (! $kecId) {
                    $this->command?->warn("PendidikanSeeder: kecamatan '{$nama}' tidak ada di tabel kecamatan.");
                    continue;
                }

                PendidikanKecamatan::updateOrCreate(
                    ['kecamatan_id' => $kecId, 'tahun' => $thLabel],
                    [
                        'jumlah_sekolah_negeri' => $this->jumlahkan($sekolah, $vervar, self::TURVAR_NEGERI, $thKode),
                        'jumlah_sekolah_swasta' => $this->jumlahkan($sekolah, $vervar, self::TURVAR_SWASTA, $thKode),
                        'jumlah_pendidik'       => $this->jumlahkan($guru, $vervar, self::TURVAR_JUMLAH, $thKode),
                        'jumlah_pelajar'        => $this->jumlahkan($murid, $vervar, self::TURVAR_JUMLAH, $thKode),
                    ],
                );
                $n++;
            }
        }

        $this->command?->info("PendidikanSeeder: rincian kecamatan — {$n} baris dari BPS.");
    }

    /**
     * Ambil datacontent beberapa var sekaligus untuk satu tahun.
     * Mengembalikan [var => datacontent].
     */
    private function ambilDatacontent(BpsClient $bps, array $vars, string $thKode): array
    {
        $out = [];
        foreach ($vars as $var) {
            $dc = $bps->datacontent(var: $var, thKode: $thKode);
            if (! empty($dc)) {
                $out[$var] = $dc;
            }
        }

        return $out;
    }

    /**
     * Jumlahkan satu kecamatan (vervar) + status (turvar) lintas semua jenjang.
     * Key datacontent BPS berformat: {vervar}{var}{turvar}{tahun}{turtahun}.
     */
    private function jumlahkan(array $perVar, int $vervar, int $turvar, string $thKode): int
    {
        $total = 0;
        foreach ($perVar as $var => $dc) {
            $key = $vervar . $var . $turvar . $thKode . '0';
            $total += (int) ($dc[$key] ?? 0);
        }

        return $total;
    }
}
