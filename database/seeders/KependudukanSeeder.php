<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\DataKependudukan;
use App\Models\PendudukKecamatan;
use App\Models\PendudukKelurahan;
use App\Models\Kecamatan;

/**
 * Sumber tunggal: BPS WebAPI var 162 "Jumlah Penduduk menurut Kelurahan dan
 * Jenis Kelamin" (domain 3174 = Jakarta Barat).
 *
 * Prinsip konsistensi: KELURAHAN = sumber kebenaran (L/P asli BPS).
 * KECAMATAN & JAKARTA BARAT diturunkan (SUM) dari kelurahan → tidak mungkin
 * timpang. Koordinat lat/lng TIDAK ada di BPS → di-merge dari referensi
 * stabil database/data/koordinat-kelurahan.csv (ter-commit, aman dari reseed).
 */
class KependudukanSeeder extends Seeder
{
    private const BPS_DOMAIN = '3174';
    private const BPS_VAR    = 162;
    private const BPS_KEY    = '3b5c29cc3428ab819b851b3676e0063a';
    private const TURVAR_L   = 27;   // Laki-laki
    private const TURVAR_P   = 28;   // Perempuan
    private const TURVAR_LP  = 29;   // Laki-laki + Perempuan (total; lebih andal, dipakai sbg patokan)

    /**
     * th_id BPS => tahun. Hanya tahun yang datanya bersih/andal.
     * 2023 (th 123) SENGAJA dilewati: 17/56 kelurahan korup di sumber BPS
     * (pola "digit hilang" acak pada kolom L/P/total) → tak bisa dipulihkan
     * dengan kredibel. 2019 bersih 100%, 2024 korup 4 sel tapi total tetap
     * andal & terkoreksi otomatis (lihat logika fetchKelurahan).
     */
    private const TAHUN_BPS = [119 => 2019, 124 => 2024];

    private const UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 '
                     . '(KHTML, like Gecko) Chrome/120.0 Safari/537.36';

    public function run(): void
    {
        // 1) Referensi koordinat (norm(nama) => baris)
        $koordinat = $this->loadKoordinat();
        if (empty($koordinat)) {
            $this->command?->error('database/data/koordinat-kelurahan.csv tidak ditemukan/ kosong. Batal.');
            return;
        }

        // 2) Ambil SEMUA tahun dari BPS DULU. Kalau ada yang gagal → batal tanpa truncate.
        $perTahun = [];
        foreach (self::TAHUN_BPS as $thId => $tahun) {
            $rows = $this->fetchKelurahan($thId, $koordinat);
            if ($rows === null) {
                $this->command?->error("Gagal ambil BPS tahun $tahun (th $thId). Data lama TIDAK diubah.");
                return;
            }
            $perTahun[$tahun] = $rows;
            $this->command?->info("BPS $tahun: " . count($rows) . ' kelurahan diambil.');
        }

        // 3) Semua sukses → tulis. Tanpa truncate: seeder ini juga dipakai
        //    tombol "Sinkronkan BPS" di portal, dan mengosongkan tabel akan
        //    melenyapkan baris yang ditambal manual operator — termasuk
        //    koordinat kelurahan yang tidak disediakan BPS.

        $kecamatanId = Kecamatan::pluck('id', 'nama_kecamatan'); // nama => id

        foreach ($perTahun as $tahun => $rows) {
            $aggKec = [];   // kecamatan => [L, P]
            $totalL = 0;
            $totalP = 0;

            foreach ($rows as $r) {
                $kecId = $kecamatanId[$r['kecamatan']] ?? null;
                if (!$kecId) {
                    continue;
                }

                PendudukKelurahan::updateOrCreate(
                    ['nama_kelurahan' => $r['nama_kelurahan'], 'tahun' => $tahun],
                    [
                        'kecamatan_id'     => $kecId,
                        'latitude'         => $r['latitude'],
                        'longitude'        => $r['longitude'],
                        'jumlah_laki_laki' => $r['L'],
                        'jumlah_perempuan' => $r['P'],
                        'jumlah_penduduk'  => $r['L'] + $r['P'],
                    ],
                );

                $aggKec[$r['kecamatan']]['L'] = ($aggKec[$r['kecamatan']]['L'] ?? 0) + $r['L'];
                $aggKec[$r['kecamatan']]['P'] = ($aggKec[$r['kecamatan']]['P'] ?? 0) + $r['P'];
                $totalL += $r['L'];
                $totalP += $r['P'];
            }

            // Kecamatan = SUM kelurahan (konsisten by construction)
            foreach ($aggKec as $namaKec => $lp) {
                PendudukKecamatan::updateOrCreate(
                    ['kecamatan_id' => $kecamatanId[$namaKec], 'tahun' => $tahun],
                    [
                        'jumlah_laki_laki' => $lp['L'],
                        'jumlah_perempuan' => $lp['P'],
                        'jumlah_penduduk'  => $lp['L'] + $lp['P'],
                    ],
                );
            }

            // Jakarta Barat = SUM semua
            DataKependudukan::updateOrCreate(
                ['tahun' => $tahun],
                [
                    'jumlah_laki_laki' => $totalL,
                    'jumlah_perempuan' => $totalP,
                    'jumlah_total'     => $totalL + $totalP,
                    'sumber'           => "BPS Kota Jakarta Barat — Penduduk menurut Kelurahan & Jenis Kelamin ($tahun)",
                ],
            );
        }

        $this->command?->info('Selesai. Kecamatan & Jakarta Barat diturunkan dari kelurahan (konsisten).');
    }

