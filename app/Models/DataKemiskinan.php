<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataKemiskinan extends Model
{
    protected $table = 'data_kemiskinan';

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
