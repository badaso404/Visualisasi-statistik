<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendudukKecamatan extends Model
{
    protected $table = 'penduduk_kecamatan';

    protected $fillable = ['kecamatan_id', 'tahun', 'jumlah_penduduk', 'jumlah_laki_laki', 'jumlah_perempuan'];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}
