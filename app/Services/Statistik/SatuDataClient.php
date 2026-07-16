<?php

namespace App\Services\Statistik;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Satu Data Jakarta (ws.jakarta.go.id). Tanpa API key.
 */
class SatuDataClient extends ApiClient
{
    private const CACHE_KEY = 'satudata_penduduk_usia_v2';

    /**
     * Jumlah penduduk DKI per kelompok usia & jenis kelamin (~60rb baris).
     * Slug tanpa embel tahun = dataset paling lengkap.
     */
    public function pendudukPerUsia(): array
    {
        // Hasil GAGAL tidak boleh di-cache: satu timeout sesaat jangan sampai
        // mengosongkan halaman piramida selama TTL penuh.
        $rows = Cache::get(self::CACHE_KEY);
        if (! empty($rows)) {
            return $rows;
        }

        $rows = $this->fetchDataset(
            'data-jumlah-penduduk-provinsi-dki-jakarta-berdasarkan-kelompok-usia-dan-jenis-kelamin'
        );

        if (! empty($rows)) {
            Cache::put(self::CACHE_KEY, $rows, (int) config('statistik.satudata.cache_ttl'));
        }

        return $rows;
    }

    private function fetchDataset(string $slug): array
    {
        $url = config('statistik.satudata.base_url');

        try {
            $res = $this->http((int) config('statistik.satudata.timeout'))->get($url, [
                'kategori' => 'dataset',
                'tipe'     => 'detail',
                'url'      => $slug,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Satu Data Jakarta tidak terjangkau', [
                'slug' => $slug, 'error' => $e->getMessage(),
            ]);
            return [];
        }

        return $res->successful() ? ($res->json('data') ?? []) : [];
    }
}
