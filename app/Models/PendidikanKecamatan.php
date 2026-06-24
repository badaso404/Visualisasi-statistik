<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendidikanKecamatan extends Model
{
    protected $table = 'pendidikan_kecamatan';

    protected $fillable = [
        'kecamatan_id',
        'tahun',
        'jumlah_pelajar',
        'jumlah_pendidik',
        'jumlah_sekolah_negeri',
        'jumlah_sekolah_swasta',
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}
