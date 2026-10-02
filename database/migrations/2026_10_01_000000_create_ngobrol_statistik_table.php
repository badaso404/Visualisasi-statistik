<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Konten modul Ngobrol Statistik: video YouTube, infografis (gambar), dan
     * materi statistik.
     *
     * Ketiganya satu tabel karena halaman & panel admin-nya sama, dan kolom
     * bersamanya (judul, deskripsi, urutan, status) jauh lebih banyak daripada
     * kolom khasnya. Kolom khas dibiarkan nullable:
     *   video      → youtube_id
     *   infografis → gambar
     *   materi     → isi (+ gambar sampul opsional)
     *
     * Tidak ada kolom tahun maupun kecamatan seperti modul lain — isinya
     * kurasi konten, bukan angka.
     */
    public function up(): void
    {
        Schema::create('ngobrol_statistik', function (Blueprint $table) {
            $table->id();

            // Slug, bukan enum SQL — lihat NgobrolStatistik::KATEGORI.
            $table->string('kategori', 32);

            $table->string('judul');
            $table->text('deskripsi')->nullable();

            // ID video YouTube (11 karakter). Admin boleh menempel URL apa pun
            // (watch, youtu.be, shorts, embed); controller yang memotongnya.
            $table->string('youtube_id', 20)->nullable();

            // Path relatif di disk "public" (storage/app/public/ngobrol-statistik/…).
            $table->string('gambar')->nullable();

            // Isi artikel materi. Teks biasa; baris kosong = paragraf baru.
            $table->longText('isi')->nullable();

            // Urutan tampil manual: kecil di depan. Sama-sama 0 → terbaru dulu.
            // Untuk video, yang paling depan jadi video besar di halaman publik.
            $table->unsignedSmallInteger('urutan')->default(0);

            // Disembunyikan tanpa dihapus, mis. konten yang sedang ditinjau ulang.
            $table->boolean('tampil')->default(true);

            $table->timestamps();

            $table->index(['tampil', 'kategori', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ngobrol_statistik');
    }
};
