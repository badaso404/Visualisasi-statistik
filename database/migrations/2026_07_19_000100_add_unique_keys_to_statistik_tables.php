<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Setiap tabel statistik sebenarnya "satu baris per periode":
 * ringkasan -> satu per tahun, detail -> satu per kecamatan per tahun.
 *
 * Aturan itu selama ini hanya diasumsikan di kode (updateOrCreate pada isi
 * massal & import CSV) tanpa dijamin database. Akibatnya baris kembar bisa
 * lolos lewat form "Tambah", lalu isi massal hanya memperbarui salah satunya
 * sementara baris kembar yang tertinggal tetap ikut terhitung di grafik publik.
 *
 * Migration ini menutup celah itu di tingkat database.
 */
return new class extends Migration
{
    /** tabel => kolom pembentuk kunci unik */
    private const KUNCI = [
        'data_geografis'                => ['tahun'],
        'data_kependudukan'             => ['tahun'],
        'data_pendidikan'               => ['tahun'],
        'data_kesehatan'                => ['tahun'],
        'data_kemiskinan'               => ['tahun'],
        'data_iklim'                    => ['tahun', 'bulan'],
        'penduduk_kecamatan'            => ['kecamatan_id', 'tahun'],
        'pendidikan_kecamatan'          => ['kecamatan_id', 'tahun'],
        'tenaga_kesehatan_kecamatan'    => ['kecamatan_id', 'tahun'],
        'fasilitas_kesehatan_kecamatan' => ['kecamatan_id', 'tahun'],
        'kemiskinan_kecamatan'          => ['kecamatan_id', 'tahun'],
        'jak_wifi_kecamatan'            => ['kecamatan_id', 'tahun'],
        'cctv_kecamatan'                => ['kecamatan_id', 'tahun'],
        // Kunci ini menyamai pencocokan import CSV kelurahan.
        'penduduk_kelurahan'            => ['nama_kelurahan', 'tahun'],
        'luas_kecamatan'                => ['kecamatan_id', 'data_geografis_id'],
    ];

    public function up(): void
    {
        foreach (self::KUNCI as $tabel => $kolom) {
            if (!Schema::hasTable($tabel)) {
                continue;
            }

            $this->hapusDuplikat($tabel, $kolom);

            Schema::table($tabel, function (Blueprint $table) use ($tabel, $kolom) {
                $table->unique($kolom, $this->namaIndex($tabel));
            });
        }
    }

    public function down(): void
    {
        foreach (self::KUNCI as $tabel => $kolom) {
            if (!Schema::hasTable($tabel)) {
                continue;
            }

            Schema::table($tabel, function (Blueprint $table) use ($tabel) {
                $table->dropUnique($this->namaIndex($tabel));
            });
        }
    }

    /**
     * Sisakan satu baris per kunci — yang id-nya paling besar, karena itu
     * entri terbaru dan paling mungkin sudah dikoreksi.
     */
    private function hapusDuplikat(string $tabel, array $kolom): void
    {
        $group = implode(', ', $kolom);

        $idDisimpan = DB::table($tabel)
            ->selectRaw("MAX(id) as id")
            ->groupByRaw($group)
            ->pluck('id');

        if ($idDisimpan->isEmpty()) {
            return;
        }

        DB::table($tabel)->whereNotIn('id', $idDisimpan)->delete();
    }

    private function namaIndex(string $tabel): string
    {
        return $tabel . '_periode_unique';
    }
};
