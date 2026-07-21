<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rincian PDRB menurut 17 kategori lapangan usaha, satu baris per tahun per sektor.
 *
 * `kode_sektor` memakai vervar BPS (1–17) sebagai identitas stabil; `kategori`
 * adalah huruf kategori lapangan usaha (A, B, … R,S,T,U) yang di BPS menempel di
 * depan label dan dipisah saat seeding.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdrb_sektor', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->unsignedTinyInteger('kode_sektor');   // vervar BPS 1–17
            $table->string('kategori', 12)->nullable();   // huruf kategori lapangan usaha
            $table->string('nama_sektor');
            $table->decimal('adhb', 18, 2);               // Juta Rupiah, harga berlaku
            $table->decimal('adhk', 18, 2);               // Juta Rupiah, harga konstan 2010
            $table->decimal('distribusi', 6, 2);          // % terhadap PDRB
            $table->decimal('laju_pertumbuhan', 6, 2);    // % — bisa negatif
            $table->timestamps();

            $table->unique(['tahun', 'kode_sektor'], 'pdrb_sektor_tahun_kode_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdrb_sektor');
    }
};
