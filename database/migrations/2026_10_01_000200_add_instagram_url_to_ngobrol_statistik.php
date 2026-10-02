<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tautan unggahan Instagram untuk infografis. Infografis umumnya terbit
     * lebih dulu di Instagram; tombol di kartu publik mengarahkan ke sana.
     */
    public function up(): void
    {
        Schema::table('ngobrol_statistik', function (Blueprint $table) {
            $table->string('instagram_url', 500)->nullable()->after('gambar');
        });
    }

    public function down(): void
    {
        Schema::table('ngobrol_statistik', function (Blueprint $table) {
            $table->dropColumn('instagram_url');
        });
    }
};
