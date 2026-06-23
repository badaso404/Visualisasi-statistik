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
        Schema::create('data_bencana', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatan')->nullOnDelete();
            $table->string('jenis_bencana');
            $table->string('nama_lokasi');
            $table->year('tahun');
            $table->date('tanggal_kejadian')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('jumlah_kejadian')->default(1);
            $table->integer('jumlah_korban')->default(0);
            $table->integer('jumlah_terdampak')->default(0);
            $table->text('keterangan')->nullable();
            $table->string('sumber')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_bencana');
    }
};
