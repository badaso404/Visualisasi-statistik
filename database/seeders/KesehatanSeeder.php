<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\Statistik\BpsClient;
use App\Models\DataKesehatan;
use App\Models\TenagaKesehatanKecamatan;
use App\Models\FasilitasKesehatanKecamatan;
use App\Models\Kecamatan;

class KesehatanSeeder extends Seeder
{
    /**
     * Data kesehatan diambil LANGSUNG dari BPS WebAPI (domain 3174 = Jakarta Barat):
     *   - var 129 → Tenaga Kesehatan per kecamatan (Dokter, Perawat, Bidan, Farmasi, Ahli Gizi)
     *   - var 128 → Fasilitas Kesehatan per kecamatan (RS Umum/Khusus/Bersalin, Puskesmas, Klinik, Posyandu, Polindes)
     *   - var 221 (RS Umum) + var 222 (RS Khusus) → total Tempat Tidur RS (baris "Jumlah" = Jakarta Barat)
     *
     * BPS memecah var 128/129 per KECAMATAN (vervar 1-8) + total Jakarta Barat (vervar 9).
     * Tahun dideteksi OTOMATIS dari BPS (union var 128 & 129) lalu disaring >= MIN_TAHUN.
     * Jadi begitu BPS merilis tahun baru, cukup jalankan `db:seed --class=KesehatanSeeder`
     * dan tahun itu ikut tampil tanpa mengubah kode. Tempat tidur RS (221/222) hanya ada
     * di sebagian tahun (0 bila tak tersedia). Cakupan imunisasi tidak ada di BPS → null.
     */
    private const MIN_TAHUN = 2022;

    // vervar BPS (var 128 & 129) → nama kecamatan di DB
    private const KEC = [
        1 => 'Kembangan',
        2 => 'Kebon Jeruk',
        3 => 'Palmerah',
        4 => 'Grogol Petamburan',
        5 => 'Tambora',
        6 => 'Taman Sari',
        7 => 'Cengkareng',
        8 => 'Kalideres',
    ];

