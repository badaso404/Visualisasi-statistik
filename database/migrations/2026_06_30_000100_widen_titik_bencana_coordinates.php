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
        DB::statement('ALTER TABLE titik_bencana MODIFY latitude DECIMAL(18,15) NOT NULL');
        DB::statement('ALTER TABLE titik_bencana MODIFY longitude DECIMAL(18,15) NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE titik_bencana MODIFY latitude DECIMAL(10,7) NOT NULL');
        DB::statement('ALTER TABLE titik_bencana MODIFY longitude DECIMAL(10,7) NOT NULL');
    }
};
