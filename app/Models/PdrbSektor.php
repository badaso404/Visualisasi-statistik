<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdrbSektor extends Model
{
    protected $table = 'pdrb_sektor';

    protected $fillable = [
        'tahun',
        'kode_sektor',        // vervar BPS 1–17
        'kategori',           // huruf kategori lapangan usaha (A, B, … R,S,T,U)
        'nama_sektor',
        'adhb',               // Juta Rupiah, harga berlaku
        'distribusi',         // % terhadap PDRB
        'laju_pertumbuhan',   // %
    ];
}
