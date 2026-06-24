<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendidikan_kecamatan', function (Blueprint $table) {
            $table->renameColumn('jumlah_murid', 'jumlah_pelajar');
            $table->renameColumn('jumlah_guru', 'jumlah_pendidik');
        });

        Schema::table('pendidikan_kecamatan', function (Blueprint $table) {
            $table->integer('jumlah_sekolah_negeri')->default(0)->after('jumlah_pendidik');
            $table->integer('jumlah_sekolah_swasta')->default(0)->after('jumlah_sekolah_negeri');
            $table->dropColumn('jumlah_sekolah');
        });
    }

    public function down(): void
    {
        Schema::table('pendidikan_kecamatan', function (Blueprint $table) {
            $table->renameColumn('jumlah_pelajar', 'jumlah_murid');
            $table->renameColumn('jumlah_pendidik', 'jumlah_guru');
        });

        Schema::table('pendidikan_kecamatan', function (Blueprint $table) {
            $table->dropColumn(['jumlah_sekolah_negeri', 'jumlah_sekolah_swasta']);
            $table->integer('jumlah_sekolah')->default(0);
        });
    }
};
