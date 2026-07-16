<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Data administrasi per kecamatan dari BPS var 155
     * (Jumlah Kelurahan, RW, RT, KK menurut Kecamatan).
     */
    public function up(): void
    {
        Schema::table('luas_kecamatan', function (Blueprint $table) {
            $table->unsignedSmallInteger('jumlah_kelurahan')->nullable()->after('persentase');
            $table->unsignedSmallInteger('jumlah_rw')->nullable()->after('jumlah_kelurahan');
            $table->unsignedInteger('jumlah_rt')->nullable()->after('jumlah_rw');
        });
    }

    public function down(): void
    {
        Schema::table('luas_kecamatan', function (Blueprint $table) {
            $table->dropColumn(['jumlah_kelurahan', 'jumlah_rw', 'jumlah_rt']);
        });
    }
};
