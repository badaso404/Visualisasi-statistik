{{--
    Tabel statistik — tampilan bersama semua modul publik.

    Kerangkanya (kartu, judul, pencarian, unduh CSV, paginasi, footer sumber)
    diangkat dari tabel modul geografis, lalu dilepas dari ketergantungannya
    pada #geo-table dan pada jumlah kolom yang tepat tujuh.

    Pemakaian — perhatikan kurung siku sengaja dihilangkan dari contoh di
    bawah ini. Blade mengompilasi tag komponen bahkan di dalam komentar, jadi
    menuliskannya utuh membuat berkas ini memanggil dirinya sendiri:

        x-statistik.tabel
            id="tabel-faskes"
            :judul="__('kesehatan.table_title')"
            :subjudul="__('kesehatan.table_sub', ['tahun' => $tahun])"
            :kolom="[ __('kesehatan.col_kecamatan'), __('kesehatan.col_total') ]"
            :per-halaman="8"
            :sumber="$summary->sumber"
            :berkas="'faskes-' . $tahun"

            → isi slot: satu <tr> per baris, mis.
              <tr data-cari="cengkareng"> <td>…</td> </tr>

        /x-statistik.tabel

    Baris dikirim lewat slot, bukan lewat array data. Isi tiap modul terlalu
    berbeda — ada lencana status, ada tautan, ada sel dua nilai — dan
    menormalkannya jadi satu bentuk hanya akan memindahkan percabangan ke
    dalam komponen. Yang diseragamkan adalah kerangka dan perilakunya.

    Catatan pemakaian:
      • Kolom PERTAMA otomatis rata kiri (kolom label); sisanya center.
      • Beri `data-cari` pada <tr> berisi teks yang boleh dicari. Kalau tidak
        ada, pencarian jatuh ke seluruh teks baris.
      • per-halaman = 0 / null mematikan paginasi (tabel pendek tetap utuh).
--}}

@props([
    'id',
    'judul',
    'subjudul'   => null,
    'kolom'      => [],
    'cari'       => true,
    'perHalaman' => 0,
    'sumber'     => null,
    'berkas'     => null,
    'lebarLabel' => 240,   // lebar minimum kolom pertama (px)
    'lebarKolom' => 130,   // lebar minimum kolom angka (px)
])

@php
    $jumlahKolom = count($kolom);

    // Lebar minimum tabel dihitung dari jumlah kolom, bukan dipatok. Tabel
    // lima kolom jadi tidak ikut memaksa scroll horizontal seperti dulu.
    $lebarMin = $jumlahKolom > 0
        ? $lebarLabel + ($jumlahKolom - 1) * $lebarKolom
        : 0;

    $paginasi = (int) $perHalaman > 0;
@endphp

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/statistik/css/tabel.css') }}">
    @endpush
@endonce

{{-- Warna per kolom. Tidak bisa ikut tabel.css karena jumlah kolomnya
     berbeda tiap tabel; diturunkan dari satu warna dasar per kolom
     (config/statistik.warna.kategori) lewat warna_campur(). --}}
@push('styles')
<style>
    @foreach ($kolom as $i => $label)
        @php
            $dasar = warna_kategori($i);
        @endphp
        #{{ $id }} thead th:nth-child({{ $i + 1 }}) {
            background-color: {{ $dasar }};
            background-image: linear-gradient(135deg,
                {{ warna_campur($dasar, '#ffffff', 0.18) }} 0%,
                {{ warna_campur($dasar, '#000000', 0.12) }} 100%);
        }
        #{{ $id }} tbody td:nth-child({{ $i + 1 }}) {
            background: {{ warna_campur($dasar, '#ffffff', 0.92) }};
        }
        #{{ $id }} tbody tr:hover td:nth-child({{ $i + 1 }}) {
            background: {{ warna_campur($dasar, '#ffffff', 0.85) }};
        }
    @endforeach

    /* Baris yang disorot modul (mis. tahun yang sedang dipilih). Harus ikut
       memakai #id supaya menang atas tint kolom di atas — sebuah kelas biasa
       kalah spesifisitas dari selektor ber-id, dan sorotannya tak akan
       kelihatan. Ditulis paling akhir agar juga menang atas aturan :hover. */
    #{{ $id }} tbody tr.stat-baris-aktif td {
        background: #fff8e1;
        box-shadow: inset 0 1px 0 #f0dcab, inset 0 -1px 0 #f0dcab;
    }

    #{{ $id }} tbody tr.stat-baris-aktif .stat-nilai,
    #{{ $id }} tbody tr.stat-baris-aktif .stat-nama {
        color: #7a5b00;
        font-weight: 700;
    }
