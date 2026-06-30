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
    .map-legend-box { background: #fff; padding: 12px 16px; border-radius: 8px; box-shadow: 0 2px 12px rgba(0,0,0,.2); font-size: 13px; min-width: 240px; }
    .map-legend-box .legend-title { text-align: center; font-weight: 700; color: #333; margin-bottom: 10px; }
    .legend-row { display: flex; align-items: center; gap: 8px; margin-bottom: 7px; }
    .legend-row:last-child { margin-bottom: 0; }
    .legend-row .lr-icon { width: 20px; text-align: center; flex-shrink: 0; font-size: 15px; }
    .legend-row .lr-label { flex: 1; font-weight: 600; color: #333; white-space: nowrap; }
    .legend-row .lr-sep { color: #333; }
    .legend-row .lr-count { font-weight: 700; color: #333; min-width: 22px; text-align: right; }
    /* Marker damkar & zona aman pada peta */
    .damkar-marker { display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: #e53935; color: #fff; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,.3); font-size: 13px; }
    .zona-marker { display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: #2e7d32; color: #fff; border-radius: 6px; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,.3); font-size: 13px; }
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

        {{-- SIDEBAR --}}
        <div class="statistik-sidebar">
            <nav class="nav flex-column">
                <a class="nav-link" href="{{ route('statistik.geografis') }}"><i class="fa fa-map"></i> Geografis</a>
                <a class="nav-link" href="{{ route('statistik.iklim') }}"><i class="fa fa-cloud"></i> Iklim</a>
                <a class="nav-link" href="{{ route('statistik.kependudukan') }}"><i class="fa fa-users"></i> Kependudukan</a>
                <a class="nav-link" href="{{ route('statistik.pendidikan') }}"><i class="fa fa-graduation-cap"></i> Pendidikan</a>
                <a class="nav-link" href="{{ route('statistik.kesehatan') }}"><i class="fa fa-plus-circle"></i> Kesehatan</a>
                <a class="nav-link active" href="{{ route('statistik.bencana') }}"><i class="fa fa-house-flood-water"></i> Monitor Bencana</a>
            </nav>
        </div>

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
                    <div class="card-icon" style="background:#ffbf00; margin-left:auto;">
                        <i class="fa fa-house-flood-water" style="color:#fff;"></i>
                    </div>
                </div>
                <div class="stat-summary-card">
                    <div class="card-text">
                        <div class="label">TOTAL KORBAN</div>
                        <div class="value" id="sc-korban">{{ number_format($ringkasan['total_korban']) }}</div>
                    </div>
                    <div class="card-icon" style="background:#ffbf00; margin-left:auto;">
                        <i class="fa fa-user-injured" style="color:#fff;"></i>
                    </div>
                </div>
                <div class="stat-summary-card">
                    <div class="card-text">
                        <div class="label">TOTAL TERDAMPAK</div>
                        <div class="value" id="sc-terdampak">{{ number_format($ringkasan['total_terdampak']) }}</div>
                    </div>
                    <div class="card-icon" style="background:#ffbf00; margin-left:auto;">
                        <i class="fa fa-users" style="color:#fff;"></i>
                    </div>
                </div>
                <div class="stat-summary-card">
                    <div class="card-text">
                        <div class="label" id="sc-jenis-label">JENIS TERBANYAK</div>
                        <div class="value" id="sc-jenis" style="font-size:20px;">{{ $ringkasan['jenis_terbanyak'] }}</div>
                    </div>
                    <div class="card-icon" style="background:#ffbf00; margin-left:auto;">
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
                                <button class="map-tab-btn active" data-filter="banjir" style="font-size: 11px; padding: 6px 10px;">Banjir</button>
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
                        <div class="chart-title">Statistik per Kecamatan</div>
                        <div id="chart-kecamatan" style="min-height: 360px;"></div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="chart-card">
                        <div class="chart-title">Tren Kejadian Bulanan</div>
                        <div id="chart-bulanan" style="min-height: 360px;"></div>
                    </div>
                </div>
            </div>

    <div class="chart-card">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 table-header">
            <div>
                <div class="chart-title" style="margin-bottom: 4px;">Rincian Laporan Bencana</div>
                <div class="text-muted" style="font-size:13px;">Update terakhir: 10 Menit yang lalu</div>
            </div>
            <div class="d-flex flex-wrap gap-2 table-controls">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border"><i class="fa fa-search"></i></span>
                    <input type="text" class="form-control" placeholder="Cari berdasarkan lokasi atau jenis">
                </div>
                <button class="btn btn-outline-secondary btn-sm"><i class="fa fa-sort"></i> Sort</button>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="bencana-table">
                <thead>
                    <tr>
                        <th>Tanggal</th><th>Jenis Bencana</th><th>Lokasi</th><th>Kecamatan</th>
                        <th>Kejadian</th><th>Korban</th><th>Terdampak</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $b)
                    <tr>
                        <td>{{ $b->tanggal_kejadian ? \Carbon\Carbon::parse($b->tanggal_kejadian)->translatedFormat('d M Y') : '-' }}</td>
                        <td><span class="badge-jenis" style="background: {{ $warnaJenis[$b->jenis_bencana] ?? '#9e9e9e' }};">{{ $b->jenis_bencana }}</span></td>
                        <td>{{ $b->nama_lokasi }}</td>
                        <td>{{ $b->kecamatan->nama_kecamatan ?? '-' }}</td>
                        <td>{{ number_format($b->jumlah_kejadian) }}</td>
                        <td>{{ number_format($b->jumlah_korban) }}</td>
                        <td>{{ number_format($b->jumlah_terdampak) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center; color:#999; padding:24px;">Belum ada data bencana untuk tahun ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="sumber">Sumber: {{ $items->first()->sumber ?? 'BPBD DKI Jakarta' }}</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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

        // Pilihan basemap: Peta Terang (default), Satelit, Peta Jalan
        var petaTerang = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '© OpenStreetMap, © CARTO', subdomains: 'abcd', maxZoom: 19
        }).addTo(map);
        var satelit = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles © Esri', maxZoom: 19
        });
        var petaJalan = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors', maxZoom: 19
        });
        L.control.layers(
            { 'Peta Terang': petaTerang, 'Satelit': satelit, 'Peta Jalan': petaJalan },
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
                    style: function() {
                        return { color: '#42a5f5', weight: 1.5, fillColor: '#90caf9', fillOpacity: 0.18, dashArray: '4' };
                    },
                    onEachFeature: function(feature, layer) {
                        layer.bindTooltip(feature.properties.name || '', { sticky: true, direction: 'top' });
                        layer.on('mouseover', function() { layer.setStyle({ fillOpacity: 0.30 }); });
                        layer.on('mouseout', function() { layer.setStyle({ fillOpacity: 0.18 }); });
                    }
                }).addTo(map);
                if (geoLayer.getBounds().isValid()) {
                    map.fitBounds(geoLayer.getBounds(), { padding: [20, 20] });
                }
            });

        // Data titik dari database (zona rawan banjir, pos damkar, zona aman)
        var markerData = Object.assign(
            { 'banjir-p1': [], 'banjir-p2': [], 'banjir-p3': [], 'pos-damkar': [], 'zona-aman': [] },
            {!! json_encode($titikBencana) !!}
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
        // Pin banjir berwarna sesuai prioritas (selaras dengan legend)
        function banjirIcon(color) {
            return L.divIcon({
                className: '',
                html: '<i class="fa fa-location-dot" style="color:' + color + ';font-size:28px;text-shadow:0 1px 2px rgba(0,0,0,.45);"></i>',
                iconSize: [28, 28], iconAnchor: [14, 28], popupAnchor: [0, -26]
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
                } else {
                    var labelP = type === 'banjir-p1' ? 'Prioritas 1' : (type === 'banjir-p2' ? 'Prioritas 2' : 'Prioritas 3');
                    marker = L.marker([point.lat, point.lng], { icon: banjirIcon(banjirColors[type]) })
                        .bindPopup('<b>' + point.name + '</b><br>Rawan Banjir ' + labelP + ket);
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
        var damkarMini = '<span class="damkar-marker" style="width:18px;height:18px;font-size:9px;"><i class="fa fa-fire-extinguisher"></i></span>';
        var zonaMini   = '<span class="zona-marker" style="width:18px;height:18px;font-size:9px;border-radius:4px;"><i class="fa fa-shield-halved"></i></span>';

        function updateLegend(filter) {
            var html = '<div class="legend-title">Keterangan :</div>';
            if (filter === 'banjir') {
                html += legendRow(pin('#ff6b6b'), 'Lokasi Banjir Prioritas 1', cnt('banjir-p1'));
                html += legendRow(pin('#ffa500'), 'Lokasi Banjir Prioritas 2', cnt('banjir-p2'));
                html += legendRow(pin('#ffeb3b'), 'Lokasi Banjir Prioritas 3', cnt('banjir-p3'));
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
</script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    var warnaJenis   = {!! json_encode($warnaJenis) !!};
    var jenisLabels  = {!! json_encode($perJenis->keys()) !!};
    var jenisSeries  = {!! json_encode($perJenis->values()->map(fn($v) => (int)$v)) !!};

    var rawItems = {!! json_encode($items->map(fn($b) => [
        'tanggal' => $b->tanggal_kejadian,
        'kecamatan' => $b->kecamatan->nama_kecamatan ?? '-',
        'jenis' => $b->jenis_bencana,
        'jumlah' => (int) $b->jumlah_kejadian,
        'korban' => (int) $b->jumlah_korban,
        'terdampak' => (int) $b->jumlah_terdampak,
    ])->values()) !!};

    // Total per jenis bencana (untuk menyesuaikan summary card saat slice donut diklik)
    var totalsByJenis = {};
    rawItems.forEach(function (it) {
        var t = totalsByJenis[it.jenis] || { kejadian: 0, korban: 0, terdampak: 0 };
        t.kejadian  += it.jumlah || 0;
        t.korban    += it.korban || 0;
        t.terdampak += it.terdampak || 0;
        totalsByJenis[it.jenis] = t;
    });
    // Total keseluruhan (untuk reset)
    var grandTotals = { kejadian: 0, korban: 0, terdampak: 0 };
    Object.keys(totalsByJenis).forEach(function (j) {
        grandTotals.kejadian  += totalsByJenis[j].kejadian;
        grandTotals.korban    += totalsByJenis[j].korban;
        grandTotals.terdampak += totalsByJenis[j].terdampak;
    });

    function fmt(n) { return Number(n).toLocaleString('en-US'); }
    function setSummary(jenis) {
        var t = jenis ? totalsByJenis[jenis] : grandTotals;
        document.getElementById('sc-kejadian').textContent  = fmt(t.kejadian);
        document.getElementById('sc-korban').textContent    = fmt(t.korban);
        document.getElementById('sc-terdampak').textContent = fmt(t.terdampak);
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

    var monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    var monthData = Array(12).fill(0);
    rawItems.forEach(function(item) {
        if (!item.tanggal) return;
        var date = new Date(item.tanggal);
        if (isNaN(date)) return;
        monthData[date.getMonth()] += item.jumlah;
    });

    var kecamatanCounts = {};
    rawItems.forEach(function(item) {
        if (!item.kecamatan) return;
        kecamatanCounts[item.kecamatan] = (kecamatanCounts[item.kecamatan] || 0) + item.jumlah;
    });
    var kecamatanEntries = Object.entries(kecamatanCounts)
        .sort(function(a, b){ return b[1] - a[1]; })
        .slice(0, 6);
    var kecamatanLabels = kecamatanEntries.map(function(item){ return item[0]; });
    var kecamatanSeries = kecamatanEntries.map(function(item){ return item[1]; });

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

    new ApexCharts(document.querySelector("#chart-bulanan"), {
        chart: { type: 'area', height: 360, toolbar: { show: false } },
        stroke: { curve: 'smooth', width: 3 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.72, opacityTo: 0.18, stops: [0, 90, 100] } },
        series: [{ name: 'Kejadian', data: monthData }],
        xaxis: { categories: monthNames },
        yaxis: { labels: { formatter: function(val) { return val.toFixed(0); } } },
        markers: { size: 4 },
        tooltip: { y: { formatter: function (v) { return v + ' kejadian'; } } }
    }).render();

    new ApexCharts(document.querySelector("#chart-kecamatan"), {
        chart: { type: 'bar', height: 360, toolbar: { show: false } },
        plotOptions: { bar: { horizontal: true, barHeight: '50%' } },
        series: [{ name: 'Kejadian', data: kecamatanSeries }],
        xaxis: { labels: { formatter: function(val) { return val.toFixed(0); } } },
        yaxis: { categories: kecamatanLabels },
        dataLabels: { enabled: true },
        colors: ['#ffbf00']
    }).render();

</script>
@endpush
