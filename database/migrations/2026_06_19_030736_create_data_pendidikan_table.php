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
        Schema::create('data_pendidikan', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->decimal('apm_sd_mi', 5, 2);
            $table->decimal('apm_smp_mts', 5, 2);
            $table->decimal('apm_sma_smk_man', 5, 2);
            $table->decimal('apk_sd_mi', 5, 2);
            $table->decimal('apk_smp_mts', 5, 2);
            $table->decimal('apk_sma_smk_man', 5, 2);
            $table->string('sumber')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_pendidikan');
    }
};
