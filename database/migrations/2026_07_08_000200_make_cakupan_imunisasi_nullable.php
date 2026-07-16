<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cakupan imunisasi dasar tidak tersedia di BPS WebAPI (var 128/129/221/222),
     * jadi kolomnya dibuat nullable agar data kesehatan bisa 100% dari BPS.
     */
    public function up(): void
    {
        Schema::table('data_kesehatan', function (Blueprint $table) {
            $table->decimal('cakupan_imunisasi_dasar', 5, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('data_kesehatan', function (Blueprint $table) {
            $table->decimal('cakupan_imunisasi_dasar', 5, 2)->nullable(false)->change();
        });
    }
};