    /** Fetch var 162 satu tahun → array kelurahan siap simpan, atau null bila gagal. */
    private function fetchKelurahan(int $thId, array $koordinat): ?array
    {
        $url = 'https://webapi.bps.go.id/v1/api/list/model/data/lang/ind'
             . '/domain/' . self::BPS_DOMAIN . '/var/' . self::BPS_VAR
             . '/th/' . $thId . '/key/' . self::BPS_KEY . '/';

        try {
            $resp = Http::withHeaders(['User-Agent' => self::UA])->timeout(60)->retry(2, 500)->get($url);
        } catch (\Throwable) {
            return null;
        }
        if (!$resp->ok()) {
            return null;
        }

        $json = $resp->json();
        if (($json['status'] ?? '') !== 'OK') {
            return null;
        }
        $dc = $json['datacontent'] ?? [];
        if (empty($dc)) {
            return null; // metadata ada tapi angka kosong
        }

        $hasil = [];
        foreach ($json['vervar'] as $vv) {
            $labelRaw = $vv['label'];
            $isHeader = str_contains($labelRaw, '<b>');   // baris kecamatan → dilewati (kita SUM sendiri)
            $bersih   = $this->cleanLabel($labelRaw);

            if ($bersih === '' || $bersih === 'Jakarta Barat') {
                continue;
            }
            if ($isHeader) {
                continue;
            }

            $nama = preg_replace('/^\d+\.\s*/', '', $bersih); // buang "3. "
            $key  = $this->norm($nama);
            $ref  = $koordinat[$key] ?? null;
            if (!$ref) {
                // Nama BPS tak dikenal → lewati (harusnya tak terjadi, sudah diverifikasi 0)
                continue;
            }

            $keyL  = $vv['val'] . self::BPS_VAR . self::TURVAR_L  . $thId . '0';
            $keyP  = $vv['val'] . self::BPS_VAR . self::TURVAR_P  . $thId . '0';
            $keyLP = $vv['val'] . self::BPS_VAR . self::TURVAR_LP . $thId . '0';

            $l     = (int) ($dc[$keyL]  ?? 0);
            $p28   = (int) ($dc[$keyP]  ?? 0);
            $total = (int) ($dc[$keyLP] ?? 0);   // L+P dari BPS = patokan (kolom 28 kadang korup)

            // Turunkan P dari total-L supaya konsisten & mengoreksi nilai Perempuan yg korup.
            if ($total > 0 && $total >= $l) {
                $p = $total - $l;
            } else {
                // fallback: turvar 29 hilang/aneh → pakai 27+28 apa adanya
                $p = $p28;
            }

            $hasil[] = [
                'kecamatan'      => $ref['kecamatan'],
                'nama_kelurahan' => $ref['nama_kelurahan'],
                'latitude'       => $ref['latitude'],
                'longitude'      => $ref['longitude'],
                'L'              => $l,
                'P'              => $p,
            ];
        }

        return $hasil ?: null;
    }

    /** Bersihkan label BPS: decode &nbsp;, buang tag, rapikan spasi. */
    private function cleanLabel(string $label): string
    {
        $label = html_entity_decode($label, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $label = strip_tags($label);
        $label = str_replace("\xC2\xA0", ' ', $label); // nbsp
        return trim(preg_replace('/\s+/', ' ', $label));
    }

    /** Normalisasi nama untuk matching (samakan dgn CSV koordinat). */
    private function norm(string $s): string
    {
        $s = strtolower($s);
        $s = preg_replace('/[^a-z]/', '', $s);
        return str_replace('tj', 'tanjung', $s); // "Tj. Duren" -> "Tanjung Duren"
    }

    /** Muat referensi koordinat: norm(nama_kelurahan) => baris. */
    private function loadKoordinat(): array
    {
        $file = base_path('database/data/koordinat-kelurahan.csv');
        if (!is_file($file)) {
            return [];
        }
        $handle = fopen($file, 'r');
        fgetcsv($handle); // lewati header: kecamatan, nama_kelurahan, latitude, longitude
        $map = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 4) {
                continue;
            }
            [$kec, $nama, $lat, $lng] = $row;
            $map[$this->norm($nama)] = [
                'kecamatan'      => trim($kec),
                'nama_kelurahan' => trim($nama),
                'latitude'       => $lat !== '' ? (float) $lat : null,
                'longitude'      => $lng !== '' ? (float) $lng : null,
            ];
        }
        fclose($handle);
        return $map;
    }
}
