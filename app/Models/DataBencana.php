<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataBencana extends Model
{
    protected $table = 'data_bencana';

    protected $fillable = [
        'kecamatan_id',
        'jenis_bencana',
        'nama_lokasi',
        'tahun',
        'tanggal_kejadian',
        'latitude',
        'longitude',
        'jumlah_kejadian',
        'jumlah_korban',
        'jumlah_terdampak',
        'keterangan',
        'sumber',
    ];

    /** Daftar jenis bencana yang tersedia (untuk dropdown form). */
    public const JENIS = [
        'Banjir',
        'Kebakaran',
        'Tanah Longsor',
        'Angin Kencang',
        'Pohon Tumbang',
        'Gempa Bumi',
        'Lainnya',
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}
