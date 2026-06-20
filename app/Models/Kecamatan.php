<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = 'kecamatan';
    
    protected $fillable = ['nama_kecamatan'];

    public function luasKecamatan()
    {
        return $this->hasMany(LuasKecamatan::class);
    }
}