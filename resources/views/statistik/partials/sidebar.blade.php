{{-- Sidebar navigasi statistik — dipakai semua halaman modul agar konsisten. --}}
@once
@push('styles')
<style>
    .statistik-sidebar { width:220px; flex-shrink:0; }
    .statistik-sidebar .nav-link {
        display:flex; align-items:center; gap:10px;
        padding:12px 16px; border-radius:8px; color:#555;
        font-weight:500; margin-bottom:4px; transition:all .2s;
        text-decoration:none;
    }
    .statistik-sidebar .nav-link:hover  { background:#f0f0f0; color:#ffbf00; }
    .statistik-sidebar .nav-link.active { background:#ffbf00; color:#fff; }
    .statistik-sidebar .nav-link i      { width:18px; text-align:center; }

    @media (max-width: 768px) {
        .statistik-sidebar { width:100%; }
        .statistik-sidebar .nav {
            flex-direction:row !important; flex-wrap:nowrap;
            overflow-x:auto; gap:6px; padding-bottom:4px; -webkit-overflow-scrolling:touch;
        }
        .statistik-sidebar .nav-link { white-space:nowrap; margin-bottom:0; }
    }
</style>
@endpush
@endonce

@php
    $menu = [
        ['route' => 'statistik.overview',             'icon' => 'fa-table-columns',    'label' => __('common.nav_overview')],
        ['route' => 'statistik.geografis',            'icon' => 'fa-map',              'label' => __('common.nav_geografis')],
        ['route' => 'statistik.iklim',                'icon' => 'fa-cloud',            'label' => __('common.nav_iklim')],
        ['route' => 'statistik.kependudukan',         'icon' => 'fa-users',            'label' => __('common.nav_kependudukan')],
        ['route' => 'statistik.pendidikan',           'icon' => 'fa-graduation-cap',   'label' => __('common.nav_pendidikan')],
        ['route' => 'statistik.kesehatan',            'icon' => 'fa-plus-circle',      'label' => __('common.nav_kesehatan')],
        ['route' => 'statistik.bencana',              'icon' => 'fa-house-flood-water','label' => __('common.nav_bencana')],
        ['route' => 'statistik.kemiskinan',           'icon' => 'fa-hand-holding-heart','label' => __('common.nav_kemiskinan')],
        ['route' => 'statistik.perekonomian',         'icon' => 'fa-chart-line',       'label' => __('common.nav_perekonomian')],
        ['route' => 'statistik.infrastruktur-digital','icon' => 'fa-wifi',             'label' => __('common.nav_infrastruktur')],
        ['route' => 'statistik.fasilitas-umum',       'icon' => 'fa-building-columns', 'label' => __('common.nav_fasilitas')],
        ['route' => 'statistik.potensi-kelurahan',    'icon' => 'fa-city',             'label' => __('common.nav_podes')],
    ];
@endphp

<div class="statistik-sidebar">
    {{-- Pemilih bahasa untuk seluruh modul. Diletakkan di sini, bukan di
         halaman masing-masing, supaya cukup ada satu di seluruh portal. --}}
    @include('statistik.partials.language-switcher')

    <nav class="nav flex-column">
        @foreach ($menu as $m)
            <a class="nav-link {{ request()->routeIs($m['route']) ? 'active' : '' }}" href="{{ route($m['route']) }}">
                <i class="fa {{ $m['icon'] }}"></i> {{ $m['label'] }}
            </a>
        @endforeach
    </nav>
</div>
