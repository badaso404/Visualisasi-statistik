<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ADHK per lapangan usaha tidak dipakai di mana pun setelah tabel sektor pada
 * halaman publik diringkas (kolomnya dibuang, grafik strukturnya memakai
 * distribusi). ADHK tingkat KOTA tetap ada di `data_perekonomian` — yang itu
 * masih dipakai kartu ringkasan, grafik tren, dan indeks implisit.
 *
 * Kalau suatu saat dibutuhkan lagi: jalankan rollback lalu `db:seed
 * --class=PerekonomianSeeder`; angkanya diambil ulang dari BPS var 52, karena
 * migration ini tidak menyimpan salinan nilainya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pdrb_sektor', function (Blueprint $table) {
            $table->dropColumn('adhk');
        });
    }

    public function down(): void
    {
        Schema::table('pdrb_sektor', function (Blueprint $table) {
            $table->decimal('adhk', 18, 2)->default(0)->after('adhb');
        });
    }
};