</style>
@endpush

<div {{ $attributes->merge(['class' => 'stat-tabel-wrap']) }}
     data-stat-tabel
     @if ($paginasi) data-per-halaman="{{ (int) $perHalaman }}" @endif>

    {{-- Kepala --}}
    <div class="stat-tabel-top">
        <div class="stat-tabel-header">

            <div class="stat-tabel-heading">
                <p class="tbl-title">{{ $judul }}</p>
                @if ($subjudul)
                    <p class="tbl-subtitle">{{ $subjudul }}</p>
                @endif
                <span class="stat-title-accent"></span>
            </div>

            <div class="stat-tabel-tools">
                {{-- Kontrol tambahan milik modul (mis. penyaring jenis bencana).
                     Sebuah <select data-stat-filter="jenis"> otomatis ikut
                     menyaring baris berdasarkan atribut data-jenis-nya. --}}
                {{ $alat ?? '' }}

                @if ($cari)
                    <div class="stat-cari-box">
                        <span class="stat-cari-icon">
                            <svg width="21" height="21" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" />
                                <path d="M20 20L16.5 16.5" stroke="currentColor"
                                      stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </span>
                        <input type="text"
                               class="stat-cari-input"
                               data-stat-cari
                               aria-controls="{{ $id }}"
                               placeholder="{{ __('common.tabel_cari') }}">
                    </div>
                @endif

                @include('statistik.partials.unduh-tabel', [
                    'target' => '#' . $id,
                    'nama'   => $berkas ?? $id,
                ])
            </div>

        </div>
    </div>

    {{-- Tabel --}}
    <div class="stat-tabel-content">
        <div class="stat-tabel-responsive">
            <table id="{{ $id }}"
                   class="stat-tabel"
                   style="--stat-tabel-min: {{ $lebarMin }}px;"
                   data-unduh-angka="{{ app()->getLocale() }}">
                <thead>
                    <tr>
                        @foreach ($kolom as $label)
                            <th scope="col">{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody data-stat-body>
                    {{ $slot }}

                    {{-- Ditampilkan JS saat pencarian tidak menemukan apa pun.
                         data-unduh-abaikan supaya kalimat ini tidak ikut
                         terbawa ke berkas CSV sebagai baris data. --}}
                    <tr data-stat-kosong hidden>
                        <td class="stat-tabel-kosong" data-unduh-abaikan
                            colspan="{{ max($jumlahKolom, 1) }}">
                            {{ __('common.tabel_kosong') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginasi --}}
    @if ($paginasi)
        <div class="stat-pagination">
            <div class="stat-pager-info" data-stat-info></div>
            <div class="stat-pager" data-stat-pager></div>
        </div>
    @endif

    {{-- Sumber --}}
    @if ($sumber)
        <div class="stat-source-footer">
            <span class="stat-source-icon">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                     xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M12 3L19 6V11C19 15.5 16.1 19.1 12 21C7.9 19.1 5 15.5 5 11V6L12 3Z"
                          stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                    <path d="M9.5 11.5L11.2 13.2L14.8 9.5" stroke="currentColor"
                          stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
            <span><strong>{{ __('common.tabel_sumber') }}</strong> {!! $sumber !!}</span>
        </div>
    @endif

</div>

@once
@push('scripts')
<script>
/**
 * Pencarian & paginasi untuk komponen statistik.tabel.
 *
 * Dipasang per tabel, bukan lewat variabel global seperti versi lama di
 * geografis. Itu yang membuat halaman dengan dua tabel (kemiskinan,
 * perekonomian) tidak bisa dipaginasi sebelum ini — keduanya berebut satu
 * `currentPage` yang sama.
 */
(function () {
    var TEKS = {
        info:   @json(__('common.tabel_info')),
        prev:   @json(__('common.tabel_sebelumnya')),
        next:   @json(__('common.tabel_berikutnya')),
    };

    function pasang(wrap) {
        var tbody = wrap.querySelector('[data-stat-body]');
        if (!tbody) return;

        var barisKosong = tbody.querySelector('[data-stat-kosong]');
        var input       = wrap.querySelector('[data-stat-cari]');
        var elInfo      = wrap.querySelector('[data-stat-info]');
        var elPager     = wrap.querySelector('[data-stat-pager]');

        var perHalaman  = parseInt(wrap.dataset.perHalaman, 10) || 0;
        var halaman     = 1;

        // Penyaring tambahan: <select data-stat-filter="jenis"> dicocokkan
        // dengan atribut data-jenis pada tiap baris. Nilai kosong atau "all"
        // berarti tidak menyaring.
        var penyaring = Array.prototype.slice.call(
            wrap.querySelectorAll('[data-stat-filter]')
        );

        // Baris yang ikut dicari & dipaginasi. Baris bertanda data-stat-tetap
        // (mis. baris TOTAL di tabel sektor perekonomian) tidak termasuk: ia
        // harus tetap terlihat di halaman mana pun dan tidak boleh ikut
        // terhitung sebagai "1 dari sekian baris".
        function semuaBaris() {
            return Array.prototype.filter.call(tbody.children, function (tr) {
                return !tr.hasAttribute('data-stat-kosong')
                    && !tr.hasAttribute('data-stat-tetap');
            });
        }

        // Kunci pencarian disusun sekali saja. Tabel fasilitas umum berisi
        // ratusan baris; membaca ulang textContent tiap huruf yang diketik
        // membuat pencarian tersendat.
        var kunci = null;

        function siapkanKunci(baris) {
            if (kunci) return;
            kunci = new WeakMap();
            baris.forEach(function (tr) {
                // data-cari dipakai kalau modul menyediakannya; kalau tidak,
                // seluruh teks baris ikut dicari — jadi kolom alamat atau
                // kategori pun ketemu, bukan cuma kolom pertama.
                var teks = tr.getAttribute('data-cari');
                if (teks === null) teks = tr.textContent;
                kunci.set(tr, teks.toLowerCase().replace(/\s+/g, ' ').trim());
            });
        }

        function cocok(tr, q) {
            for (var i = 0; i < penyaring.length; i++) {
                var nilai = penyaring[i].value;

                // "all" dikenali tanpa memandang huruf besar/kecil: tiap modul
                // menulis opsi "semua"-nya berbeda (all / ALL).
                if (!nilai || nilai.toLowerCase() === 'all') continue;

                if (tr.getAttribute('data-' + penyaring[i].dataset.statFilter) !== nilai) {
                    return false;
                }
            }

            if (!q) return true;

            return kunci.get(tr).indexOf(q) !== -1;
        }

        function render() {
            var q     = input ? input.value.toLowerCase().trim() : '';
            var baris = semuaBaris();

            siapkanKunci(baris);

            var lolos = baris.filter(function (tr) { return cocok(tr, q); });
            var total = lolos.length;

            var jmlHalaman = perHalaman
                ? Math.max(1, Math.ceil(total / perHalaman))
                : 1;

            if (halaman > jmlHalaman) halaman = jmlHalaman;

            var mulai = perHalaman ? (halaman - 1) * perHalaman : 0;
            var akhir = perHalaman ? Math.min(mulai + perHalaman, total) : total;

            baris.forEach(function (tr) { tr.hidden = true; });
            lolos.slice(mulai, akhir).forEach(function (tr) { tr.hidden = false; });

            if (barisKosong) barisKosong.hidden = total !== 0;

            // Baris tetap ikut disembunyikan saat pencarian tidak menemukan
            // apa pun — menyisakan baris TOTAL sendirian di bawah pesan
            // "tidak ada data" akan terbaca seolah totalnya nol.
            tbody.querySelectorAll('[data-stat-tetap]').forEach(function (tr) {
                tr.hidden = total === 0;
            });

            if (elInfo) {
                elInfo.textContent = total === 0 ? '' : TEKS.info
                    .replace(':dari', mulai + 1)
                    .replace(':sampai', akhir)
                    .replace(':total', total);
            }

            tandaiBarisAkhir();

            if (elPager) gambarPager(total, jmlHalaman);
        }

        // Membulatkan sudut bawah pada baris terakhir yang tampak — ikut
        // berpindah setiap kali halaman atau pencarian berubah.
        function tandaiBarisAkhir() {
            var tampak = Array.prototype.filter.call(tbody.children, function (tr) {
                return !tr.hidden;
            });

            Array.prototype.forEach.call(tbody.children, function (tr) {
                tr.classList.remove('stat-baris-akhir');
            });

            if (tampak.length) {
                tampak[tampak.length - 1].classList.add('stat-baris-akhir');
            }
        }

        function tombol(isi, label) {
            var b = document.createElement('button');
            b.type = 'button';
            b.innerHTML = isi;
            if (label) b.setAttribute('aria-label', label);
            return b;
        }

        function tombolHalaman(n) {
            var b = tombol(String(n));
            if (n === halaman) b.classList.add('active');
            b.onclick = function () { halaman = n; render(); };
            return b;
        }

        function elipsis() {
            var b = tombol('…');
            b.disabled = true;
            return b;
        }

        function gambarPager(total, jmlHalaman) {
            elPager.innerHTML = '';

            // Satu halaman tidak butuh navigasi apa pun.
            if (total === 0 || jmlHalaman <= 1) return;

            var prev = tombol('&#8249;', TEKS.prev);
            prev.disabled = halaman === 1;
            prev.onclick = function () {
                if (halaman > 1) { halaman--; render(); }
            };
            elPager.appendChild(prev);

            // Daftar fasilitas umum bisa puluhan halaman; kalau semua nomor
            // dicetak, barisan tombolnya lebih panjang dari tabelnya. Yang
            // ditampilkan hanya jendela di sekitar halaman aktif, plus
            // halaman pertama & terakhir sebagai jangkar.
            var dari = Math.max(1, halaman - 2);
            var ke   = Math.min(jmlHalaman, dari + 4);
            dari     = Math.max(1, ke - 4);

            if (dari > 1)  elPager.appendChild(tombolHalaman(1));
            if (dari > 2)  elPager.appendChild(elipsis());

            for (var p = dari; p <= ke; p++) {
                elPager.appendChild(tombolHalaman(p));
            }

            if (ke < jmlHalaman - 1) elPager.appendChild(elipsis());
            if (ke < jmlHalaman)     elPager.appendChild(tombolHalaman(jmlHalaman));

            var next = tombol('&#8250;', TEKS.next);
            next.disabled = halaman === jmlHalaman;
            next.onclick = function () {
                if (halaman < jmlHalaman) { halaman++; render(); }
            };
            elPager.appendChild(next);
        }

        function keHalamanSatu() {
            halaman = 1;
            render();
        }

        // Pencarian ditunda sesaat: mengetik cepat di daftar ratusan baris
        // akan memicu render berulang kali per huruf tanpa jeda ini.
        if (input) {
            var timer = null;
            input.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(keHalamanSatu, 150);
            });
        }

        penyaring.forEach(function (el) {
            el.addEventListener('change', keHalamanSatu);
        });

        render();
    }

    document.querySelectorAll('[data-stat-tabel]').forEach(pasang);
})();
</script>
@endpush
@endonce
