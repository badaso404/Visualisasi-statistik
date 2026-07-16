<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendudukKelurahan extends Model
{
    protected $table = 'penduduk_kelurahan';

    protected $fillable = [
        'kecamatan_id',
        'tahun',
        'nama_kelurahan',
        'latitude',
        'longitude',
        'jumlah_penduduk',
        'jumlah_laki_laki',
        'jumlah_perempuan',
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}
