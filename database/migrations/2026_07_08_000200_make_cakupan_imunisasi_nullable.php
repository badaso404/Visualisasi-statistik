<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Cakupan imunisasi dasar tidak tersedia di BPS WebAPI (var 128/129/221/222),
     * jadi kolomnya dibuat nullable agar data kesehatan bisa 100% dari BPS.
     *
     * Ditulis dengan SQL langsung, bukan ->change(). Di Laravel 10 ->change()
     * mensyaratkan paket doctrine/dbal; kalau paket itu hanya ada di require-dev,
     * migration ini gagal di server produksi yang memasang --no-dev.
     */
    public function up(): void
    {
        $this->ubahKolom('NULL');
    }

    public function down(): void
    {
        $this->ubahKolom('NOT NULL');
    }

    private function ubahKolom(string $nullability): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE data_kesehatan MODIFY cakupan_imunisasi_dasar DECIMAL(5,2) {$nullability}");

            return;
        }

        if ($driver === 'pgsql') {
            $aksi = $nullability === 'NULL' ? 'DROP NOT NULL' : 'SET NOT NULL';
            DB::statement("ALTER TABLE data_kesehatan ALTER COLUMN cakupan_imunisasi_dasar {$aksi}");
        }

        // sqlite tidak mendukung perubahan kolom dan tidak menegakkan presisi
        // desimal, jadi tidak ada yang perlu dikerjakan di sana.
    }
};
