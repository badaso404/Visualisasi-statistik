{{--
    Tombol unduh CSV untuk tabel di halaman publik.

    Pemakaian:
        @include('statistik.partials.unduh-tabel', [
            'target' => '#tabel-penduduk',
            'nama'   => 'kependudukan-per-kecamatan-' . $tahun,
        ])

    Isinya dibaca dari tabel yang SUDAH tampil, jadi berkas yang terunduh selalu
    sama persis dengan yang dilihat pengunjung — termasuk filter tahun yang
    sedang aktif, tanpa perlu route atau query tambahan.

    Penanda opsional pada sel:
      data-unduh-nilai="312450"  -> dipakai menggantikan teks sel, untuk angka
                                    yang di layar diformat ("312.450") agar di
                                    Excel tetap terbaca sebagai bilangan.
      data-unduh-abaikan          -> kolom dilewati (mis. kolom aksi/ikon).

    Penanda opsional pada <table>, untuk membersihkan pemisah ribuan tanpa perlu
    menambah data-unduh-nilai di setiap sel:
      data-unduh-angka="id"  -> layar memakai format Indonesia  "1.234,5" -> 1234.5
      data-unduh-angka="en"  -> layar memakai format Inggris    "1,234.5" -> 1234.5

    Keduanya hanya berlaku mulai kolom kedua; kolom pertama dianggap label
    (nama kecamatan, bulan) yang titik/komanya justru harus dipertahankan.
--}}
@php
    $namaBerkas = $nama ?? 'tabel';
@endphp

<button type="button" class="btn-unduh-tabel"
        data-unduh-tabel="{{ $target }}"
        data-unduh-nama="{{ $namaBerkas }}">
    <i class="fa fa-file-csv"></i> Unduh CSV
</button>

@once
@push('styles')
<style>
    .btn-unduh-tabel {
        display:inline-flex; align-items:center; gap:6px;
        padding:6px 12px; border:1px solid #d5d5d5; border-radius:6px;
        background:#fff; color:#555; font-size:.82rem; font-weight:500;
        cursor:pointer; transition:all .2s;
    }
    .btn-unduh-tabel:hover { background:#ffbf00; border-color:#ffbf00; color:#fff; }
    .btn-unduh-tabel:disabled { opacity:.5; cursor:not-allowed; }
</style>
@endpush

@push('scripts')
<script>
(function () {
    // Bungkus nilai sesuai aturan CSV: tanda kutip digandakan, dan sel yang
    // mengandung pemisah/baris baru dikutip agar tidak memecah kolom.
    function selCsv(teks) {
        var isi = (teks == null ? '' : String(teks)).trim().replace(/\s+/g, ' ');
        return /[",\n;]/.test(isi) ? '"' + isi.replace(/"/g, '""') + '"' : isi;
    }

    // Buang pemisah ribuan sesuai format yang dipakai di layar, supaya angkanya
    // masuk Excel sebagai bilangan dan bukan teks. Hanya dijalankan bila sel
    // memang murni angka; "12 unit" atau "-" dibiarkan apa adanya.
    function bersihkanAngka(teks, format) {
        if (!format) return teks;

        var isi = teks.replace(/%/g, '').trim();
        var pola = format === 'id' ? /^-?[\d.]+(,\d+)?$/ : /^-?[\d,]+(\.\d+)?$/;
        if (!pola.test(isi)) return teks;

        return format === 'id'
            ? isi.replace(/\./g, '').replace(',', '.')
            : isi.replace(/,/g, '');
    }

    function barisDari(tr, format) {
        var sel = tr.querySelectorAll('th, td');
        var out = [];
        for (var i = 0; i < sel.length; i++) {
            var c = sel[i];
            if (c.hasAttribute('data-unduh-abaikan')) continue;

            // Angka yang di layar sudah diformat boleh menyertakan nilai
            // mentahnya lewat data-unduh-nilai; itu selalu diprioritaskan.
            if (c.hasAttribute('data-unduh-nilai')) {
                out.push(selCsv(c.getAttribute('data-unduh-nilai')));
                continue;
            }

            var teks = c.innerText.trim();
            // Kolom pertama adalah label (nama kecamatan, bulan) — titik dan
            // komanya bagian dari nama, bukan pemisah ribuan.
            out.push(selCsv(out.length === 0 ? teks : bersihkanAngka(teks, format)));
        }
        return out.join(',');
    }

    document.addEventListener('click', function (e) {
        var tombol = e.target.closest('[data-unduh-tabel]');
        if (!tombol) return;

        var tabel = document.querySelector(tombol.dataset.unduhTabel);
        if (!tabel) return;

        var format = tabel.getAttribute('data-unduh-angka');
        var baris  = [];

        // Baris tbody yang disembunyikan (hasil pencarian atau paginasi di sisi
        // klien) tetap diikutkan: pengunjung menekan "Unduh CSV" untuk
        // mendapatkan tabelnya utuh, bukan sepotong halaman yang sedang tampak.
        tabel.querySelectorAll('thead tr, tbody tr').forEach(function (tr) {
            var isi = barisDari(tr, format);
            if (isi.replace(/,/g, '').trim() !== '') baris.push(isi);
        });

        if (baris.length === 0) return;

        // BOM di depan supaya Excel membaca berkas sebagai UTF-8 dan nama
        // kelurahan berhuruf khusus tidak berubah jadi karakter aneh.
        var blob = new Blob(["﻿" + baris.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
        var url  = URL.createObjectURL(blob);
        var a    = document.createElement('a');
        a.href = url;
        a.download = (tombol.dataset.unduhNama || 'tabel') + '.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });
})();
</script>
@endpush
@endonce
