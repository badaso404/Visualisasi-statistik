<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenagaKesehatanKecamatan extends Model
{
    protected $table = 'tenaga_kesehatan_kecamatan';

    protected $fillable = [
        'kecamatan_id',
        'tahun',
        'jumlah_total',
        'dokter',
        'perawat',
        'bidan',
        'ahli_gizi',
        'farmasi'
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}
