<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jenis konten "knowledge" berganti nama jadi "materi". Slug-nya ikut
     * diganti (bukan hanya label) karena slug ini muncul di URL halaman
     * publik: /statistik/ngobrol-statistik/materi.
     */
    public function up(): void
    {
        DB::table('ngobrol_statistik')->where('kategori', 'knowledge')->update(['kategori' => 'materi']);
    }

    public function down(): void
    {
        DB::table('ngobrol_statistik')->where('kategori', 'materi')->update(['kategori' => 'knowledge']);
    }
};
