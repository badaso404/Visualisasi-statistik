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

if (! function_exists('warna_campur')) {
    /**
     * Campur dua warna heksadesimal.
     *
     *     warna_campur('#2a78d6', '#ffffff', 0.92)  →  "#eef4fc"
     *
     * $bobot adalah porsi warna kedua: 0 mengembalikan $dasar apa adanya, 1
     * mengembalikan $ke. Dipakai komponen tabel untuk menurunkan gradasi
     * header dan tint badan kolom dari satu warna dasar, supaya menambah atau
     * mengurangi kolom tidak lagi menuntut pencarian warna baru dengan tangan.
     */
    function warna_campur(string $dasar, string $ke, float $bobot): string
    {
        $urai = static function (string $hex): array {
            $hex = ltrim($hex, '#');

            // Bentuk ringkas (#abc) ditulis ulang jadi #aabbcc dulu.
            if (strlen($hex) === 3) {
                $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
            }

            return [
                (int) hexdec(substr($hex, 0, 2)),
                (int) hexdec(substr($hex, 2, 2)),
                (int) hexdec(substr($hex, 4, 2)),
            ];
        };

        [$r1, $g1, $b1] = $urai($dasar);
        [$r2, $g2, $b2] = $urai($ke);
        $bobot = max(0.0, min(1.0, $bobot));

        return sprintf(
            '#%02x%02x%02x',
            (int) round($r1 + ($r2 - $r1) * $bobot),
            (int) round($g1 + ($g2 - $g1) * $bobot),
            (int) round($b1 + ($b2 - $b1) * $bobot),
        );
    }
}

if (! function_exists('warna_kategori')) {
    /**
     * Warna ke-$i dari palet kategorikal, berputar bila indeksnya melewati
     * ujung daftar. Tabel dengan sembilan kolom memakai ulang warna pertama,
     * bukan kehabisan warna dan tampil abu-abu.
     */
    function warna_kategori(int $i): string
    {
        $palet = config('statistik.warna.kategori');

        return $palet[$i % count($palet)];
    }
}
