<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\DataKependudukan;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Aturan validasi bersama modul kependudukan: jumlah penduduk baris-baris lain
 * pada tahun yang sama + nilai baru tidak boleh melebihi total ringkasan (induk)
 * tahun tersebut. Dipakai baik oleh data per-kecamatan maupun per-kelurahan.
 */
trait MembatasiTotalIndukKependudukan
{
    /**
     * @param string $modelAnak kelas Eloquent tabel anak (punya kolom tahun & jumlah_penduduk)
     * @param string $sebutan   label himpunan untuk pesan, mis. 'semua kecamatan'
     */
    protected function tidakMelebihiTotalInduk(Request $request, ?Model $item, string $modelAnak, string $sebutan): Closure
    {
        return function (string $attribute, $value, Closure $fail) use ($request, $item, $modelAnak, $sebutan) {
            $tahun = (int) $request->input('tahun');
            $total = DataKependudukan::where('tahun', $tahun)->value('jumlah_total');
            if ($total === null) {
                return;   // tanpa induk, aturan tahun-induk yang menolaknya lebih dulu
            }

            $lain = $modelAnak::where('tahun', $tahun)
                ->when($item, fn ($q) => $q->whereKeyNot($item->getKey()))
                ->sum('jumlah_penduduk');

            if ($lain + (int) $value > $total) {
                $fail('Angka ini membuat total ' . $sebutan . ' (' . number_format($lain + (int) $value, 0, ',', '.')
                    . ') melebihi total ringkasan tahun ' . $tahun . ' (' . number_format($total, 0, ',', '.') . ').');
            }
        };
    }
}
