<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ringkasan PDRB Kota Jakarta Barat, satu baris per tahun.
 *
 * Nilai PDRB disimpan dalam JUTA RUPIAH mengikuti satuan asli BPS (var 42 & 52)
 * agar tidak ada pembulatan saat pengambilan; konversi ke triliun hanya dilakukan
 * saat tampil.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_perekonomian', function (Blueprint $table) {
            $table->id();
            $table->year('tahun')->unique();
            $table->decimal('pdrb_adhb', 18, 2);          // Juta Rupiah, harga berlaku
            $table->decimal('pdrb_adhk', 18, 2);          // Juta Rupiah, harga konstan 2010
            $table->decimal('laju_pertumbuhan', 6, 2);    // % — bisa negatif (2020)
            $table->string('sumber')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_perekonomian');
    }
};
