<?php

namespace Database\Seeders;

use App\Models\DataPerekonomian;
use App\Models\PdrbSektor;
use App\Services\Statistik\BpsClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * PDRB Kota Jakarta Barat, langsung dari BPS WebAPI (domain 3174).
 *
 * Empat variabel dipakai bersama karena berbagi struktur baris yang sama
 * (vervar 1–17 = kategori lapangan usaha, vervar 18 = PDRB total):
 *
 *   var 42 = PDRB ADHB (Juta Rupiah)      var 58 = Laju Pertumbuhan Ekonomi (%)
 *   var 52 = PDRB ADHK 2010 (Juta Rupiah) var 60 = Distribusi Persentase PDRB (%)
 *
 * BPS hanya merilis PDRB sampai level KOTA — tidak ada rincian per kecamatan,
 * jadi modul ini memang tidak punya tabel/peta per kecamatan.
 *
 * Varian triwulanan (var 448–476) sengaja TIDAK dipakai: metadata tahunnya ada
 * (bahkan 2025/2026) tetapi datacontent-nya kosong.
 */
class PerekonomianSeeder extends Seeder
{
    /**
     * Kode tahun BPS → tahun asli. Tambahkan entri baru di sini begitu BPS
     * merilis tahun berikutnya ('125' => 2025) — dashboard otomatis ikut.
     */
    private const TAHUN_BPS = [
        '119' => 2019,
        '120' => 2020,
        '121' => 2021,
        '122' => 2022,
        '123' => 2023,
        '124' => 2024,
    ];

    /** var BPS => kolom tujuan. vervar & turvar identik di keempatnya. */
    private const VAR_BPS = [
        42 => 'adhb',
        52 => 'adhk',
        58 => 'laju_pertumbuhan',
        60 => 'distribusi',
    ];

    private const VERVAR_TOTAL = 18;   // baris "PDRB" (bukan sektor)

    private const SUMBER = 'BPS Kota Jakarta Barat (webapi.bps.go.id), var 42/52/58/60';

    public function run(): void
    {
        $tahunan = $this->fetchBps();

        // Satu tahun pun gagal berarti data tidak utuh: batalkan TANPA truncate
        // supaya isi tabel lama tetap ada, bukan berganti data setengah jadi.
        if ($tahunan === null) {
            $this->command?->warn('PerekonomianSeeder: pengambilan BPS gagal, data lama dipertahankan.');
            return;
        }

        if (empty($tahunan)) {
            $this->command?->warn('PerekonomianSeeder: BPS tidak mengembalikan data, dilewati.');
            return;
        }

        // Tidak lagi mengosongkan tabel lebih dulu: seeder ini juga dipakai
        // tombol "Sinkronkan BPS" di portal, dan menghapus-lalu-isi akan
        // melenyapkan tahun yang diinput manual operator. Baris dicocokkan per
        // tahun (ringkasan) dan per tahun+kode_sektor (rincian).
        //
        // Transaksinya dipertahankan supaya kegagalan di tengah tidak
        // menyisakan sebagian tahun sudah diperbarui dan sisanya belum.
        DB::transaction(function () use ($tahunan) {
            foreach ($tahunan as $tahun => $isi) {
                DataPerekonomian::updateOrCreate(
                    ['tahun' => $tahun],
                    [
                        'pdrb_adhb'        => $isi['total']['adhb'],
                        'pdrb_adhk'        => $isi['total']['adhk'],
                        'laju_pertumbuhan' => $isi['total']['laju_pertumbuhan'],
                        'sumber'           => self::SUMBER,
                    ],
                );

                foreach ($isi['sektor'] as $kode => $s) {
                    PdrbSektor::updateOrCreate(
                        ['tahun' => $tahun, 'kode_sektor' => $kode],
                        [
                            'kategori'         => $s['kategori'],
                            'nama_sektor'      => $s['nama_sektor'],
                            // ADHK sengaja tidak disimpan per sektor — hanya tingkat
                            // kota yang dipakai (lihat migration drop_adhk_from_pdrb_sektor).
                            'adhb'             => $s['adhb'],
                            'distribusi'       => $s['distribusi'],
                            'laju_pertumbuhan' => $s['laju_pertumbuhan'],
                        ],
                    );
                }
            }
        });

        $this->command?->info('PerekonomianSeeder: ' . count($tahunan) . ' tahun PDRB dari BPS ('
            . implode(', ', array_keys($tahunan)) . ').');
    }

