<?php

namespace App\Services\Statistik;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * DSDA Posko Banjir DKI — Tinggi Muka Air (TMA), data live.
 *
 * Satu-satunya sumber yang memang harus segar; sisanya di-sync ke DB.
 */
class DsdaClient extends ApiClient
{
    private const CACHE_KEY = 'tma_all';

    /**
     * Semua stasiun TMA, sudah dinormalisasi. Baris tanpa koordinat valid dibuang.
     *
     * @return array<int, array{lat: float, lng: float, name: string, status: string, tinggi: mixed, tanggal: mixed}>
     */
    public function tinggiMukaAir(): array
    {
        // Hasil KOSONG/GAGAL tidak di-cache, agar timeout sesaat tidak
        // menghilangkan titik selama TTL penuh — request berikutnya langsung retry.
        $stasiun = Cache::get(self::CACHE_KEY);
        if (! empty($stasiun)) {
            return $stasiun;
        }

        $stasiun = $this->fetch();

        if (! empty($stasiun)) {
            Cache::put(self::CACHE_KEY, $stasiun, (int) config('statistik.dsda.cache_ttl'));
        }

        return $stasiun;
    }

    private function fetch(): array
    {
        try {
            $res = $this->http((int) config('statistik.dsda.timeout'))
                ->get(config('statistik.dsda.url'));
        } catch (\Throwable $e) {
            Log::warning('DSDA TMA tidak terjangkau', ['error' => $e->getMessage()]);
            return [];
        }

        if (! $res->ok()) {
            return [];
        }

        $stasiun = [];
        foreach (($res->json() ?? []) as $r) {
            $lat = is_numeric($r['LATITUDE'] ?? null) ? (float) $r['LATITUDE'] : null;
            $lng = is_numeric($r['LONGITUDE'] ?? null) ? (float) $r['LONGITUDE'] : null;
            if ($lat === null || $lng === null) {
                continue;
            }

            $stasiun[] = [
                'lat'     => $lat,
                'lng'     => $lng,
                'name'    => trim($r['NAMA_PINTU_AIR'] ?? ''),
                'status'  => $r['STATUS_SIAGA'] ?? '-',
                'tinggi'  => $r['TINGGI_AIR'] ?? null,
                'tanggal' => $r['TANGGAL'] ?? null,
            ];
        }

        return $stasiun;
    }
}
