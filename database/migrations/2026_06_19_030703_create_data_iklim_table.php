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
        Schema::create('data_iklim', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan'); // 1-12
            $table->decimal('hari_hujan', 5, 2);
            $table->decimal('tekanan_udara', 8, 2);
            $table->decimal('suhu_udara', 5, 2);
            $table->decimal('kecepatan_angin', 5, 2);
            $table->decimal('kelembaban_udara', 5, 2);
            $table->decimal('penyinaran_matahari', 5, 2);
            $table->string('sumber')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_iklim');
    }
};
