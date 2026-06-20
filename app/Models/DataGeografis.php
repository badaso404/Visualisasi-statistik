<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataGeografis extends Model
{
    protected $table = 'data_geografis';
    
    protected $fillable = ['tahun', 'luas_kota_km2', 'ketinggian_mdpl', 'sumber'];

    public function luasKecamatan()
    {
        return $this->hasMany(LuasKecamatan::class);
    }
}