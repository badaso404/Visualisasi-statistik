<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CctvKecamatan extends Model
{
    protected $table = 'cctv_kecamatan';

    protected $fillable = [
        'kecamatan_id',
        'tahun',
        'jumlah_unit',
        'unit_aktif',
        'terintegrasi',
        'keterangan'
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}
