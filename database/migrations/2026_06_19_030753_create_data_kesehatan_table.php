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
        Schema::create('data_kesehatan', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->integer('jumlah_tempat_tidur_rs');
            $table->decimal('cakupan_imunisasi_dasar', 5, 2);
            $table->string('sumber')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_kesehatan');
    }
};
