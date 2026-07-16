<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penduduk_kecamatan', function (Blueprint $table) {
            $table->integer('jumlah_laki_laki')->default(0)->after('jumlah_penduduk');
            $table->integer('jumlah_perempuan')->default(0)->after('jumlah_laki_laki');
        });

        Schema::table('penduduk_kelurahan', function (Blueprint $table) {
            $table->integer('jumlah_laki_laki')->default(0)->after('jumlah_penduduk');
            $table->integer('jumlah_perempuan')->default(0)->after('jumlah_laki_laki');
        });
    }

    public function down(): void
    {
        Schema::table('penduduk_kecamatan', function (Blueprint $table) {
            $table->dropColumn(['jumlah_laki_laki', 'jumlah_perempuan']);
        });

        Schema::table('penduduk_kelurahan', function (Blueprint $table) {
            $table->dropColumn(['jumlah_laki_laki', 'jumlah_perempuan']);
        });
    }
};
