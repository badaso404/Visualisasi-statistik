<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataKependudukan extends Model
{
    protected $table = 'data_kependudukan';

    protected $fillable = [
        'tahun',
        'jumlah_laki_laki',
        'jumlah_perempuan',
        'jumlah_total',
        'sumber'
    ];
}
