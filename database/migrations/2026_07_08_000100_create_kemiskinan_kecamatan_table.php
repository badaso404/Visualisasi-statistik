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
        Schema::create('kemiskinan_kecamatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->constrained('kecamatan')->cascadeOnDelete();
            $table->year('tahun');
            $table->integer('jumlah_penduduk_miskin');  // jiwa
            $table->integer('jumlah_keluarga_miskin');  // KK
            $table->integer('penerima_bantuan');        // jiwa penerima bantuan sosial
            $table->decimal('persentase', 5, 2);        // % penduduk miskin di kecamatan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kemiskinan_kecamatan');
    }
};
