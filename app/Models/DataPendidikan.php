<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataPendidikan extends Model
{
    protected $table = 'data_pendidikan';

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
