<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendidikanKecamatan extends Model
{
    protected $table = 'pendidikan_kecamatan';

    protected $fillable = [
        'kecamatan_id',
        'tahun',
        'jumlah_murid',
        'jumlah_guru',
        'jumlah_sekolah'
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}
