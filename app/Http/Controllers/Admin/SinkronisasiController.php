<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Statistik\SinkronisasiBps;
use Illuminate\Http\Request;

/**
 * Tombol "Sinkronkan BPS" pada modul yang datanya bersumber dari BPS WebAPI.
 *
 * Satu controller untuk semua modul: yang membedakan hanya seeder yang
 * dijalankan, dan pemetaannya sudah ada di SinkronisasiBps.
 */
class SinkronisasiController extends Controller
{
    public function __invoke(Request $request, string $modul, SinkronisasiBps $sync)
    {
        if (!SinkronisasiBps::dikenal($modul)) {
            abort(404);
        }

        $hasil = $sync->jalankan($modul);
        $pesan = $sync->ringkas($modul, $hasil);

        return back()->with($hasil['error'] ? 'error' : 'success', $pesan);
    }
}
