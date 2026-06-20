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
        Schema::create('data_geografis', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->decimal('luas_kota_km2', 8, 2);
            $table->integer('ketinggian_mdpl');
            $table->string('sumber')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_geografis');
    }
};
