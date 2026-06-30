<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Titik referensi pada peta bencana (POI), berbeda dari catatan kejadian:
     * - banjir_rawan : zona rawan banjir (level 1/2/3)
     * - pos_damkar   : pos pemadam kebakaran
     * - zona_aman    : titik kumpul / tempat evakuasi sementara
     */
    public function up(): void
    {
        Schema::create('titik_bencana', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatan')->nullOnDelete();
            $table->string('kategori'); // banjir_rawan | pos_damkar | zona_aman
            $table->unsignedTinyInteger('level')->nullable(); // khusus banjir_rawan: 1, 2, 3
            $table->string('nama');
            $table->decimal('latitude', 18, 15);
            $table->decimal('longitude', 18, 15);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('titik_bencana');
    }
};
