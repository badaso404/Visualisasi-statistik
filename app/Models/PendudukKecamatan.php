<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendudukKecamatan extends Model
{
    protected $table = 'penduduk_kecamatan';

    protected $fillable = ['kecamatan_id', 'tahun', 'jumlah_penduduk'];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}
