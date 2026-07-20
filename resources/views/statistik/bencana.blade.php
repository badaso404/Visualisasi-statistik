@extends('landing-page.layout.app')

@push('styles')
<style>
    .statistik-wrapper { display: flex; gap: 24px; padding: 40px 0; }
    .statistik-sidebar { width: 220px; flex-shrink: 0; }
    .statistik-sidebar .nav-link {
        display: flex; align-items: center; gap: 10px;
        padding: 12px 16px; border-radius: 8px; color: #555;
        font-weight: 500; margin-bottom: 4px; transition: all 0.2s;
    }
    .statistik-sidebar .nav-link:hover { background: #f0f0f0; color: #ffbf00; }
    .statistik-sidebar .nav-link.active { background: #ffbf00; color: #fff; }
    .statistik-content { flex: 1; min-width: 0; }
    .stat-header-wrap { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
    .stat-header {
        background: #ffbf00; color: white;
        padding: 14px 20px; border-radius: 8px; font-weight: 700;
        font-size: 18px; letter-spacing: 1px;
        flex: 1;
        text-align: center;
    }
    .chart-card { background: #fff; border: 1px solid #eee; border-radius: 18px; padding: 22px; box-shadow: 0 10px 40px rgba(76, 78, 100, 0.05); }
    .chart-card .chart-title { font-size: 14px; font-weight: 700; color: #333; margin-bottom: 18px; letter-spacing: .5px; }
    .bencana-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .bencana-table th, .bencana-table td { padding: 14px 12px; border-bottom: 1px solid #f0f0f0; text-align: left; }
    .bencana-table th { background: #fafafa; color: #666; font-weight: 700; font-size: 12px; letter-spacing: .5px; }
    .badge-jenis { padding: 5px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; color: #fff; display: inline-flex; align-items: center; }
    .table-header { border-bottom: 1px solid #eee; margin-bottom: 20px; padding-bottom: 8px; }
    .table-controls .input-group { width: 280px; }
    .table-controls .btn { white-space: nowrap; }
    .sumber { text-align: right; font-size: 12px; color: #999; margin-top: 14px; }
    .dropdown-tahun { position: relative; flex-shrink: 0; }
    .dropdown-tahun-btn {
        display: flex; align-items: center; gap: 8px;
        border: 2px solid #ffbf00; border-radius: 6px; background: #fff;
        color: #b8860b; font-weight: 700; font-size: 14px;
        padding: 6px 12px; cursor: pointer; white-space: nowrap; user-select: none;
    }
    .dropdown-tahun-btn .arrow { font-size: 10px; transition: transform 0.2s; }
    .dropdown-tahun-btn.open .arrow { transform: rotate(180deg); }
    .dropdown-tahun-menu {
        display: none; position: absolute; top: calc(100% + 4px); right: 0;
        background: #fff; border: 2px solid #ffbf00; border-radius: 6px;
        min-width: 100%; z-index: 9999; overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .dropdown-tahun-menu.show { display: block; }
    .dropdown-tahun-menu a {
        display: block; padding: 8px 16px; color: #555;
        font-weight: 600; font-size: 14px; text-decoration: none;
        transition: background 0.15s;
    }
    .dropdown-tahun-menu a:hover { background: #fff8e1; color: #b8860b; }
    .dropdown-tahun-menu a.active { background: #ffbf00; color: #fff; }
    .page-btn {
        min-width: 34px; height: 34px; padding: 0 10px; border: 1px solid #ddd;
        border-radius: 6px; background: #fff; color: #555; font-weight: 600;
        font-size: 13px; cursor: pointer; transition: all .15s;
    }
    .page-btn:hover:not(:disabled) { border-color: #ffbf00; color: #b8860b; }
    .page-btn.active { background: #ffbf00; border-color: #ffbf00; color: #fff; }
    .page-btn:disabled { opacity: .45; cursor: not-allowed; }
    .map-container { margin-bottom: 0; }
    .map-tabs { display: flex; gap: 6px; margin-bottom: 12px; flex-wrap: wrap; }
    .map-tab-btn {
        padding: 5px 10px; border: 1px solid #ddd; border-radius: 4px;
        background: #fff; color: #555; font-weight: 600; font-size: 12px;
        cursor: pointer; transition: all 0.2s;
    }
    .map-tab-btn:hover { border-color: #ffbf00; color: #ffbf00; }
    .map-tab-btn.active { background: #ffbf00; border-color: #ffbf00; color: #fff; }
    #bencana-map { width: 100%; height: 520px; border-radius: 8px; }
    /* Hilangkan kotak outline saat polygon kecamatan diklik/fokus */
    #bencana-map .leaflet-interactive:focus,
    #bencana-map path.leaflet-interactive:focus { outline: none; }
    .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    /* Summary cards — samakan dengan modul geografis, iklim, kependudukan */
    .stat-summary-card {
        background: #f9f9f9; border: 1px solid #eee;
        border-radius: 8px; padding: 16px 24px;
        display: flex; align-items: center; gap: 16px;
    }
    .stat-summary-card .card-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; flex-shrink: 0;
    }
    .stat-summary-card .card-text .label { font-size: 12px; font-weight: 600; color: #888; letter-spacing: 1px; }
    .stat-summary-card .card-text .value { font-size: 28px; font-weight: 700; color: #333; line-height: 1.15; }
    .stat-summary-card .card-text .value small { font-size: 13px; font-weight: 500; color: #888; margin-left: 3px; }
    .map-legend { background: #fff; padding: 16px; border-radius: 8px; border: 1px solid #eee; max-width: 300px; }
    .map-legend-title { font-weight: 700; color: #333; margin-bottom: 12px; font-size: 13px; }
    .map-legend-item { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 12px; color: #555; }
    .map-legend-icon { width: 16px; height: 16px; border-radius: 50%; flex-shrink: 0; }
    /* Legend overlay peta (pojok kanan atas) */
    .map-legend-box { background: #fff; padding: 8px 11px; border-radius: 6px; box-shadow: 0 2px 10px rgba(0,0,0,.18); font-size: 11px; min-width: 175px; }
    .map-legend-box .legend-title { text-align: center; font-weight: 700; color: #333; margin-bottom: 6px; font-size: 11px; }
    .legend-row { display: flex; align-items: center; gap: 6px; margin-bottom: 4px; }
    .legend-row:last-child { margin-bottom: 0; }
    .legend-row .lr-icon { width: 15px; text-align: center; flex-shrink: 0; font-size: 12px; }
    .legend-row .lr-label { flex: 1; font-weight: 600; color: #333; white-space: nowrap; }
    .legend-row .lr-sep { color: #333; }
    .legend-row .lr-count { font-weight: 700; color: #333; min-width: 18px; text-align: right; }
    /* Marker damkar & zona aman pada peta */
    .damkar-marker { display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: #e53935; color: #fff; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,.3); font-size: 13px; }
    .zona-marker { display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: #2e7d32; color: #fff; border-radius: 6px; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,.3); font-size: 13px; }
    .pintu-air-marker { display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: #1e88e5; color: #fff; border-radius: 6px; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,.3); font-size: 13px; }
    .pompa-marker { display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: #5e35b1; color: #fff; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,.3); font-size: 13px; }
    .posko-marker { display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: #f9a825; color: #fff; border-radius: 6px; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,.3); font-size: 13px; }
    .pintu-air-marker.siaga, .pompa-marker.siaga, .posko-marker.siaga { background: #e53935; }
    /* Card popup peta lebih ringkas */
    #bencana-map .leaflet-popup-content-wrapper { border-radius: 8px; }
    #bencana-map .leaflet-popup-content { font-size: 11px; line-height: 1.45; margin: 8px 12px; min-width: 0; }
    #bencana-map .leaflet-popup-content b { font-size: 11.5px; }
    /* Badge merah berdenyut pada pin banjir yang berstatus siaga */
    .siaga-badge { position: absolute; top: -1px; right: -1px; width: 8px; height: 8px; background: #e53935; border: 1.5px solid #fff; border-radius: 50%; z-index: 2; }
    .siaga-badge::after { content: ''; position: absolute; inset: -2px; border-radius: 50%; border: 2px solid rgba(229,57,53,.6); animation: siagaPulse 1.4s ease-out infinite; }
    @keyframes siagaPulse { 0% { transform: scale(.7); opacity: .9; } 100% { transform: scale(2.2); opacity: 0; } }
    @media (max-width: 992px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) {
        .stat-grid { grid-template-columns: repeat(2, 1fr); }
        .statistik-wrapper  { flex-direction: column; padding: 20px 0; gap: 16px; }
        .statistik-sidebar  { width: 100%; }
        .statistik-sidebar .nav {
            flex-direction: row !important; flex-wrap: nowrap;
            overflow-x: auto; gap: 6px; padding-bottom: 4px; -webkit-overflow-scrolling: touch;
        }
        .statistik-sidebar .nav-link { white-space: nowrap; margin-bottom: 0; }
        .table-controls .input-group { width: 100%; }
        #bencana-map { height: 300px; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="statistik-wrapper">

        @include('statistik.partials.sidebar')

        <div class="statistik-content">
            <div class="stat-header-wrap">
                <div class="stat-header">MONITOR BENCANA JAKARTA BARAT</div>
            @if($availableTahun->isNotEmpty())
            <div class="dropdown-tahun">
                <div class="dropdown-tahun-btn" id="dropdownTahunBtn">
                    <i class="fa fa-calendar"></i> {{ $tahun }} <span class="arrow">&#9660;</span>
                </div>
                <div class="dropdown-tahun-menu" id="dropdownTahunMenu">
                    @foreach($availableTahun as $t)
                    <a href="{{ route('statistik.bencana', ['tahun' => $t]) }}" class="{{ $t == $tahun ? 'active' : '' }}">{{ $t }}</a>
                    @endforeach
                </div>
            </div>
            @endif
            </div>

            {{-- ── Summary Cards (menyesuaikan saat jenis di donut diklik) ─── --}}
            <div class="stat-grid">
                <div class="stat-summary-card">
                    <div class="card-text">
                        <div class="label">TOTAL KEJADIAN</div>
                        <div class="value" id="sc-kejadian">{{ number_format($ringkasan['total_kejadian']) }}</div>
                    </div>
                    <div class="card-icon" style="background:#2a78d6; margin-left:auto;">
                        <i class="fa fa-house-flood-water" style="color:#fff;"></i>
                    </div>
                </div>
                <div class="stat-summary-card">
                    <div class="card-text">
                        <div class="label">KORBAN MENINGGAL</div>
                        <div class="value" id="sc-meninggal">{{ number_format($ringkasan['total_meninggal']) }}</div>
                    </div>
                    <div class="card-icon" style="background:#e34948; margin-left:auto;">
                        <i class="fa fa-heart-crack" style="color:#fff;"></i>
                    </div>
                </div>
                <div class="stat-summary-card">
                    <div class="card-text">
                        <div class="label">KORBAN LUKA-LUKA</div>
                        <div class="value" id="sc-luka">{{ number_format($ringkasan['total_luka']) }}</div>
                    </div>
                    <div class="card-icon" style="background:#008300; margin-left:auto;">
                        <i class="fa fa-user-injured" style="color:#fff;"></i>
                    </div>
                </div>
                <div class="stat-summary-card">
                    <div class="card-text">
                        <div class="label" id="sc-jenis-label">JENIS TERBANYAK</div>
                        <div class="value" id="sc-jenis" style="font-size:20px;">{{ $ringkasan['jenis_terbanyak'] }}</div>
                    </div>
                    <div class="card-icon" style="background:#eb6834; margin-left:auto;">
                        <i class="fa fa-triangle-exclamation" style="color:#fff;"></i>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-lg-7 d-flex">
                    <div class="chart-card w-100 d-flex flex-column">
                        <div class="chart-title">Proporsi Jenis Bencana <span class="text-muted" style="font-weight:400;">· klik jenis untuk lihat ringkasannya</span></div>
                        <div id="chart-bencana" class="flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 440px;"></div>
                    </div>
                </div>
                <div class="col-lg-5 d-flex">
                    <div class="chart-card map-container w-100">
                        <div class="d-flex justify-content-between align-items-center mb-3" style="gap: 8px; flex-wrap: wrap;">
                            <div class="chart-title" style="margin-bottom: 0; flex: 1;">Peta Sebaran Bencana</div>
                            <div class="map-tabs" style="flex-wrap: wrap;">
                                <button class="map-tab-btn active" data-filter="banjir" style="font-size: 11px; padding: 6px 10px;">Pantau Banjir</button>
                                <button class="map-tab-btn" data-filter="pos-damkar" style="font-size: 11px; padding: 6px 10px;">Damkar</button>
                                <button class="map-tab-btn" data-filter="zona-aman" style="font-size: 11px; padding: 6px 10px;">Zona Aman</button>
                            </div>
                        </div>
                        <div id="bencana-map" style="min-height: 520px;"></div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-lg-7">
                    <div class="chart-card">
                        <div class="chart-title">Jenis Bencana per Triwulan <span class="text-muted" style="font-weight:400;">· Jakarta Barat {{ $tahun }}</span></div>
                        <div id="chart-triwulan" style="min-height: 360px;"></div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="chart-card">
                        <div class="chart-title">Tren Kejadian per Triwulan <span class="text-muted" style="font-weight:400;">· seluruh periode</span></div>
                        <div id="chart-tren" style="min-height: 360px;"></div>
                    </div>
                </div>
            </div>

    <div class="chart-card">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 table-header">
            <div>
                <div class="chart-title" style="margin-bottom: 4px;">Rekap Bencana per Triwulan</div>
                <div class="text-muted" style="font-size:13px;">Jakarta Barat &middot; {{ $tahun }} &middot; agregat triwulanan (bukan log kejadian per lokasi)</div>
            </div>
            <div class="d-flex flex-wrap gap-2 table-controls">
                <select id="bencana-jenis-filter" class="form-select form-select-sm" style="width:auto;">
                    <option value="all">Semua jenis</option>
                    @foreach($items->pluck('jenis_bencana')->unique()->sort()->values() as $j)
                    <option value="{{ $j }}">{{ $j }}</option>
                    @endforeach
                </select>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border"><i class="fa fa-search"></i></span>
                    <input type="text" id="bencana-search" class="form-control" placeholder="Cari periode atau jenis">
                </div>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="bencana-table">
                <thead>
                    <tr>
                        <th>Periode</th><th>Triwulan</th><th>Jenis Bencana</th>
                        <th>Kejadian</th><th>Korban Meninggal</th><th>Korban Luka</th>
                    </tr>
                </thead>
                <tbody id="bencana-tbody">
                    @forelse($items as $b)
                    <tr class="bencana-row" data-jenis="{{ $b->jenis_bencana }}" data-search="{{ strtolower($b->periode_label . ' ' . $b->jenis_bencana) }}">
                        <td>{{ $b->periode_label }}</td>
                        <td>{{ $b->triwulan ? 'TW' . $b->triwulan : '-' }}</td>
                        <td><span class="badge-jenis" style="background: {{ $warnaJenis[$b->jenis_bencana] ?? '#9e9e9e' }};">{{ $b->jenis_bencana }}</span></td>
                        <td>{{ number_format($b->jumlah_kejadian) }}</td>
                        <td>{{ number_format($b->jumlah_korban_meninggal) }}</td>
                        <td>{{ number_format($b->jumlah_korban_luka) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center; color:#999; padding:24px;">Belum ada data rekap untuk tahun ini. Jalankan "Sync dari API" di portal admin.</td></tr>
                    @endforelse
                    <tr id="bencana-empty-search" style="display:none;"><td colspan="6" style="text-align:center; color:#999; padding:24px;">Tidak ada data yang cocok dengan pencarian.</td></tr>
                </tbody>
            </table>
        </div>
        @if($items->isNotEmpty())
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-3">
            <div class="text-muted" id="bencana-page-info" style="font-size:13px;"></div>
            <div class="d-flex gap-1" id="bencana-pagination"></div>
        </div>
        @endif
    </div>

    <div class="sumber">Sumber: {{ $items->first()->sumber ?? 'Satu Data Jakarta' }} &middot; titik peta: BPBD &amp; DSDA DKI Jakarta</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('statistik.partials.warna-kecamatan')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
    (function () {
        var btn = document.getElementById('dropdownTahunBtn');
        var menu = document.getElementById('dropdownTahunMenu');
        if (btn) {
            btn.addEventListener('click', function (e) { e.stopPropagation(); btn.classList.toggle('open'); menu.classList.toggle('show'); });
            document.addEventListener('click', function () { btn.classList.remove('open'); menu.classList.remove('show'); });
        }

        // Initialize Leaflet Map
        var map = L.map('bencana-map').setView([-6.1751, 106.7272], 12);

        // Pilihan basemap: Satelit (default), Peta Terang, Peta Jalan
        var satelit = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles © Esri', maxZoom: 19
        }).addTo(map);
        var petaTerang = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '© OpenStreetMap, © CARTO', subdomains: 'abcd', maxZoom: 19
        });
        var petaJalan = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors', maxZoom: 19
        });
        L.control.layers(
            { 'Satelit': satelit, 'Peta Terang': petaTerang, 'Peta Jalan': petaJalan },
            {},
            { position: 'bottomleft' }
        ).addTo(map);

        // Legend overlay (pojok kanan atas) — isinya berubah sesuai tab aktif
        var legendControl = L.control({ position: 'topright' });
        legendControl.onAdd = function () {
            var div = L.DomUtil.create('div', 'map-legend-box');
            L.DomEvent.disableClickPropagation(div);
            this._div = div;
            return div;
        };
        legendControl.addTo(map);

        // ── Overlay batas wilayah kecamatan (mark keseluruhan) ──
        // Pane khusus dengan z-index rendah agar polygon tidak menutupi titik (circle marker banjir)
        map.createPane('kecamatanPane');
        map.getPane('kecamatanPane').style.zIndex = 350;

        var kecJakbar = {!! json_encode($kecamatanNames->map(fn($n) => strtoupper($n))->values()) !!};
        fetch('{{ asset("assets/geojson/kecamatan.geojson") }}')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                data.features = data.features.filter(function(f) {
                    return kecJakbar.includes((f.properties.name || '').toUpperCase());
                });
                var geoLayer = L.geoJSON(data, {
                    pane: 'kecamatanPane',
                    style: function(feature) {
                        var w = window.warnaKecamatan(feature.properties.name || '');
                        return { color: w, weight: 2, fillColor: w, fillOpacity: 0.15 };
                    },
                    onEachFeature: function(feature, layer) {
                        layer.bindTooltip(feature.properties.name || '', { sticky: true, direction: 'top' });
                        layer.on('mouseover', function() { layer.setStyle({ fillOpacity: 0.28 }); });
                        layer.on('mouseout', function() { layer.setStyle({ fillOpacity: 0.15 }); });
                    }
                }).addTo(map);
                if (geoLayer.getBounds().isValid()) {
                    map.fitBounds(geoLayer.getBounds(), { padding: [20, 20] });
                }
            });

        // Data titik dari database (banjir, pos damkar, zona aman) + live DSDA (pantau air, gabung ke banjir)
        var markerData = Object.assign(
            { 'banjir-p1': [], 'banjir-p2': [], 'banjir-p3': [], 'banjir-air': [], 'pos-damkar': [], 'zona-aman': [] },
            {!! json_encode($titikBencana) !!},
            {!! json_encode($tmaTitik) !!}
        );

        var markers = {};
        var banjirColors = {
            'banjir-p1': '#ff6b6b',
            'banjir-p2': '#ffa500',
            'banjir-p3': '#ffeb3b'
        };

        // Icon kustom Damkar & Zona Aman (Font Awesome di dalam divIcon)
        var damkarIcon = L.divIcon({
            className: '', html: '<div class="damkar-marker"><i class="fa fa-fire-extinguisher"></i></div>',
            iconSize: [28, 28], iconAnchor: [14, 14], popupAnchor: [0, -14]
        });
        var zonaIcon = L.divIcon({
            className: '', html: '<div class="zona-marker"><i class="fa fa-shield-halved"></i></div>',
            iconSize: [28, 28], iconAnchor: [14, 14], popupAnchor: [0, -14]
        });
        // Ikon titik pantau air: pintu air / rumah pompa / posko (merah bila status siaga)
        var tmaStyle = {
            'pintu-air':   { cls: 'pintu-air-marker', fa: 'fa-water',       label: 'Pintu Air' },
            'rumah-pompa': { cls: 'pompa-marker',     fa: 'fa-fan',         label: 'Rumah Pompa' },
            'posko':       { cls: 'posko-marker',     fa: 'fa-tower-observation', label: 'Posko SDA' }
        };
        function tmaIcon(kind, siaga) {
            var s = tmaStyle[kind] || tmaStyle['pintu-air'];
            return L.divIcon({
                className: '', html: '<div class="' + s.cls + (siaga ? ' siaga' : '') + '"><i class="fa ' + s.fa + '"></i></div>',
                iconSize: [28, 28], iconAnchor: [14, 14], popupAnchor: [0, -14]
            });
        }
        // Pin banjir berwarna sesuai prioritas (selaras dengan legend). Badge merah bila siaga.
        function banjirIcon(color, siaga) {
            var badge = siaga ? '<span class="siaga-badge"></span>' : '';
            return L.divIcon({
                className: '',
                html: '<div style="position:relative;width:19px;height:19px;">' + badge
                    + '<i class="fa fa-location-dot" style="color:' + color + ';font-size:19px;text-shadow:0 1px 2px rgba(0,0,0,.45);"></i></div>',
                iconSize: [19, 19], iconAnchor: [9, 19], popupAnchor: [0, -17]
            });
        }

        // Create markers
        Object.keys(markerData).forEach(function(type) {
            markers[type] = [];
            markerData[type].forEach(function(point) {
                var marker;
                var ket = point.ket ? '<br><span style="color:#777;">' + point.ket + '</span>' : '';
                // Tombol ke Google Maps: pakai link manual bila ada, jika tidak pakai koordinat
                var mapsUrl = point.link
                    ? point.link
                    : 'https://www.google.com/maps/search/?api=1&query=' + point.lat + ',' + point.lng;
                var navBtn = '<br><a href="' + mapsUrl + '" target="_blank" rel="noopener" '
                    + 'style="display:inline-flex;align-items:center;gap:6px;margin-top:8px;padding:6px 12px;'
                    + 'background:#1a73e8;color:#fff;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;">'
                    + '<i class="fa fa-location-dot"></i> Buka Maps</a>';
                if (type === 'pos-damkar') {
                    marker = L.marker([point.lat, point.lng], { icon: damkarIcon })
                        .bindPopup('<b><i class="fa fa-fire-extinguisher"></i> ' + point.name + '</b>' + ket + navBtn);
                } else if (type === 'zona-aman') {
                    // Bentuk mark area aman + titik kumpul
                    var area = L.circle([point.lat, point.lng], {
                        radius: 350, color: '#2e7d32', fillColor: '#2e7d32', weight: 2, fillOpacity: 0.12
                    });
                    marker = L.marker([point.lat, point.lng], { icon: zonaIcon })
                        .bindPopup('<b><i class="fa fa-shield-halved"></i> ' + point.name + '</b>' + (ket || '<br>Area aman evakuasi') + navBtn);
                    marker._areaLayer = area;
                } else if (type === 'banjir-air') {
                    var isSiaga = /siaga/i.test(point.status || '');
                    var statusColor = isSiaga ? '#e53935' : '#2e7d32';
                    var st = tmaStyle[point.kind] || tmaStyle['pintu-air'];
                    var tinggi = (point.tinggi !== null && point.tinggi !== undefined) ? '<br>Tinggi air: <b>' + point.tinggi + ' cm</b>' : '';
                    var waktu = point.tanggal ? '<br><span style="color:#999;font-size:11px;">Update: ' + point.tanggal.replace('T', ' ').slice(0, 16) + '</span>' : '';
                    marker = L.marker([point.lat, point.lng], { icon: tmaIcon(point.kind, isSiaga) })
                        .bindPopup('<b><i class="fa ' + st.fa + '"></i> ' + point.name + '</b><br><span style="color:#777;">' + st.label + '</span>'
                            + '<br>Status: <b style="color:' + statusColor + ';">' + (point.status || '-').replace('Status : ', '') + '</b>'
                            + tinggi + waktu
                            + '<br><span style="color:#bbb;font-size:10px;">Sumber: DSDA DKI Jakarta (real-time)</span>');
                } else {
                    var labelP = type === 'banjir-p1' ? 'Prioritas 1' : (type === 'banjir-p2' ? 'Prioritas 2' : 'Prioritas 3');
                    var isSiaga = /siaga/i.test(point.status || '');
                    var statusLine = '';
                    if (point.status) {
                        statusLine = '<br>Status: <b style="color:' + (isSiaga ? '#e53935' : '#2e7d32') + ';">'
                            + point.status.replace('Status : ', '') + '</b>'
                            + (point.tinggi !== null && point.tinggi !== undefined ? ' · TMA ' + point.tinggi + ' cm' : '')
                            + (point.dari ? '<br><span style="color:#999;font-size:11px;">Acuan pos terdekat: ' + point.dari + ' (' + point.jarak + ' km)</span>' : '');
                    }
                    marker = L.marker([point.lat, point.lng], { icon: banjirIcon(banjirColors[type], isSiaga) })
                        .bindPopup('<b>' + point.name + '</b><br>Rawan Banjir ' + labelP + ket + statusLine);
                }
                marker.addTo(map);
                if (marker._areaLayer) marker._areaLayer.addTo(map);
                markers[type].push(marker);
            });
        });

        // ── Legend overlay: isi sesuai tab aktif + jumlah titik per kategori ──
        function legendRow(iconHtml, label, count) {
            return '<div class="legend-row">'
                + '<span class="lr-icon">' + iconHtml + '</span>'
                + '<span class="lr-label">' + label + '</span>'
                + '<span class="lr-sep">:</span>'
                + '<span class="lr-count">' + count + '</span></div>';
        }
        function cnt(type) { return (markerData[type] || []).length; }
        function pin(color) { return '<i class="fa fa-location-dot" style="color:' + color + ';"></i>'; }
        var damkarMini = '<span class="damkar-marker" style="width:15px;height:15px;font-size:8px;"><i class="fa fa-fire-extinguisher"></i></span>';
        var zonaMini   = '<span class="zona-marker" style="width:15px;height:15px;font-size:8px;border-radius:4px;"><i class="fa fa-shield-halved"></i></span>';
        var pintuAirMini = '<span class="pintu-air-marker" style="width:15px;height:15px;font-size:8px;border-radius:4px;"><i class="fa fa-water"></i></span>';
        var pompaMini    = '<span class="pompa-marker" style="width:15px;height:15px;font-size:8px;"><i class="fa fa-fan"></i></span>';

        var poskoMini = '<span class="posko-marker" style="width:15px;height:15px;font-size:8px;border-radius:4px;"><i class="fa fa-tower-observation"></i></span>';
        function cntKind(kind) { return (markerData['banjir-air'] || []).filter(function (p) { return p.kind === kind; }).length; }

        function updateLegend(filter) {
            var html = '<div class="legend-title">Keterangan :</div>';
            if (filter === 'banjir') {
                html += legendRow(pin('#ff6b6b'), 'Lokasi Banjir Prioritas 1', cnt('banjir-p1'));
                html += legendRow(pin('#ffa500'), 'Lokasi Banjir Prioritas 2', cnt('banjir-p2'));
                html += legendRow(pin('#ffeb3b'), 'Lokasi Banjir Prioritas 3', cnt('banjir-p3'));
                if (cntKind('pintu-air'))   html += legendRow(pintuAirMini, 'Pintu Air', cntKind('pintu-air'));
                if (cntKind('rumah-pompa')) html += legendRow(pompaMini, 'Rumah Pompa', cntKind('rumah-pompa'));
                if (cntKind('posko'))       html += legendRow(poskoMini, 'Posko SDA', cntKind('posko'));
                html += '<div style="font-size:10px;color:#999;margin-top:4px;">🔴 badge/titik merah = status siaga · real-time DSDA</div>';
            } else if (filter === 'pos-damkar') {
                html += legendRow(damkarMini, 'Pos Damkar', cnt('pos-damkar'));
            } else if (filter === 'zona-aman') {
                html += legendRow(zonaMini, 'Zona Aman / Evakuasi', cnt('zona-aman'));
            }
            if (legendControl._div) legendControl._div.innerHTML = html;
        }

        function setMarkerVisible(marker, show) {
            if (show) { map.addLayer(marker); if (marker._areaLayer) map.addLayer(marker._areaLayer); }
            else { map.removeLayer(marker); if (marker._areaLayer) map.removeLayer(marker._areaLayer); }
        }

        function applyFilter(filter) {
            Object.keys(markers).forEach(function(type) {
                markers[type].forEach(function(marker) {
                    setMarkerVisible(marker, filter === 'all' || type.startsWith(filter));
                });
            });
            updateLegend(filter);
        }

        // Filter functionality
        document.querySelectorAll('.map-tab-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.map-tab-btn').forEach(function(b){ b.classList.remove('active'); });
                this.classList.add('active');
                applyFilter(this.getAttribute('data-filter'));
            });
        });

        // Filter awal sesuai tab aktif (default: Banjir)
        var activeBtn = document.querySelector('.map-tab-btn.active');
        applyFilter(activeBtn ? activeBtn.getAttribute('data-filter') : 'banjir');
    })();

    // ── Tabel: pencarian + pagination (client-side) ──
    (function () {
        var perPage = 10;
        var allRows = Array.prototype.slice.call(document.querySelectorAll('#bencana-tbody .bencana-row'));
        if (!allRows.length) return;

        var searchEl = document.getElementById('bencana-search');
        var jenisEl  = document.getElementById('bencana-jenis-filter');
        var infoEl   = document.getElementById('bencana-page-info');
        var pagEl    = document.getElementById('bencana-pagination');
        var emptyEl  = document.getElementById('bencana-empty-search');
        var currentPage = 1;
        var filtered = allRows;

        function applyRowFilters() {
            var q = (searchEl && searchEl.value.trim().toLowerCase()) || '';
            var jenis = (jenisEl && jenisEl.value) || 'all';
            filtered = allRows.filter(function (r) {
                var okJenis = jenis === 'all' || r.getAttribute('data-jenis') === jenis;
                var okSearch = !q || (r.getAttribute('data-search') || '').indexOf(q) !== -1;
                return okJenis && okSearch;
            });
            currentPage = 1;
            render();
        }

        function render() {
            var totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
            if (currentPage > totalPages) currentPage = totalPages;

            allRows.forEach(function (r) { r.style.display = 'none'; });
            var start = (currentPage - 1) * perPage;
            var pageRows = filtered.slice(start, start + perPage);
            pageRows.forEach(function (r) { r.style.display = ''; });

            emptyEl.style.display = filtered.length ? 'none' : '';

            if (filtered.length) {
                infoEl.textContent = 'Menampilkan ' + (start + 1) + '–' + (start + pageRows.length) + ' dari ' + filtered.length + ' laporan';
            } else {
                infoEl.textContent = '';
            }

            // Bangun tombol halaman
            pagEl.innerHTML = '';
            if (filtered.length) {
                pagEl.appendChild(pageButton('‹', currentPage - 1, currentPage === 1));
                for (var p = 1; p <= totalPages; p++) {
                    pagEl.appendChild(pageButton(p, p, false, p === currentPage));
                }
                pagEl.appendChild(pageButton('›', currentPage + 1, currentPage === totalPages));
            }
        }

        function pageButton(label, page, disabled, active) {
            var b = document.createElement('button');
            b.className = 'page-btn' + (active ? ' active' : '');
            b.textContent = label;
            if (disabled) b.disabled = true;
            b.addEventListener('click', function () { currentPage = page; render(); });
            return b;
        }

        if (searchEl) searchEl.addEventListener('input', applyRowFilters);
        if (jenisEl)  jenisEl.addEventListener('change', applyRowFilters);

        render();
    })();
</script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    var warnaJenis   = {!! json_encode($warnaJenis) !!};
    var jenisLabels  = {!! json_encode($perJenis->keys()) !!};
    var jenisSeries  = {!! json_encode($perJenis->values()->map(fn($v) => (int)$v)) !!};

    var rawItems = {!! json_encode($items->map(fn($b) => [
        'periode' => $b->periode_label,
        'triwulan' => (int) $b->triwulan,
        'jenis' => $b->jenis_bencana,
        'jumlah' => (int) $b->jumlah_kejadian,
        'meninggal' => (int) $b->jumlah_korban_meninggal,
        'luka' => (int) $b->jumlah_korban_luka,
    ])->values()) !!};

    // Data chart dari controller
    var triwulanLabels = {!! json_encode($perTriwulan['labels']) !!};
    var triwulanSeries = {!! json_encode($perTriwulan['series']) !!};
    var trenLabels     = {!! json_encode($tren['labels']) !!};
    var trenData       = {!! json_encode($tren['data']) !!};

    // Total per jenis bencana (untuk menyesuaikan summary card saat slice donut diklik)
    var totalsByJenis = {};
    rawItems.forEach(function (it) {
        var t = totalsByJenis[it.jenis] || { kejadian: 0, meninggal: 0, luka: 0 };
        t.kejadian  += it.jumlah || 0;
        t.meninggal += it.meninggal || 0;
        t.luka      += it.luka || 0;
        totalsByJenis[it.jenis] = t;
    });
    // Total keseluruhan (untuk reset)
    var grandTotals = { kejadian: 0, meninggal: 0, luka: 0 };
    Object.keys(totalsByJenis).forEach(function (j) {
        grandTotals.kejadian  += totalsByJenis[j].kejadian;
        grandTotals.meninggal += totalsByJenis[j].meninggal;
        grandTotals.luka      += totalsByJenis[j].luka;
    });

    function fmt(n) { return Number(n).toLocaleString('en-US'); }
    function setSummary(jenis) {
        var t = jenis ? totalsByJenis[jenis] : grandTotals;
        document.getElementById('sc-kejadian').textContent  = fmt(t.kejadian);
        document.getElementById('sc-meninggal').textContent = fmt(t.meninggal);
        document.getElementById('sc-luka').textContent      = fmt(t.luka);
        var jenisLbl = document.getElementById('sc-jenis-label');
        var jenisVal = document.getElementById('sc-jenis');
        if (jenis) {
            jenisLbl.textContent = 'Jenis Dipilih';
            jenisVal.textContent = jenis;
        } else {
            jenisLbl.textContent = 'Jenis Terbanyak';
            jenisVal.textContent = @json($ringkasan['jenis_terbanyak']);
        }
    }

    var selectedJenis = null;

    if (jenisSeries.length) {
        var donutChart = new ApexCharts(document.querySelector("#chart-bencana"), {
            chart: {
                type: 'donut', height: 420,
                events: {
                    dataPointSelection: function (event, ctx, cfg) {
                        // Toggle: klik jenis → tampilkan ringkasannya; klik lagi → reset
                        var jenis = jenisLabels[cfg.dataPointIndex];
                        selectedJenis = (selectedJenis === jenis) ? null : jenis;
                        setSummary(selectedJenis);
                    }
                }
            },
            labels: jenisLabels,
            series: jenisSeries,
            colors: jenisLabels.map(function (j) { return warnaJenis[j] || '#9e9e9e'; }),
            legend: {
                position: 'bottom', horizontalAlign: 'center',
                onItemClick: { toggleDataSeries: false }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function (w) { return w.globals.seriesTotals.reduce(function(a, b){ return a + b; }, 0); }
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: true, formatter: function (val) { return Math.round(val) + '%'; } },
            tooltip: { y: { formatter: function (v) { return v + ' kejadian'; } } }
        });
        donutChart.render();

        // Klik legend (titik kecil di bawah donut) → ringkasan jenis tsb
        document.querySelector('#chart-bencana').addEventListener('click', function (e) {
            var legendItem = e.target.closest('.apexcharts-legend-series');
            if (!legendItem) return;
            var jenis = legendItem.getAttribute('seriesname');
            // ApexCharts mengganti spasi pada seriesname dengan 'x' entity; cocokkan via teks
            var text = (legendItem.textContent || '').trim();
            var match = jenisLabels.find(function (j) { return j === text; });
            if (!match) return;
            selectedJenis = (selectedJenis === match) ? null : match;
            setSummary(selectedJenis);
        });
    } else {
        document.querySelector("#chart-bencana").innerHTML =
            '<p style="text-align:center;color:#999;padding:40px 0;">Belum ada data.</p>';
    }

    // Tren kejadian seluruh periode (lintas tahun)
    new ApexCharts(document.querySelector("#chart-tren"), {
        chart: { type: 'area', height: 360, toolbar: { show: false } },
        colors: ['#2a78d6'],
        stroke: { curve: 'smooth', width: 3 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.72, opacityTo: 0.18, stops: [0, 90, 100] } },
        series: [{ name: 'Kejadian', data: trenData }],
        xaxis: { categories: trenLabels, labels: { rotate: -45, style: { fontSize: '10px' } } },
        yaxis: { labels: { formatter: function(val) { return val.toFixed(0); } } },
        markers: { size: 4 },
        tooltip: { y: { formatter: function (v) { return v + ' kejadian'; } } }
    }).render();

    // Bar: jenis bencana per triwulan (tahun terpilih)
    if (triwulanSeries.length) {
        new ApexCharts(document.querySelector("#chart-triwulan"), {
            chart: { type: 'bar', height: 360, toolbar: { show: false } },
            plotOptions: { bar: { horizontal: false, columnWidth: '58%', borderRadius: 3 } },
            series: triwulanSeries,
            xaxis: { categories: triwulanLabels },
            yaxis: { labels: { formatter: function(val) { return val.toFixed(0); } } },
            dataLabels: { enabled: false },
            legend: { position: 'bottom' },
            colors: triwulanSeries.map(function (s) { return warnaJenis[s.name] || '#9e9e9e'; }),
            tooltip: { y: { formatter: function (v) { return v + ' kejadian'; } } }
        }).render();
    } else {
        document.querySelector("#chart-triwulan").innerHTML =
            '<p style="text-align:center;color:#999;padding:40px 0;">Belum ada data.</p>';
    }

</script>
@endpush
