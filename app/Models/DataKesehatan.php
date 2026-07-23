<?php

namespace App\Models;

use App\Models\Concerns\MenghapusAnakPerTahun;
use Illuminate\Database\Eloquent\Model;

class DataKesehatan extends Model
{
    use MenghapusAnakPerTahun;

    protected $table = 'data_kesehatan';

    /** Tabel anak yang terhubung lewat kolom tahun (ikut terhapus bersama induk). */
    protected array $anakPerTahun = [TenagaKesehatanKecamatan::class, FasilitasKesehatanKecamatan::class];

    protected $fillable = [
        'tahun',
        'jumlah_tempat_tidur_rs',
        'cakupan_imunisasi_dasar',
        'sumber'
    ];
}
