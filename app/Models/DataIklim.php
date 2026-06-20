<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataIklim extends Model
{
    protected $table = 'data_iklim';

    protected $fillable = [
        'tahun',
        'bulan',
        'hari_hujan',
        'tekanan_udara',
        'suhu_udara',
        'kecepatan_angin',
        'kelembaban_udara',
        'penyinaran_matahari',
        'sumber'
    ];
}
