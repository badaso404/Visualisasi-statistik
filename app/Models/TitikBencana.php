<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TitikBencana extends Model
{
    protected $table = 'titik_bencana';

    protected $fillable = [
        'kecamatan_id',
        'kategori',
        'level',
        'nama',
        'latitude',
        'longitude',
        'link_maps',
        'keterangan',
    ];

    /** Daftar kategori titik (untuk dropdown form & label). */
    public const KATEGORI = [
        'banjir_rawan' => 'Zona Rawan Banjir',
        'pos_damkar'   => 'Pos Damkar',
        'zona_aman'    => 'Zona Aman',
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function getKategoriLabelAttribute(): string
    {
        return self::KATEGORI[$this->kategori] ?? $this->kategori;
    }
}
