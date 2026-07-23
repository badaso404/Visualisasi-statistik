<?php

namespace App\Models;

use App\Models\Concerns\MenghapusAnakPerTahun;
use Illuminate\Database\Eloquent\Model;

class DataKemiskinan extends Model
{
    use MenghapusAnakPerTahun;

    protected $table = 'data_kemiskinan';

    /** Tabel anak yang terhubung lewat kolom tahun (ikut terhapus bersama induk). */
    protected array $anakPerTahun = [KemiskinanKecamatan::class];

    protected $fillable = [
        'tahun',
        'jumlah_penduduk_miskin',        // jiwa
        'persentase_penduduk_miskin',    // %
        'garis_kemiskinan',              // Rp per kapita/bulan
        'indeks_kedalaman',              // P1
        'indeks_keparahan',              // P2
        'sumber',
    ];
}
