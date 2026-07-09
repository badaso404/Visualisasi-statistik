<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_kemiskinan', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->integer('jumlah_penduduk_miskin');            // jiwa
            $table->decimal('persentase_penduduk_miskin', 5, 2);  // %
            $table->decimal('garis_kemiskinan', 12, 2);           // Rp per kapita/bulan
            $table->decimal('indeks_kedalaman', 5, 2);            // P1
            $table->decimal('indeks_keparahan', 5, 2);            // P2
            $table->string('sumber')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_kemiskinan');
    }
};