    /**
     * Ambil keempat variabel per tahun (request terpisah — menggabung banyak
     * tahun dalam satu request kadang ditolak BPS), lalu susun jadi
     * [tahun => ['total' => [...], 'sektor' => [kode => [...]]]].
     *
     * Mengembalikan null bila ada request yang gagal, agar pemanggil bisa
     * membedakan "gagal ambil" dari "BPS memang kosong".
     */
    private function fetchBps(): ?array
    {
        $bps = app(BpsClient::class);
        $out = [];

        foreach (self::TAHUN_BPS as $thKode => $tahun) {
            $nilai  = [];   // [vervar => [kolom => nilai]]
            $label  = [];   // [vervar => label sektor]

            foreach (self::VAR_BPS as $var => $kolom) {
                $set = $bps->dataset($var, $thKode);

                if (empty($set['datacontent'])) {
                    $this->command?->warn("PerekonomianSeeder: var {$var} tahun {$tahun} kosong/gagal.");
                    return null;
                }

                $label += $set['vervar'];

                foreach ($set['vervar'] as $vervar => $_) {
                    // Key datacontent: {vervar}{var}{turvar}{th}{turth}, turvar & turth = 0
                    $key = $vervar . $var . '0' . $thKode . '0';
                    if (isset($set['datacontent'][$key])) {
                        $nilai[$vervar][$kolom] = (float) $set['datacontent'][$key];
                    }
                }
            }

            $tahunIni = $this->susunTahun($nilai, $label, $tahun);
            if ($tahunIni === null) {
                return null;
            }

            $out[$tahun] = $tahunIni;
        }

        return $out;
    }

    /**
     * Validasi & rapikan satu tahun: baris total harus lengkap, dan tiap sektor
     * wajib punya nilai PDRB — baris setengah isi lebih menyesatkan daripada
     * tidak ditampilkan.
     *
     * Perkecualian sektor bernilai NOL (di Jakarta Barat: Pertambangan dan
     * Penggalian). BPS menghilangkan distribusi & laju pertumbuhannya pada
     * sebagian tahun karena 0/0 tak terdefinisi, lalu mengirim 0 pada tahun
     * lain. Keduanya dilengkapi jadi 0 supaya daftar sektor tetap 17 di semua
     * tahun — kalau tidak, sektornya hilang-timbul antar tahun.
     */
    private function susunTahun(array $nilai, array $label, int $tahun): ?array
    {
        $total = $nilai[self::VERVAR_TOTAL] ?? [];

        foreach (['adhb', 'adhk', 'laju_pertumbuhan'] as $wajib) {
            if (!isset($total[$wajib])) {
                $this->command?->warn("PerekonomianSeeder: baris PDRB total tahun {$tahun} tidak lengkap.");
                return null;
            }
        }

        $sektor = [];
        foreach ($nilai as $vervar => $baris) {
            // ADHK tidak ikut disyaratkan: per sektor angka itu tidak disimpan.
            if ($vervar === self::VERVAR_TOTAL || !isset($baris['adhb'])) {
                continue;
            }

            foreach (['distribusi', 'laju_pertumbuhan'] as $turunan) {
                if (isset($baris[$turunan])) {
                    continue;
                }
                if ($baris['adhb'] != 0.0) {
                    // Sektor berisi tapi angka turunannya hilang: benar-benar tak lengkap.
                    continue 2;
                }
                $baris[$turunan] = 0.0;
            }

            [$kategori, $nama] = $this->pisahKategori($label[$vervar] ?? '');

            $sektor[$vervar] = $baris + ['kategori' => $kategori, 'nama_sektor' => $nama];
        }

        if (empty($sektor)) {
            $this->command?->warn("PerekonomianSeeder: tidak ada sektor lengkap pada tahun {$tahun}.");
            return null;
        }

        return ['total' => $total, 'sektor' => $sektor];
    }

    /**
     * Label BPS berbentuk "A. Pertanian Kehutanan, dan Perikanan" atau
     * "M,N. Jasa Perusahaan" — huruf kategori dipisah dari nama sektor.
     * Bila polanya tak dikenali, seluruh label dipakai sebagai nama.
     */
    private function pisahKategori(string $label): array
    {
        if (preg_match('/^([A-Z][A-Z,]*)\.\s*(.+)$/u', trim($label), $m)) {
            return [$m[1], trim($m[2])];
        }

        return ['', trim($label)];
    }
}
