<?php

namespace App\Models;

use App\Models\Concerns\MenghapusAnakPerTahun;
use Illuminate\Database\Eloquent\Model;

class DataPendidikan extends Model
{
    use MenghapusAnakPerTahun;

    protected $table = 'data_pendidikan';

    /** Tabel anak yang terhubung lewat kolom tahun (ikut terhapus bersama induk). */
    protected array $anakPerTahun = [PendidikanKecamatan::class];

    protected $fillable = [
        'tahun',
        'apm_sd_mi',
        'apm_smp_mts',
        'apm_sma_smk_man',
        'apk_sd_mi',
        'apk_smp_mts',
        'apk_sma_smk_man',
        'sumber'
    ];
}
