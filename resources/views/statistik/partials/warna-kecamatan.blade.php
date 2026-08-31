{{-- Sumber warna tunggal per kecamatan — dipakai SEMUA modul statistik agar
     konsisten (mis. Cengkareng selalu amber di setiap modul). Palet kategorikal
     colorblind-safe & tervalidasi (dataviz).

     Daftar warnanya sendiri sekarang tinggal di config/statistik.php, karena
     komponen <x-statistik.tabel> mewarnai kolom di sisi PHP dan tidak bisa
     membaca literal JavaScript. Berkas ini hanya memancarkannya ke browser —
     ubah warnanya di config, bukan di sini. --}}
<script>
    // Warna khas per kecamatan (key = NAMA UPPERCASE)
    window.WARNA_KEC = @json(config('statistik.warna.kecamatan'));

    // Ambil warna sebuah kecamatan (case-insensitive). Fallback abu netral.
    window.warnaKecamatan = function (n) {
        return window.WARNA_KEC[String(n || '').toUpperCase().trim()]
            || @json(config('statistik.warna.netral'));
    };

    // Palet kategorikal umum untuk chart NON-kecamatan (mis. per bulan / per jenis)
    window.CAT_COLORS = @json(config('statistik.warna.kategori'));
</script>
