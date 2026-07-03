{{-- Sumber warna tunggal per kecamatan — dipakai SEMUA modul statistik agar
     konsisten (mis. Cengkareng selalu pink di setiap modul). Palet kategorikal
     colorblind-safe & tervalidasi (dataviz). Ubah di sini → berubah di semua modul. --}}
<script>
    // Warna khas per kecamatan (key = NAMA UPPERCASE)
    window.WARNA_KEC = {
        'KALIDERES':          '#e87ba4',   // pink
        'CENGKARENG':         '#eda100',   // amber
        'KEBON JERUK':        '#e34948',   // merah
        'KEMBANGAN':          '#4a3aa7',   // ungu
        'GROGOL PETAMBURAN':  '#2a78d6',   // biru
        'PALMERAH':           '#008300',   // hijau
        'TAMBORA':            '#1baf7a',   // teal
        'TAMAN SARI':         '#eb6834'    // oranye
    };
    // Ambil warna sebuah kecamatan (case-insensitive). Fallback abu netral.
    window.warnaKecamatan = function (n) {
        return window.WARNA_KEC[String(n || '').toUpperCase().trim()] || '#9e9e9e';
    };
    // Palet kategorikal umum untuk chart NON-kecamatan (mis. per bulan / per jenis)
    window.CAT_COLORS = ['#2a78d6','#1baf7a','#eda100','#008300','#4a3aa7','#e34948','#e87ba4','#eb6834'];
</script>
