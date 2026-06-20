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
        Schema::create('tenaga_kesehatan_kecamatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->constrained('kecamatan')->cascadeOnDelete();
            $table->year('tahun');
            $table->integer('jumlah_total');
            $table->integer('dokter')->default(0);
            $table->integer('perawat')->default(0);
            $table->integer('bidan')->default(0);
            $table->integer('ahli_gizi')->default(0);
            $table->integer('farmasi')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenaga_kesehatan_kecamatan');
    }
};
