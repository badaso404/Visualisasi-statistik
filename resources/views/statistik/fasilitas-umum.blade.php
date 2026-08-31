@extends('landing-page.layout.app')
@section('page_title', __('fasilitas.page_title') . ' - Jakarta Barat')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css"/>
<style>
    /* ── Layout ─────────────────────────────────────────────── */
    .kes-wrapper  { display:flex; gap:24px; padding:40px 0; }
    .kes-content  { flex:1; min-width:0; }

    /* ── Page header ────────────────────────────────────────── */
    .stat-header-wrap { display:flex; align-items:center; gap:12px; margin-bottom:24px; }
    .stat-header {
        flex:1; background:#ffbf00; color:#fff; text-align:center;
        padding:14px; border-radius:8px; font-weight:700;
        font-size:18px; letter-spacing:1px;
    }

    /* ── Stat cards ─────────────────────────────────────────── */
    .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:16px; }
    .stat-card {
        background:#f9f9f9; border:1px solid #eee; border-radius:8px;
        padding:16px 24px; position:relative; overflow:hidden;
    }
    .sc-card-body  { display:flex; justify-content:space-between; align-items:flex-start; margin-top:8px; }
    .sc-card-left  { flex:1; min-width:0; }
    .sc-icon {
        width:48px; height:48px; border-radius:12px;
        display:flex; align-items:center; justify-content:center;
        font-size:22px; flex-shrink:0; margin-left:12px;
        background:#2a78d6; color:#fff;
    }
    .sc-icon.ic-blue   { background:#2a78d6; }
    .sc-icon.ic-orange { background:#eb6834; }
    .sc-icon.ic-green  { background:#008300; }
    .sc-icon.ic-violet { background:#4a3aa7; }
    .sc-label { font-size:12px; font-weight:600; color:#888; letter-spacing:1px; text-transform:uppercase; margin-bottom:4px; }
    .sc-value { font-size:28px; font-weight:700; color:#333; line-height:1.15; margin-bottom:6px; }
    .sc-value.sm { font-size:19px; }
    .sc-desc  { font-size:11px; color:#aaa; }

    /* ── Panel card ─────────────────────────────────────────── */
    .panel-card { background:#fff; border:1px solid #ebebeb; border-radius:12px; padding:22px; }
    .pc-header  { display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; }
    .pc-title   { font-size:15px; font-weight:700; color:#1a1a1a; margin:0; display:flex; align-items:center; gap:8px; }
    .pc-title i { color:#ffbf00; }

    .fas-grid { display:grid; grid-template-columns:2fr 1fr; gap:16px; margin-bottom:16px; }

    /* ── Legenda kategori ───────────────────────────────────── */
    .kat-legend { display:flex; flex-wrap:wrap; gap:10px 16px; font-size:11px; color:#666; font-weight:600; margin-top:14px; }
    .kat-legend .dot { width:9px; height:9px; border-radius:50%; display:inline-block; margin-right:5px; }

    /* ── Peta ───────────────────────────────────────────────── */
    .map-card  { background:#fff; border:1px solid #ebebeb; border-radius:12px; padding:22px; margin-bottom:16px; }
    .fas-map   { width:100%; height:420px; border-radius:8px; border:1px solid #eee; z-index:0; }
    .map-note  { font-size:11px; color:#aaa; margin-top:10px; }
    .map-empty {
        display:flex; align-items:center; justify-content:center; text-align:center;
        height:180px; border:1px dashed #e0e0e0; border-radius:8px;
        color:#aaa; font-size:13px; padding:20px;
    }

    /* ── Tabel ──────────────────────────────────────────────────
       Kerangka tabel, pencarian, dan paginasi kini dari komponen
       statistik.tabel (assets/statistik/css/tabel.css). Yang tersisa di sini
       hanya gaya khas fasilitas umum. */
    .td-alamat { color:#888; font-size:12px; max-width:340px; }

    .badge-kat {
        display:inline-flex; align-items:center; gap:5px; white-space:nowrap;
        font-size:11px; font-weight:700; padding:4px 10px; border-radius:20px;
        color:#fff;
    }
    /* Warna lencana lewat kelas, bukan style sebaris: dengan 776 baris,
       mengulang atribut style di tiap baris menambah puluhan kilobyte
       untuk enam nilai warna yang itu-itu saja. */
@foreach (\App\Models\FasilitasUmum::WARNA as $slug => $warna)
    .badge-kat.kat-{{ $slug }} { background: {{ $warna }}; }
@endforeach
    .badge-kosong { background:#f0f0f0; color:#999; font-size:11px; padding:3px 8px; border-radius:20px; }


    /* ── Responsive ─────────────────────────────────────────── */
    @media (max-width: 992px) {
        .stat-grid { grid-template-columns: repeat(2,1fr); }
        .fas-grid  { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .kes-wrapper { flex-direction: column; padding: 20px 0; gap: 16px; }
        .stat-header { font-size: 15px; padding: 12px; }
    }
    @media (max-width: 520px) {
        .stat-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
@php
    // Nama kelas ditulis lengkap sekali di sini lalu dipakai lewat variabel,
    // supaya sisa view tidak berulang-ulang menyebut namespace-nya.
    $warnaKategori  = \App\Models\FasilitasUmum::WARNA;
    $ikonKategori   = \App\Models\FasilitasUmum::IKON;
    $daftarKategori = \App\Models\FasilitasUmum::KATEGORI;
    $labelKategori  = fn ($slug) => \App\Models\FasilitasUmum::label($slug);
    $terakhir       = $semua->max('updated_at');
@endphp

<div class="container-fluid px-4">
    <div class="kes-wrapper">

        @include('statistik.partials.sidebar')

        {{-- ── KONTEN ───────────────────────────────────── --}}
        <div class="kes-content">

            {{-- Header. Tidak ada dropdown tahun di modul ini: data sumbernya
                 berupa inventaris tanpa penanda periode. --}}
            <div class="stat-header-wrap">
                <div class="stat-header">{{ __('fasilitas.header') }}</div>
            </div>

            {{-- ── 4 Stat Cards ────────────────────────── --}}
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">{{ __('fasilitas.card_total') }}</div>
                            <div class="sc-value">{{ nf($ringkasan['total']) }}</div>
                            <div class="sc-desc">{{ __('fasilitas.card_total_desc', ['jumlah' => nf($ringkasan['kecamatan_terisi'])]) }}</div>
                        </div>
                        <div class="sc-icon ic-blue"><i class="fa fa-building-columns"></i></div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">{{ __('fasilitas.card_kategori') }}</div>
                            <div class="sc-value sm">
                                {{ $ringkasan['kategori_top'] ? $labelKategori($ringkasan['kategori_top']) : '—' }}
                            </div>
                            <div class="sc-desc">{{ __('fasilitas.card_kategori_desc', ['jumlah' => nf($ringkasan['kategori_top_n'])]) }}</div>
                        </div>
                        <div class="sc-icon ic-violet">
                            <i class="fa {{ $ikonKategori[$ringkasan['kategori_top']] ?? 'fa-layer-group' }}"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">{{ __('fasilitas.card_kecamatan') }}</div>
                            <div class="sc-value sm">{{ $ringkasan['kecamatan_top'] ?? '—' }}</div>
                            <div class="sc-desc">{{ __('fasilitas.card_kecamatan_desc', ['jumlah' => nf($ringkasan['kecamatan_top_n'])]) }}</div>
                        </div>
                        <div class="sc-icon ic-orange"><i class="fa fa-map-location-dot"></i></div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">{{ __('fasilitas.card_rasio') }}</div>
                            <div class="sc-value">{{ $ringkasan['rasio'] !== null ? nf($ringkasan['rasio'], 2) : '—' }}</div>
                            <div class="sc-desc">
                                {{ $ringkasan['rasio'] !== null
                                    ? __('fasilitas.card_rasio_desc', ['tahun' => $ringkasan['tahun_penduduk']])
                                    : __('fasilitas.card_rasio_kosong') }}
                            </div>
                        </div>
                        <div class="sc-icon ic-green"><i class="fa fa-users-between-lines"></i></div>
                    </div>
                </div>
            </div>

            {{-- ── Grafik ──────────────────────────────── --}}
            <div class="fas-grid">
                <div class="panel-card">
                    <div class="pc-header">
                        <h3 class="pc-title"><i class="fa fa-chart-column"></i> {{ __('fasilitas.panel_sebaran') }}</h3>
                    </div>
                    <div id="chart-sebaran"></div>
                    <div class="kat-legend">
                        @foreach ($daftarKategori as $slug => $label)
                            <span><span class="dot" style="background: {{ $warnaKategori[$slug] }}"></span>{{ $labelKategori($slug) }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="panel-card">
                    <div class="pc-header">
                        <h3 class="pc-title"><i class="fa fa-chart-pie"></i> {{ __('fasilitas.panel_komposisi') }}</h3>
                    </div>
                    <div id="chart-komposisi"></div>
                </div>
            </div>

            {{-- ── Peta ────────────────────────────────── --}}
            <div class="map-card">
                <div class="pc-header">
                    <h3 class="pc-title"><i class="fa fa-map-location-dot"></i> {{ __('fasilitas.map_title') }}</h3>
                </div>

                @if ($titik->isEmpty())
                    {{-- Peta kosong tanpa keterangan mudah disalahpahami sebagai
                         "tidak ada fasilitas", padahal datanya ada — yang belum
                         ada koordinatnya. --}}
                    <div class="map-empty">{{ __('fasilitas.map_kosong') }}</div>
                @else
                    <div id="map-fasilitas" class="fas-map"></div>
                    <div class="map-note">
                        {{ __('fasilitas.map_catatan', [
                            'tanpa' => nf($ringkasan['total'] - $titik->count()),
                            'total' => nf($ringkasan['total']),
                        ]) }}
                    </div>
                @endif
            </div>

            {{-- ── Tabel ───────────────────────────────── --}}
            <x-statistik.tabel
                id="tabel-fasilitas-umum"
                :judul="__('fasilitas.table_title')"
                :subjudul="__('fasilitas.table_sub', ['total' => nf($ringkasan['total'])])"
                :kolom="[
                    __('fasilitas.col_nama'),
                    __('fasilitas.col_kategori'),
                    __('fasilitas.col_kecamatan'),
                    __('fasilitas.col_kelurahan'),
                    __('fasilitas.col_alamat'),
                ]"
                :per-halaman="15"
                :lebar-label="280"
                :lebar-kolom="180"
                :sumber="__('fasilitas.source', [
                    'tanggal' => $terakhir ? \Carbon\Carbon::parse($terakhir)->translatedFormat('d F Y') : '-',
                ])"
                berkas="fasilitas-umum-jakarta-barat"
            >
                <x-slot:alat>
                    <select data-stat-filter="kategori" class="stat-cari-input" style="width:auto; padding:0 16px;">
                        <option value="ALL">{{ __('fasilitas.filter_semua') }}</option>
                        @foreach ($daftarKategori as $slug => $label)
                            <option value="{{ $slug }}">{{ $labelKategori($slug) }} ({{ $perKategori[$slug] ?? 0 }})</option>
                        @endforeach
                    </select>
                </x-slot:alat>

                {{-- Ratusan baris dicetak sekaligus supaya pencarian dan
                     penyaringan berjalan tanpa memuat ulang halaman. Karena
                     itu markup per barisnya ditulis rapat: indentasi Blade
                     yang wajar untuk 8 baris menjadi ratusan kilobyte spasi
                     kosong pada 776 baris. Kunci pencarian juga tidak ditulis
                     sebagai atribut data-cari — tanpa itu komponen menyusun
                     kuncinya sendiri sekali dari teks baris. --}}
                @foreach ($semua as $f)
                <tr data-kategori="{{ $f->kategori }}"><td><span class="stat-nama" style="white-space:normal;">{{ $f->nama }}</span></td><td><span class="badge-kat kat-{{ $f->kategori }}"><i class="fa {{ $f->ikon() }}"></i> {{ $f->labelKategori() }}</span></td><td><span class="stat-nilai">@if ($f->kecamatan){{ $f->kecamatan->nama_kecamatan }}@else<span class="badge-kosong">{{ __('fasilitas.belum_diisi') }}</span>@endif</span></td><td><span class="stat-nilai">{{ $f->kelurahan ?: '—' }}</span></td><td><span class="stat-nilai td-alamat">{{ $f->alamat ?: '—' }}</span></td></tr>
                @endforeach
            </x-statistik.tabel>

        </div>{{-- /.kes-content --}}
    </div>{{-- /.kes-wrapper --}}
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
(function () {
    // ── Peta titik fasilitas ──────────────────────────────────────
    // Berbeda dari peta infrastruktur digital yang posisinya digenerate acak
    // di dalam polygon kecamatan: di sini tiap titik adalah koordinat asli
    // sebuah fasilitas, jadi tidak ada yang perlu disebar-sebar sendiri.
    var titik = {!! json_encode($titik) !!};
    var el = document.getElementById('map-fasilitas');
    if (typeof L === 'undefined' || !el || !titik.length) return;

    var map = L.map('map-fasilitas', { scrollWheelZoom: false }).setView([-6.168, 106.785], 12);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        maxZoom: 19, attribution: '&copy; OpenStreetMap &copy; CARTO'
    }).addTo(map);

    var bounds = [];
    titik.forEach(function (t) {
        L.circleMarker([t.lat, t.lng], {
            radius: 6, color: '#fff', weight: 1.5,
            fillColor: t.warna, fillOpacity: 0.9
        }).addTo(map).bindPopup(
            '<b>' + t.nama + '</b><br>' + t.kategori + '<br><span style="color:#888">' + t.kecamatan + '</span>'
        );
        bounds.push([t.lat, t.lng]);
    });

    if (bounds.length) map.fitBounds(bounds, { padding: [30, 30], maxZoom: 15 });
})();
</script>
<script>
(function () {
    // Pemisah ribuan/desimal ikut bahasa aktif (lihat helper nf()).
    var fmt = function (v) { return Number(v).toLocaleString('{{ locale_angka_js() }}'); };

    // ── Chart sebaran per kecamatan (batang bertumpuk per kategori) ──
    var kecNama = {!! json_encode($perKecamatan->pluck('nama')->values()) !!};
    var seri    = {!! json_encode(
        collect(App\Models\FasilitasUmum::KATEGORI)->keys()->map(fn ($slug) => [
            'name' => App\Models\FasilitasUmum::label($slug),
            'data' => $perKecamatan->map(fn ($r) => (int) ($r['kategori'][$slug] ?? 0))->values(),
        ])->values()
    ) !!};
    var warna = {!! json_encode(array_values(App\Models\FasilitasUmum::WARNA)) !!};

    if (document.querySelector('#chart-sebaran') && kecNama.length) {
        new ApexCharts(document.querySelector('#chart-sebaran'), {
            chart: {
                type: 'bar', height: 360, stacked: true, toolbar: { show: false },
                fontFamily: 'inherit', animations: { enabled: true, speed: 500 },
            },
            series: seri,
            colors: warna,
            plotOptions: { bar: { horizontal: true, borderRadius: 3, barHeight: '70%' } },
            dataLabels: { enabled: false },
            // Sekat setipis latar di antara segmen: tanpa ini dua kategori yang
            // bersebelahan pada batang bertumpuk terbaca seperti satu blok.
            stroke: { width: 2, colors: ['#fff'] },
            xaxis: {
                categories: kecNama,
                labels: { style: { fontSize: '11px', colors: '#888' }, formatter: fmt },
                axisBorder: { show: false }, axisTicks: { show: false },
            },
            yaxis: { labels: { style: { fontSize: '11px', colors: '#666' } } },
            // Legenda sudah digambar sendiri di bawah grafik (.kat-legend)
            // supaya warnanya konsisten dengan lencana pada tabel.
            legend: { show: false },
            grid: { borderColor: '#f0f0f0', strokeDashArray: 3, xaxis: { lines: { show: true } }, yaxis: { lines: { show: false } } },
            tooltip: { theme: 'light', y: { formatter: fmt } },
        }).render();
    }

    // ── Chart komposisi kategori (donat) ──────────────────────────
    var kompLabel = {!! json_encode(
        collect(App\Models\FasilitasUmum::KATEGORI)->keys()
            ->map(fn ($slug) => App\Models\FasilitasUmum::label($slug))->values()
    ) !!};
    var kompData = {!! json_encode(
        collect(App\Models\FasilitasUmum::KATEGORI)->keys()
            ->map(fn ($slug) => (int) ($perKategori[$slug] ?? 0))->values()
    ) !!};

    if (document.querySelector('#chart-komposisi') && kompData.some(function (v) { return v > 0; })) {
        new ApexCharts(document.querySelector('#chart-komposisi'), {
            chart: { type: 'donut', height: 360, fontFamily: 'inherit' },
            series: kompData,
            labels: kompLabel,
            colors: warna,
            legend: { position: 'bottom', fontSize: '11px', markers: { radius: 4 } },
            dataLabels: { enabled: true, style: { fontSize: '10px' }, dropShadow: { enabled: false } },
            plotOptions: { pie: { donut: { size: '58%' } } },
            stroke: { width: 2, colors: ['#fff'] },
            tooltip: { theme: 'light', y: { formatter: fmt } },
        }).render();
    }

    // Pencarian, penyaring kategori, paginasi, dan unduh CSV kini ditangani
    // komponen statistik.tabel bersama partial unduh-tabel.
})();
</script>
@endpush
