<?php

namespace App\Services;

use App\Models\DataBencana;
use Illuminate\Support\Facades\Http;

/**
 * Menarik rekap bencana triwulanan dari API Satu Data Jakarta dan
 * menyimpannya ke tabel data_bencana (DB sebagai cermin API).
 *
 * Granularitas API: per kota/kabupaten per triwulan per jenis bencana —
 * tidak ada kecamatan, lokasi, maupun tanggal kejadian.
 */
class SatuDataBencanaSync
{
    private const ENDPOINT = 'https://ws.jakarta.go.id/gateway/DataPortalSatuDataJakarta/1.0/satudata'
        . '?kategori=dataset&tipe=detail'
        . '&url=jumlah-bencana-yang-terjadi-di-provinsi-dki-jakarta-menurut-jenis-bencana-dan-kabupatenkota';

    /**
     * @return array{ditambah:int, diperbarui:int, dilewati:int, error:?string}
     */
    public function jalankan(string $wilayah = DataBencana::WILAYAH_JAKBAR): array
    {
        $hasil = ['ditambah' => 0, 'diperbarui' => 0, 'dilewati' => 0, 'error' => null];

        try {
            $resp = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; VisualisasiStatistik/1.0)'])
                ->get(self::ENDPOINT);

            if (!$resp->ok()) {
                $hasil['error'] = 'API membalas status ' . $resp->status() . '.';
                return $hasil;
            }
            $rows = $resp->json('data');
        } catch (\Throwable $e) {
            $hasil['error'] = 'Gagal menghubungi API: ' . $e->getMessage();
            return $hasil;
        }

        if (!is_array($rows) || empty($rows)) {
            $hasil['error'] = 'API tidak mengembalikan data.';
            return $hasil;
        }

        foreach ($rows as $r) {
            $wil = trim((string) ($r['wilayah'] ?? ''));
            if (strcasecmp($wil, $wilayah) !== 0) {
                $hasil['dilewati']++;
                continue;
            }

            $periode = trim((string) ($r['periode_data'] ?? ''));
            $jenis   = $this->normalkanJenis((string) ($r['jenis_bencana'] ?? ''));
            if (strlen($periode) < 6 || $jenis === '') {
                $hasil['dilewati']++;
                continue;
            }

            $triwulan = $r['triwulan'] !== null && $r['triwulan'] !== ''
                ? (int) $r['triwulan']
                : DataBencana::triwulanDariPeriode($periode);

            $rec = DataBencana::updateOrCreate(
                [
                    'periode_data'  => $periode,
                    'wilayah'       => $wil,
                    'jenis_bencana' => $jenis,
                ],
                [
                    'tahun'                   => (int) substr($periode, 0, 4),
                    'triwulan'                => $triwulan,
                    'jumlah_kejadian'         => (int) ($r['jumlah_kejadian_bencana'] ?? 0),
                    'jumlah_korban_meninggal' => (int) ($r['jumlah_korban_meninggal'] ?? 0),
                    'jumlah_korban_luka'      => (int) ($r['jumlah_korban_luka_luka'] ?? 0),
                    'sumber'                  => 'Satu Data Jakarta',
                ]
            );

            $rec->wasRecentlyCreated ? $hasil['ditambah']++ : $hasil['diperbarui']++;
        }

        return $hasil;
    }

    /**
     * API menulis jenis bencana dengan casing tidak konsisten
     * ("BANJIR" vs "Banjir") — samakan agar tidak jadi kategori ganda.
     */
    private function normalkanJenis(string $jenis): string
    {
        $jenis = trim(preg_replace('/\s+/', ' ', $jenis));
        return $jenis === '' ? '' : mb_convert_case(mb_strtolower($jenis, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }
}
