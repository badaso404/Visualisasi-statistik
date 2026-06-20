<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LuasKecamatan extends Model
{
    protected $table = 'luas_kecamatan';
    
    protected $fillable = [
        'kecamatan_id',
        'data_geografis_id',
        'luas_km2',
        'persentase'
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function dataGeografis()
    {
        return $this->belongsTo(DataGeografis::class);
    }
}