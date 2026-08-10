<?php

use Illuminate\Support\Facades\App;

if (! function_exists('nf')) {
    /**
     * Format angka mengikuti bahasa yang sedang aktif.
     *
     *     nf(1234567.89, 2)  →  "1.234.567,89"  (id)
     *                        →  "1,234,567.89"  (en)
     *
     * Dibuat karena number_format() memaksa pemisahnya ditulis di setiap
     * pemanggilan. Sebelum ada fungsi ini pemakaiannya tercampur di dalam satu
     * proyek — sebagian view menulis number_format($x, 0, ',', '.') dan
     * sebagian lain memakai number_format($x) yang bawaannya justru gaya
     * Inggris — sehingga satu halaman bisa menampilkan dua gaya sekaligus.
     *
     * Perlu diingat: "1.234" dan "1,234" bukan cuma soal selera. Pembaca
     * berbahasa Inggris membaca "1.234" sebagai satu koma dua tiga empat, jadi
     * angka yang tidak diikutkan ke sini bukan sekadar terlihat asing —
     * nilainya salah terbaca sampai seribu kali lipat.
     */
    function nf(float|int|string|null $nilai, int $desimal = 0): string
    {
        [$titikDesimal, $pemisahRibuan] = App::getLocale() === 'id'
            ? [',', '.']
            : ['.', ','];

        return number_format((float) $nilai, $desimal, $titikDesimal, $pemisahRibuan);
    }
}

if (! function_exists('locale_angka_js')) {
    /**
     * Tag bahasa untuk Number.prototype.toLocaleString di sisi JavaScript,
     * supaya angka pada grafik, peta, dan tabel yang dibangun JS memakai
     * pemisah yang sama dengan angka yang dirender PHP.
     */
    function locale_angka_js(): string
    {
        return App::getLocale() === 'id' ? 'id-ID' : 'en-US';
    }
}
