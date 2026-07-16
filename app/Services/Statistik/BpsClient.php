<?php

namespace App\Services\Statistik;

use Illuminate\Support\Facades\Log;

/**
 * BPS WebAPI (webapi.bps.go.id), domain 3174 = Kota Jakarta Barat.
 *
 * Semua kegagalan (timeout, WAF, tahun belum rilis) dikembalikan sebagai array
 * kosong, tidak melempar exception — pemanggil cukup melewati tahun tersebut.
 */
class BpsClient extends ApiClient
{
    /**
     * datacontent satu variabel pada satu tahun.
     * Key datacontent berformat: {vervar}{var}{turvar}{th}{turth}.
     */
    public function datacontent(int $var, string $thKode): array
    {
        $json = $this->fetch('data/lang/ind', ['var' => $var, 'th' => $thKode]);

        return $json['datacontent'] ?? [];
    }

    /**
     * Tahun yang tersedia di BPS untuk sebuah variabel, disaring >= $minTahun.
     * Mengembalikan [th_kode(string) => tahun(int)].
     */
    public function tahunTersedia(int $var, int $minTahun = 0): array
    {
        $json = $this->fetch('th', ['var' => $var]);

        $tahun = [];
        foreach (($json['data'][1] ?? []) as $row) {
            $kode = (string) ($row['th_id'] ?? '');
            $th   = (int) ($row['th'] ?? 0);
            if ($kode !== '' && $th >= $minTahun) {
                $tahun[$kode] = $th;
            }
        }

        return $tahun;
    }

    /**
     * Susun URL BPS (berbasis segmen path, bukan query string) lalu ambil.
     * Contoh: {base}/list/model/data/lang/ind/domain/3174/var/117/th/122/key/{key}/
     */
    private function fetch(string $model, array $segments): array
    {
        $key = config('statistik.bps.key');
        if (empty($key)) {
            Log::warning('BPS_API_KEY belum diisi di .env — data BPS dilewati.');
            return [];
        }

        $url = rtrim(config('statistik.bps.base_url'), '/')
             . "/list/model/{$model}/domain/" . config('statistik.bps.domain');

        foreach ($segments as $nama => $nilai) {
            $url .= "/{$nama}/{$nilai}";
        }

        $url .= "/key/{$key}/";

        try {
            $res = $this->http((int) config('statistik.bps.timeout'))->get($url);
        } catch (\Throwable $e) {
            Log::warning('BPS WebAPI tidak terjangkau', [
                'model' => $model, 'segments' => $segments, 'error' => $e->getMessage(),
            ]);
            return [];
        }

        if (! $res->ok() || $res->json('status') !== 'OK') {
            return [];   // tahun/variabel belum tersedia di BPS
        }

        return $res->json() ?? [];
    }
}