    public function run(): void
    {
        $kecamatanId = Kecamatan::pluck('id', 'nama_kecamatan'); // nama => id
        $adaData = false;

        // Tahun dideteksi otomatis dari BPS (>= MIN_TAHUN)
        $tahunList = $this->fetchTahun();
        if (empty($tahunList)) {
            $this->command?->warn('KesehatanSeeder: gagal ambil daftar tahun BPS, dilewati.');
            return;
        }

        // Tanpa truncate: seeder ini juga dipakai tombol "Sinkronkan BPS" di
        // portal, dan mengosongkan tabel akan melenyapkan baris yang ditambal
        // manual operator. Semua penulisan di bawah memakai updateOrCreate
        // dengan kunci (kecamatan, tahun) atau (tahun) untuk ringkasan.

        foreach ($tahunList as $thKode => $tahun) {
            $dc129 = $this->fetchDatacontent(129, $thKode); // tenaga
            $dc128 = $this->fetchDatacontent(128, $thKode); // fasilitas
            $dc221 = $this->fetchDatacontent(221, $thKode); // RS umum (tempat tidur)
            $dc222 = $this->fetchDatacontent(222, $thKode); // RS khusus (tempat tidur)

            // Lewati tahun yang benar-benar kosong (mis. API gagal total)
            if (empty($dc129) && empty($dc128)) {
                continue;
            }
            $adaData = true;

            foreach (self::KEC as $vv => $nama) {
                $kecId = $kecamatanId[$nama] ?? null;
                if (! $kecId) {
                    continue;
                }

                // ── Tenaga kesehatan (var 129): key = {vervar}129{turvar}{th}0 ──
                if (! empty($dc129)) {
                    $dokter   = (int) ($dc129["{$vv}12980{$thKode}0"] ?? 0);
                    $perawat  = (int) ($dc129["{$vv}12981{$thKode}0"] ?? 0);
                    $bidan    = (int) ($dc129["{$vv}12982{$thKode}0"] ?? 0);
                    $farmasi  = (int) ($dc129["{$vv}12983{$thKode}0"] ?? 0);
                    $ahliGizi = (int) ($dc129["{$vv}12984{$thKode}0"] ?? 0);
                    $totalTenaga = $dokter + $perawat + $bidan + $farmasi + $ahliGizi;

                    if ($totalTenaga > 0) {
                        TenagaKesehatanKecamatan::updateOrCreate(
                            ['kecamatan_id' => $kecId, 'tahun' => $tahun],
                            [
                                'jumlah_total' => $totalTenaga,
                                'dokter'       => $dokter,
                                'perawat'      => $perawat,
                                'bidan'        => $bidan,
                                'ahli_gizi'    => $ahliGizi,
                                'farmasi'      => $farmasi,
                            ],
                        );
                    }
                }

                // ── Fasilitas kesehatan (var 128): key = {vervar}128{turvar}{th}0 ──
                if (! empty($dc128)) {
                    $rsu      = (int) ($dc128["{$vv}12885{$thKode}0"] ?? 0);
                    $rsk      = (int) ($dc128["{$vv}12886{$thKode}0"] ?? 0);
                    $rsb      = (int) ($dc128["{$vv}12887{$thKode}0"] ?? 0);
                    $pusk     = (int) ($dc128["{$vv}12888{$thKode}0"] ?? 0);
                    $klinik   = (int) ($dc128["{$vv}12889{$thKode}0"] ?? 0);
                    $posyandu = (int) ($dc128["{$vv}12890{$thKode}0"] ?? 0);
                    $polindes = (int) ($dc128["{$vv}12891{$thKode}0"] ?? 0);

                    $rumahSakit = $rsu + $rsk + $rsb; // RS = Umum + Khusus + Bersalin
                    $total = $rumahSakit + $pusk + $klinik + $posyandu + $polindes;

                    if ($total > 0) {
                        FasilitasKesehatanKecamatan::updateOrCreate(
                            ['kecamatan_id' => $kecId, 'tahun' => $tahun],
                            [
                                'jumlah_total'     => $total,
                                'klinik_kesehatan' => $klinik,
                                'posyandu'         => $posyandu,
                                'puskesmas'        => $pusk,
                                'rumah_sakit'      => $rumahSakit,
                            ],
                        );
                    }
                }
            }

            // ── Ringkasan kota: total tempat tidur RS Umum + RS Khusus ──
            // Baris "Jumlah" Jakarta Barat = vervar 25, turvar 210 (Tempat Tidur).
            $ttUmum   = (int) ($dc221["25221210{$thKode}0"] ?? 0);
            $ttKhusus = (int) ($dc222["25222210{$thKode}0"] ?? 0);

            // cakupan_imunisasi_dasar sengaja TIDAK ikut ditulis: BPS WebAPI
            // tidak menyediakannya, jadi kalau operator mengisinya manual,
            // sinkronisasi tidak boleh menimpanya kembali jadi null.
            DataKesehatan::updateOrCreate(
                ['tahun' => $tahun],
                [
                    'jumlah_tempat_tidur_rs' => $ttUmum + $ttKhusus,
                    'sumber'                 => 'BPS Kota Jakarta Barat (webapi.bps.go.id) — var 128/129/221/222',
                ],
            );
        }

        if (! $adaData) {
            $this->command?->warn('KesehatanSeeder: gagal mengambil data BPS, dilewati.');
            return;
        }

        $this->command?->info('KesehatanSeeder: data BPS ' . implode(', ', $tahunList) . ' berhasil dimuat.');
    }

    /**
     * Deteksi tahun yang tersedia di BPS (union var 128 & 129), saring >= MIN_TAHUN.
     * Mengembalikan [th_kode(string) => tahun(int)] terurut menaik. [] bila gagal.
     */
    private function fetchTahun(): array
    {
        $bps   = app(BpsClient::class);
        $tahun = [];

        foreach ([128, 129] as $var) {
            $tahun += $bps->tahunTersedia(var: $var, minTahun: self::MIN_TAHUN);
        }

        asort($tahun);
        return $tahun;
    }

    /**
     * Ambil datacontent BPS untuk satu variabel & satu tahun. Kembalikan [] bila
     * gagal / tahun tak tersedia. Key datacontent = {vervar}{var}{turvar}{th}{turth}.
     */
    private function fetchDatacontent(int $var, string $thKode): array
    {
        return app(BpsClient::class)->datacontent(var: $var, thKode: $thKode);
    }
}
