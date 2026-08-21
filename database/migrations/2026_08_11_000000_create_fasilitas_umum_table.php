<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel fasilitas umum (GOR, RPTRA, tempat ibadah, perpustakaan,
     * transportasi, pemadam kebakaran).
     *
     * Berbeda dari modul lain yang menyimpan AGREGAT per kecamatan per tahun,
     * tabel ini menyimpan SATU BARIS PER FASILITAS. Alasannya sumber datanya
     * memang begitu: API fasilitas milik situs kecamatan Jakarta Barat
     * (barat.jakarta.go.id/kecamatan/api/v1/fasilitas/{kategori}) mengembalikan
     * daftar tempat, bukan hitungan. Angka per kecamatan yang dipakai grafik
     * tinggal di-COUNT dari sini, sedangkan arah sebaliknya (memecah agregat
     * jadi daftar) tidak mungkin.
     *
     * Konsekuensinya tidak ada kolom `tahun` seperti modul lain: yang datang
     * dari API adalah inventaris apa adanya saat ini, tanpa penanda periode.
     * Kapan terakhir ditarik dibaca dari `updated_at`.
     */
    public function up(): void
    {
        Schema::create('fasilitas_umum', function (Blueprint $table) {
            $table->id();

            // Kategori disimpan sebagai slug (lihat FasilitasUmum::KATEGORI),
            // bukan enum SQL, supaya menambah kategori baru cukup lewat kode.
            $table->string('kategori', 32);

            // id milik API sumber. Dipakai sebagai kunci updateOrCreate supaya
            // sinkronisasi berulang memperbarui baris yang sama, bukan
            // menumpuk duplikat. Null untuk data yang diinput manual admin.
            $table->unsignedBigInteger('sumber_id')->nullable();

            $table->string('nama');
            $table->text('alamat')->nullable();

            // Nullable + nullOnDelete: fasilitasnya tetap ada meski kecamatan
            // dihapus dari master, dan sebagian data sumber (mis. beberapa pos
            // pemadam kebakaran) memang tidak menyebut wilayah sama sekali.
            $table->foreignId('kecamatan_id')->nullable()
                ->constrained('kecamatan')->nullOnDelete();
            $table->string('kelurahan')->nullable();

            // Koordinat dipakai peta sebaran. API sumber tidak mengirimkannya,
            // jadi terisi hanya bila admin melengkapi sendiri.
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->string('foto')->nullable();
            $table->string('sumber')->nullable();
            $table->timestamps();

            // Satu fasilitas dari API muncul sekali per kategori. Data manual
            // (sumber_id null) tidak ikut terkena karena MySQL memperlakukan
            // NULL sebagai nilai yang selalu berbeda pada unique index.
            $table->unique(['kategori', 'sumber_id']);
            $table->index(['kategori', 'kecamatan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fasilitas_umum');
    }
};
