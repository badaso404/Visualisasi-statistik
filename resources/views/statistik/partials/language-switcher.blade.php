{{--
    Pemilih bahasa — dropdown berbendera.

    Disisipkan sekali saja di sidebar, bukan di tiap halaman modul. Sidebar
    dipakai seluruh modul, jadi satu tempat ini otomatis berlaku untuk semuanya
    dan tidak ada lagi markup yang perlu disalin saat modul baru ditambahkan.

    Bahasa sendiri memang sudah global sejak pindah ke prefix URL: yang berubah
    di sini murni letak dan bentuk kontrolnya.

    Tautannya <a> biasa, bukan form POST. Selain lebih ringan, tiap bahasa jadi
    punya URL sendiri yang bisa dibagikan, dibuka di tab baru, dan diindeks
    mesin pencari.

    getLocalizedURL($code) menerjemahkan URL yang sedang dibuka ke bahasa
    tujuan, jadi pengunjung tetap di halaman yang sama. Argumen keempat
    ($forceDefaultLocation) sengaja dibiarkan false: kalau dipaksa true, tautan
    bahasa Indonesia jadi /id/... dan setiap klik kena satu redirect.
--}}

@php
    $currentLocale = app()->getLocale();
    $locales       = LaravelLocalization::getSupportedLocales();
@endphp

@once
{{-- Bendera disimpan sebagai <symbol> dan dipanggil dengan <use> karena tiap
     bendera muncul dua kali (di tombol dan di daftar). Menyalin SVG-nya akan
     menggandakan id clipPath Union Jack — id kembar itu HTML tidak sah dan
     bisa membuat potongan garis merahnya salah acuan. --}}
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
    <defs>
        {{-- Sang Saka Merah Putih --}}
        <symbol id="langFlagId" viewBox="0 0 60 30">
            <rect width="60" height="15" fill="#ce1126"/>
            <rect y="15" width="60" height="15" fill="#fff"/>
        </symbol>

        {{-- Union Jack. Dua clipPath: yang pertama mengurung gambar di dalam
             kotak bendera, yang kedua memotong garis merah miring jadi
             selang-seling seperti aslinya. --}}
        <symbol id="langFlagEn" viewBox="0 0 60 30">
            <clipPath id="langFlagEnBox">
                <path d="M0,0 v30 h60 v-30 z"/>
            </clipPath>
            <clipPath id="langFlagEnDiag">
                <path d="M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z"/>
            </clipPath>
            <g clip-path="url(#langFlagEnBox)">
                <path d="M0,0 v30 h60 v-30 z" fill="#012169"/>
                <path d="M0,0 L60,30 M60,0 L0,30" stroke="#fff" stroke-width="6"/>
                <path d="M0,0 L60,30 M60,0 L0,30" clip-path="url(#langFlagEnDiag)" stroke="#c8102e" stroke-width="4"/>
                <path d="M30,0 v30 M0,15 h60" stroke="#fff" stroke-width="10"/>
                <path d="M30,0 v30 M0,15 h60" stroke="#c8102e" stroke-width="6"/>
            </g>
        </symbol>
    </defs>
</svg>

@push('styles')
<style>
    .lang-dropdown { position: relative; margin-bottom: 14px; }

    /* Sengaja meniru .dropdown-tahun supaya kedua pemilih di halaman ini
       terbaca sebagai kontrol sejenis. */
    .lang-dropdown-btn {
        display: flex; align-items: center; gap: 8px; width: 100%;
        border: 2px solid #ffbf00; border-radius: 8px; background: #fff;
        color: #b8860b; font-weight: 700; font-size: 13px;
        padding: 9px 12px; cursor: pointer; user-select: none; text-align: left;
    }
    .lang-dropdown-btn .lang-name { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .lang-dropdown-btn .arrow { margin-left: auto; font-size: 9px; transition: transform .15s; }
    .lang-dropdown.open .lang-dropdown-btn .arrow { transform: rotate(180deg); }

    .lang-dropdown-menu {
        display: none; position: absolute; z-index: 30;
        top: calc(100% + 4px); left: 0; right: 0;
        background: #fff; border: 1px solid #eee; border-radius: 8px;
        box-shadow: 0 10px 30px rgba(76, 78, 100, .15); overflow: hidden;
    }
    .lang-dropdown.open .lang-dropdown-menu { display: block; }
    .lang-dropdown-menu a {
        display: flex; align-items: center; gap: 8px;
        padding: 9px 12px; font-size: 13px; font-weight: 600;
        color: #555; text-decoration: none;
    }
    .lang-dropdown-menu a:hover  { background: #fff8e1; color: #b8860b; }
    .lang-dropdown-menu a.active { background: #ffbf00; color: #fff; }

    .lang-flag {
        width: 22px; height: 11px; flex-shrink: 0; border-radius: 2px;
        /* Bendera Indonesia separuhnya putih; tanpa garis tepi ia lenyap di
           atas latar putih maupun di baris aktif yang kuning. */
        box-shadow: 0 0 0 1px rgba(0, 0, 0, .18);
    }

    /* Sidebar melebar penuh di layar kecil; dropdown-nya jangan ikut melar. */
    @media (max-width: 768px) {
        .lang-dropdown { max-width: 220px; margin-bottom: 10px; }
    }
</style>
@endpush

@push('scripts')
<script>
    // Dropdown sederhana: buka/tutup, tutup saat klik di luar atau tekan Esc.
    (function () {
        var root = document.getElementById('langDropdown');
        if (!root) return;

        var btn = root.querySelector('.lang-dropdown-btn');

        function close() {
            root.classList.remove('open');
            btn.setAttribute('aria-expanded', 'false');
        }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var open = root.classList.toggle('open');
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        document.addEventListener('click', function (e) {
            if (!root.contains(e.target)) close();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') close();
        });
    })();
</script>
@endpush
@endonce

<div class="lang-dropdown" id="langDropdown">
    <button type="button" class="lang-dropdown-btn"
            aria-haspopup="listbox" aria-expanded="false"
            aria-label="{{ __('common.lang_switch') }}">
        <svg class="lang-flag" aria-hidden="true"><use href="#langFlag{{ ucfirst($currentLocale) }}"/></svg>
        <span class="lang-name">{{ $locales[$currentLocale]['native'] }}</span>
        <span class="arrow">&#9660;</span>
    </button>

    <div class="lang-dropdown-menu" role="listbox">
        @foreach ($locales as $code => $properties)
            @php $isActive = $currentLocale === $code; @endphp
            <a href="{{ LaravelLocalization::getLocalizedURL($code) }}"
               role="option"
               aria-selected="{{ $isActive ? 'true' : 'false' }}"
               rel="alternate"
               hreflang="{{ $code }}"
               lang="{{ $code }}"
               class="{{ $isActive ? 'active' : '' }}">
                <svg class="lang-flag" aria-hidden="true"><use href="#langFlag{{ ucfirst($code) }}"/></svg>
                <span>{{ $properties['native'] }}</span>
            </a>
        @endforeach
    </div>
</div>
