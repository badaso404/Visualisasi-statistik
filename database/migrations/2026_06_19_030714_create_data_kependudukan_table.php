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
        Schema::create('data_kependudukan', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->integer('jumlah_laki_laki');
            $table->integer('jumlah_perempuan');
            $table->integer('jumlah_total');
            $table->string('sumber')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_kependudukan');
    }
};
