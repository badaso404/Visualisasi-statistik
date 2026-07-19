<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Lebarkan presisi koordinat agar bisa menyimpan lat/long apa adanya
     * dari Google Maps (mis. -6.183454092199373) tanpa dibulatkan.
     * decimal(18,15): 3 digit sebelum koma + 15 digit di belakang koma.
     */
    public function up(): void
    {
        $this->ubahPresisi(18, 15);
    }

    public function down(): void
    {
        $this->ubahPresisi(10, 7);
    }

    /**
     * MODIFY hanya dikenal MySQL; sqlite (dipakai saat test) tidak punya
     * padanannya dan memang menyimpan angka tanpa batas presisi kolom.
     */
    private function ubahPresisi(int $total, int $desimal): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE titik_bencana MODIFY latitude DECIMAL({$total},{$desimal}) NOT NULL");
        DB::statement("ALTER TABLE titik_bencana MODIFY longitude DECIMAL({$total},{$desimal}) NOT NULL");
    }
};
