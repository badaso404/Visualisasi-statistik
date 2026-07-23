<?php

namespace App\Models;

use App\Models\Concerns\MenghapusAnakPerTahun;
use Illuminate\Database\Eloquent\Model;

class DataKependudukan extends Model
{
    use MenghapusAnakPerTahun;

    protected $table = 'data_kependudukan';

    /** Tabel anak yang terhubung lewat kolom tahun (ikut terhapus bersama induk). */
    protected array $anakPerTahun = [PendudukKecamatan::class, PendudukKelurahan::class];

    protected $fillable = [
        'tahun',
        'jumlah_laki_laki',
        'jumlah_perempuan',
        'jumlah_total',
        'sumber'
    ];
}
