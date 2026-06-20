<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataKesehatan extends Model
{
    protected $table = 'data_kesehatan';

    protected $fillable = [
        'tahun',
        'jumlah_tempat_tidur_rs',
        'cakupan_imunisasi_dasar',
        'sumber'
    ];
}
