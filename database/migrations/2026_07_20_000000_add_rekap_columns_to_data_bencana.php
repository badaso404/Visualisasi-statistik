<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Selaraskan data_bencana dengan API Satu Data Jakarta yang granularitasnya
     * rekap triwulanan per kota/kabupaten (bukan kejadian per lokasi).
     * Kolom lama dipertahankan (nullable) agar data manual lama tidak hilang.
     */
    public function up(): void
    {
        Schema::table('data_bencana', function (Blueprint $table) {
            $table->string('periode_data', 6)->nullable()->after('tahun');      // YYYYMM, mis. 202403
            $table->unsignedTinyInteger('triwulan')->nullable()->after('periode_data');
            $table->string('wilayah')->nullable()->after('triwulan');
            $table->integer('jumlah_korban_meninggal')->default(0)->after('jumlah_kejadian');
            $table->integer('jumlah_korban_luka')->default(0)->after('jumlah_korban_meninggal');
        });

        // Baris rekap dari API tidak punya nama lokasi → izinkan kosong
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE data_bencana MODIFY nama_lokasi VARCHAR(255) NULL');
        }
    }

    public function down(): void
    {
        Schema::table('data_bencana', function (Blueprint $table) {
            $table->dropColumn([
                'periode_data', 'triwulan', 'wilayah',
                'jumlah_korban_meninggal', 'jumlah_korban_luka',
            ]);
        });
    }
};
