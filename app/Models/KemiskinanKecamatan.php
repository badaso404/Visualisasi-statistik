<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KemiskinanKecamatan extends Model
{
    protected $table = 'kemiskinan_kecamatan';

    protected $fillable = [
        'kecamatan_id',
        'tahun',
        'jumlah_penduduk_miskin',    // jiwa
        'jumlah_keluarga_miskin',    // KK
        'penerima_bantuan',          // jiwa penerima bantuan sosial
        'persentase',                // % penduduk miskin di kecamatan
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}
