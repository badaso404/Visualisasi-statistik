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
        Schema::create('fasilitas_kesehatan_kecamatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->constrained('kecamatan')->cascadeOnDelete();
            $table->year('tahun');
            $table->integer('jumlah_total');
            $table->integer('klinik_kesehatan')->default(0);
            $table->integer('posyandu')->default(0);
            $table->integer('puskesmas')->default(0);
            $table->integer('rumah_sakit')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fasilitas_kesehatan_kecamatan');
    }
};
