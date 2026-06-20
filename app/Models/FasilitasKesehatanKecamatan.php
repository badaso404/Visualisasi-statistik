<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FasilitasKesehatanKecamatan extends Model
{
    protected $table = 'fasilitas_kesehatan_kecamatan';

    protected $fillable = [
        'kecamatan_id',
        'tahun',
        'jumlah_total',
        'klinik_kesehatan',
        'posyandu',
        'puskesmas',
        'rumah_sakit'
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}
