<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JakWifiKecamatan extends Model
{
    protected $table = 'jak_wifi_kecamatan';

    protected $fillable = [
        'kecamatan_id',
        'tahun',
        'jumlah_titik',
        'titik_aktif',
        'jumlah_pengguna',
        'keterangan'
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}
