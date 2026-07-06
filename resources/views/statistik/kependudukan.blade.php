@extends('landing-page.layout.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
    .stat-header {
        background: #ffbf00; color: white; text-align: center;
        padding: 14px; border-radius: 8px; font-weight: 700;
        font-size: 18px; margin-bottom: 24px; letter-spacing: 1px;
    }
    .stat-summary-card {
        background: #f9f9f9; border: 1px solid #eee;
        border-radius: 8px; padding: 16px 24px;
    }
    .stat-summary-card .value { font-size: 28px; font-weight: 700; color: #333; }
    .stat-summary-card .label { font-size: 12px; font-weight: 600; color: #888; letter-spacing: 1px; }
    .chart-card { background: #fff; border: 1px solid #eee; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
    .chart-card .chart-title { font-size: 13px; font-weight: 600; color: #555; letter-spacing: 1px; margin-bottom: 16px; }
    .sumber { text-align: right; font-size: 12px; color: #999; margin-top: 16px; }
    #map-kelurahan { height: 450px; width: 100%; border-radius: 8px; z-index: 1; }
    .stat-header-wrap {
        display: flex; align-items: center; gap: 12px; margin-bottom: 24px;
    }
    .stat-header { flex: 1; margin-bottom: 0; }
    .dropdown-tahun { position: relative; flex-shrink: 0; }
    .dropdown-tahun-btn {
        display: flex; align-items: center; gap: 8px;
        border: 2px solid #ffbf00; border-radius: 6px; background: #fff;
        color: #b8860b; font-weight: 700; font-size: 14px;
        padding: 6px 12px; cursor: pointer; white-space: nowrap; user-select: none;
    }
    .dropdown-tahun-btn .arrow {
        font-size: 10px; transition: transform 0.2s;
    }
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
    .stat-summary-card { display: flex; align-items: center; gap: 16px; }
    .stat-summary-card .card-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; flex-shrink: 0;
    }
    .stat-summary-card .card-text .value { font-size: 28px; font-weight: 700; color: #333; }
    .stat-summary-card .card-text .label { font-size: 12px; font-weight: 600; color: #888; letter-spacing: 1px; }

    /* Animasi nilai card saat kecamatan dipilih (halus, fade + naik) — samakan dgn Geografis/Iklim */
    @keyframes cardValueIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .stat-summary-card .card-anim { animation: cardValueIn .35s ease both; }

    /* ── Responsive (tablet & HP) ──────────────────────────────── */
    @media (max-width: 768px) {
        .statistik-wrapper  { flex-direction: column; padding: 20px 0; gap: 16px; }
        .statistik-sidebar  { width: 100%; }
        .statistik-sidebar .nav {
            flex-direction: row !important; flex-wrap: nowrap;
            overflow-x: auto; gap: 6px; padding-bottom: 4px; -webkit-overflow-scrolling: touch;
        }
        .statistik-sidebar .nav-link { white-space: nowrap; margin-bottom: 0; }
        .stat-header        { font-size: 15px; padding: 12px; }
        #map-kelurahan      { height: 360px; }
        .stat-summary-card  { margin-bottom: 12px; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="statistik-wrapper">

        @include('statistik.partials.sidebar')

        {{-- KONTEN --}}
        <div class="statistik-content">
            <div class="stat-header-wrap">
                <div class="stat-header">KEPENDUDUKAN JAKARTA BARAT {{ $tahun }}</div>
                <div class="dropdown-tahun">
                    <div class="dropdown-tahun-btn" id="dropdownTahunBtn">
                        <i class="fa fa-calendar"></i>
                        {{ $tahun }}
                        <span class="arrow">&#9660;</span>
                    </div>
                    <div class="dropdown-tahun-menu" id="dropdownTahunMenu">
                        @foreach($availableTahun as $t)
                        <a href="{{ route('statistik.kependudukan', ['tahun' => $t]) }}"
                           class="{{ $t === $tahun ? 'active' : '' }}">
                            {{ $t }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Summary (dinamis: ikut berubah saat klik chart kecamatan) --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stat-summary-card">
                        <div class="card-text">
                            <div class="label" id="sum-label-1">LAKI-LAKI</div>
                            <div class="value" id="sum-value-1">{{ number_format($summary->jumlah_laki_laki) }}</div>
                        </div>
                        <div class="card-icon" id="sum-icon-1" style="background:#2a78d6; margin-left:auto;">
                            <i class="fa fa-male" id="sum-i-1" style="color:#fff;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-summary-card">
                        <div class="card-text">
                            <div class="label" id="sum-label-2">PEREMPUAN</div>
                            <div class="value" id="sum-value-2">{{ number_format($summary->jumlah_perempuan) }}</div>
                        </div>
                        <div class="card-icon" id="sum-icon-2" style="background:#e87ba4; margin-left:auto;">
                            <i class="fa fa-female" id="sum-i-2" style="color:#fff;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-summary-card">
                        <div class="card-text">
                            <div class="label" id="sum-label-3">TOTAL PENDUDUK</div>
                            <div class="value" id="sum-value-3">{{ number_format($summary->jumlah_total) }}</div>
                        </div>
                        <div class="card-icon" id="sum-icon-3" style="background:#008300; margin-left:auto;">
                            <i class="fa fa-users" id="sum-i-3" style="color:#fff;"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="chart-title mb-0">POPULASI PENDUDUK KELURAHAN</div>
                            <button id="btn-back" onclick="backToKecamatan()"
                                style="display:none; font-size:11px; padding:4px 10px; border:1px solid #ccc; border-radius:4px; background:#f9f9f9; cursor:pointer;">
                                ← Kembali
                            </button>
                        </div>
                        <div id="chart-kelurahan"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-title">POPULASI PENDUDUK KECAMATAN</div>
                        <div id="chart-kecamatan"></div>
                    </div>
                </div>
            </div>

            {{-- Map --}}
            <div class="chart-card">
                <div class="chart-title">PERSEBARAN PENDUDUK KECAMATAN & KELURAHAN</div>
                <div id="map-kelurahan"></div>
            </div>

            <div class="sumber">Sumber: {{ $summary->sumber }}</div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
@include('statistik.partials.warna-kecamatan')
<script>
    (function () {
        var btn  = document.getElementById('dropdownTahunBtn');
        var menu = document.getElementById('dropdownTahunMenu');
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            btn.classList.toggle('open');
            menu.classList.toggle('show');
        });
        document.addEventListener('click', function () {
            btn.classList.remove('open');
            menu.classList.remove('show');
        });
    })();
</script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Data dari Laravel
    var namaKecamatan = {!! json_encode($perKecamatan->pluck('kecamatan.nama_kecamatan')) !!};
    var popKecamatan  = {!! json_encode($perKecamatan->pluck('jumlah_penduduk')->map(fn($v) => (int)$v)) !!};
    var kelurahanPerKecamatan = {!! json_encode($kelurahanPerKecamatan) !!};

    // ── Skala warna choropleth (base biru) berdasarkan jumlah penduduk ──
    // Konsisten antara chart, peta & marker — meniru gaya modul Geografis.
    // ── Warna per kecamatan: dari sumber tunggal window.warnaKecamatan (konsisten antar modul) ──
    function lerpColor(a, b, t) {
        var ah = parseInt(a.slice(1), 16), bh = parseInt(b.slice(1), 16);
        var ar = ah >> 16, ag = (ah >> 8) & 0xff, ab = ah & 0xff;
        var br = bh >> 16, bg = (bh >> 8) & 0xff, bb = bh & 0xff;
        var rr = Math.round(ar + (br - ar) * t);
        var rg = Math.round(ag + (bg - ag) * t);
        var rb = Math.round(ab + (bb - ab) * t);
        return '#' + ((1 << 24) + (rr << 16) + (rg << 8) + rb).toString(16).slice(1);
    }

    var popMin = Math.min.apply(null, popKecamatan);
    var popMax = Math.max.apply(null, popKecamatan);

    // Warna sinkron map & chart (satu warna khas per kecamatan; key = NAMA UPPERCASE)
    var warnaMap = {};
    namaKecamatan.forEach(function(nama, i) {
        warnaMap[nama.toUpperCase()] = window.warnaKecamatan(nama);
    });

    /* ── WARNA LAMA (gradasi biru monokrom berdasarkan jumlah penduduk) — disimpan untuk referensi ──
    var BLUE_LIGHT = '#E2ECFA';   // penduduk paling sedikit → biru sangat muda
    var BLUE_DARK  = '#5B82C0';   // penduduk paling banyak  → biru slate cerah
    var WARNA_STEPS = 5;          // jumlah tingkatan warna (choropleth bertingkat)
    function gradByPop(v) {
        if (v == null) return '#e0e0e0';
        var t = popMax > popMin ? (v - popMin) / (popMax - popMin) : 0.5;
        var step = Math.round(t * (WARNA_STEPS - 1)) / (WARNA_STEPS - 1);
        return lerpColor(BLUE_LIGHT, BLUE_DARK, step);
    }
    */

    var warnaChart = namaKecamatan.map(function(nama) {
        return warnaMap[nama.toUpperCase()] || '#ccc';
    });

    var pendudukMap = {};
    namaKecamatan.forEach(function(nama, i) {
        pendudukMap[nama.toUpperCase()] = popKecamatan[i];
    });

    // ── Summary card dinamis ──────────────────────────────────────────────
    var totalJakbar = popKecamatan.reduce(function(a, b) { return a + b; }, 0);

    // Kondisi default (Jakarta Barat keseluruhan) — dari data_kependudukan
    var SUMMARY_DEFAULT = [
        { bg: '#2a78d6', icon: 'fa-male',   label: 'LAKI-LAKI',      value: {{ (int) $summary->jumlah_laki_laki }} },
        { bg: '#e87ba4', icon: 'fa-female', label: 'PEREMPUAN',      value: {{ (int) $summary->jumlah_perempuan }} },
        { bg: '#008300', icon: 'fa-users',  label: 'TOTAL PENDUDUK', value: {{ (int) $summary->jumlah_total }} },
    ];

    function setCard(i, bg, icon, label, valueText) {
        document.getElementById('sum-icon-' + i).style.background = bg;
        document.getElementById('sum-i-' + i).className = 'fa ' + icon;
        document.getElementById('sum-label-' + i).textContent = label;
        document.getElementById('sum-value-' + i).textContent = valueText;
    }

    // Retrigger animasi fade+naik pada semua card (meniru Geografis & Iklim)
    function animateCards() {
        document.querySelectorAll('.stat-summary-card .card-text').forEach(function(el) {
            el.classList.remove('card-anim');
            void el.offsetWidth;   // paksa reflow agar animasi terulang
            el.classList.add('card-anim');
        });
    }

    function resetSummary() {
        SUMMARY_DEFAULT.forEach(function(c, idx) {
            setCard(idx + 1, c.bg, c.icon, c.label, c.value.toLocaleString('id-ID'));
        });
        animateCards();
    }

    // Per kecamatan hanya tersedia total (bukan L/P), jadi tampilkan metrik yang relevan
    function showKecamatanSummary(namaKec) {
        var key    = namaKec.toUpperCase();
        var total  = pendudukMap[key] || 0;
        var warna  = warnaMap[key] || '#26a0fc';
        var k      = kelurahanPerKecamatan[namaKec];
        var jmlKel = (k && k.labels) ? k.labels.length : 0;
        var persen = totalJakbar ? (total / totalJakbar * 100) : 0;

        setCard(1, warna,     'fa-users',      'PENDUDUK KECAMATAN', total.toLocaleString('id-ID'));
        setCard(2, '#6c5ce7', 'fa-building',   'JUMLAH KELURAHAN',   jmlKel.toString());
        setCard(3, '#e17055', 'fa-chart-pie',  'KONTRIBUSI JAKBAR',  persen.toFixed(1) + '%');
        animateCards();
    }

    // Chart Kelurahan (drill-down)
    var chartKelurahan = new ApexCharts(document.querySelector("#chart-kelurahan"), {
        chart: { type: 'bar', height: 320, toolbar: { show: false } },
        series: [{ name: 'Penduduk', data: [] }],
        xaxis: { categories: [], labels: { style: { fontSize: '10px' } } },
        colors: ['#26a0fc'],
        dataLabels: { enabled: true, style: { fontSize: '9px' } },
        plotOptions: { bar: { borderRadius: 3, horizontal: true } },
        noData: { text: 'Klik kecamatan di sebelah kanan →', style: { fontSize: '13px', color: '#999' } }
    });
    chartKelurahan.render();

    // Tampilan awal: kelurahan terpadat dari setiap kecamatan
    function showTopKelurahan() {
        var labels = [];
        var data   = [];
        var colors = [];
        namaKecamatan.forEach(function(nama) {
            var k = kelurahanPerKecamatan[nama];
            if (!k || !k.data || !k.data.length) return;
            // data sudah terurut desc, tapi cari max untuk amannya
            var maxIdx = 0;
            for (var i = 1; i < k.data.length; i++) {
                if (k.data[i] > k.data[maxIdx]) maxIdx = i;
            }
            labels.push(k.labels[maxIdx]);
            data.push(k.data[maxIdx]);
            colors.push(warnaMap[nama.toUpperCase()] || '#26a0fc');
        });

        chartKelurahan.updateOptions({
            series: [{ name: 'Penduduk', data: data }],
            xaxis: { categories: labels },
            colors: colors,
            plotOptions: { bar: { borderRadius: 3, horizontal: true, distributed: true } },
            legend: { show: false },
            title: {
                text: 'Kelurahan Terpadat per Kecamatan',
                align: 'left',
                style: { fontSize: '12px', fontWeight: 600 }
            }
        });
    }
    showTopKelurahan();

    // Chart Kecamatan
    var chartKecamatan = new ApexCharts(document.querySelector("#chart-kecamatan"), {
        chart: {
            type: 'bar',
            height: 320,
            toolbar: { show: false },
            events: {
                dataPointSelection: function(event, chartContext, config) {
                    var idx = config.dataPointIndex;
                    var namaKec = namaKecamatan[idx];
                    drillDown(namaKec);
                }
            }
        },
        series: [{ name: 'Penduduk', data: popKecamatan }],
        xaxis: { categories: namaKecamatan, labels: { style: { fontSize: '10px' } } },
        colors: warnaChart,
        dataLabels: { enabled: true, style: { fontSize: '9px' } },
        plotOptions: { bar: { borderRadius: 3, distributed: true } },
        legend: { show: false },
        title: { text: '👆 Klik bar untuk lihat kelurahan', align: 'center', style: { fontSize: '11px', color: '#999' } }
    });
    chartKecamatan.render();

    // Drill-down
    function drillDown(namaKec) {
        var data = kelurahanPerKecamatan[namaKec];
        if (!data) return;

        var warna = warnaMap[namaKec.toUpperCase()] || '#26a0fc';

        chartKelurahan.updateOptions({
            series: [{ name: 'Penduduk', data: data.data }],
            xaxis: { categories: data.labels },
            colors: [warna],
            plotOptions: { bar: { borderRadius: 3, horizontal: true, distributed: false } },
            legend: { show: false },
            title: {
                text: 'Kelurahan - ' + namaKec,
                align: 'left',
                style: { fontSize: '12px', fontWeight: 600 }
            }
        });

        chartKecamatan.updateOptions({
            title: { text: '← Sedang melihat: ' + namaKec, align: 'center', style: { fontSize: '11px', color: '#999' } }
        });

        filterMarkerMap(namaKec);
        showKecamatanSummary(namaKec);
        flyToKecamatan(namaKec);
        document.getElementById('btn-back').style.display = 'inline-block';
    }

    // Terbang & zoom ke polygon kecamatan (meniru animasi map di Geografis)
    function flyToKecamatan(namaKec) {
        var layer = kecLayers[namaKec.toUpperCase()];
        if (!layer || typeof map === 'undefined') return;
        var bounds = layer.getBounds();
        var fitZoom = map.getBoundsZoom(bounds, false, L.point(30, 30));
        var targetZoom = Math.max(13, Math.min(14, fitZoom));
        map.flyTo(bounds.getCenter(), targetZoom);
        layer.bringToFront();
        layer.openPopup();
    }

    // Kembali
    function backToKecamatan() {
        showTopKelurahan();
        chartKecamatan.updateOptions({
            title: { text: '👆 Klik bar untuk lihat kelurahan', align: 'center', style: { fontSize: '11px', color: '#999' } }
        });
        resetMarkerMap();
        resetSummary();
        if (typeof map !== 'undefined') { map.closePopup(); map.flyTo([-6.15, 106.75], 12); }
        document.getElementById('btn-back').style.display = 'none';
    }

    // MAP
    var map = L.map('map-kelurahan').setView([-6.15, 106.75], 12);

    // Layer satelit (Esri World Imagery) — DEFAULT
    var satelit = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles © Esri — Source: Esri, Maxar, Earthstar Geographics',
        maxZoom: 19
    }).addTo(map);

    // Overlay label (nama jalan/tempat) — otomatis aktif agar nama kebaca di atas satelit
    var label = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19, pane: 'overlayPane'
    }).addTo(map);

    // Tema abu (CARTO Positron) — alternatif
    var abu = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap, © CARTO', subdomains: 'abcd', maxZoom: 19
    });

    // Layer peta biasa (OSM) — alternatif
    var jalan = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    });

    L.control.layers(
        { 'Satelit': satelit, 'Tema Abu': abu, 'Peta Jalan': jalan },
        { 'Label Nama': label },
        { position: 'topright' }
    ).addTo(map);

    var kecJakbar = Object.keys(warnaMap);
    var kecLayers = {};   // referensi layer polygon per kecamatan (untuk flyTo)

    // GeoJSON Polygon Kecamatan
    fetch('{{ asset("assets/geojson/kecamatan.geojson") }}')
        .then(res => res.json())
        .then(data => {
            data.features = data.features.filter(f => kecJakbar.includes(f.properties.name));
            L.geoJSON(data, {
                style: function(feature) {
                    return {
                        color: '#fff', weight: 1.5,
                        fillColor: warnaMap[feature.properties.name] || '#ccc',
                        fillOpacity: 0.62,   // samakan dengan modul Geografis
                    };
                },
                onEachFeature: function(feature, layer) {
                    var nama = feature.properties.name;
                    kecLayers[nama.toUpperCase()] = layer;
                    var jumlah = pendudukMap[nama] ? pendudukMap[nama].toLocaleString('id-ID') + ' jiwa' : '-';
                    layer.bindPopup('<b>Kec. ' + nama + '</b><br>👥 ' + jumlah);
                    layer.on('mouseover', function() { layer.setStyle({ fillOpacity: 0.8 }); layer.openPopup(); });
                    layer.on('mouseout',  function() { layer.setStyle({ fillOpacity: 0.62 }); layer.closePopup(); });
                }
            }).addTo(map);

            // Legend
            var legend = L.control({ position: 'bottomright' });
            legend.onAdd = function() {
                var div = L.DomUtil.create('div');
                div.style.cssText = 'background:white;padding:10px;border-radius:8px;font-size:12px;line-height:20px;box-shadow:0 1px 5px rgba(0,0,0,0.2);';
                div.innerHTML = '<b>Kecamatan</b><br>';
                kecJakbar.forEach(function(nama) {
                    var jumlah = pendudukMap[nama] ? pendudukMap[nama].toLocaleString('id-ID') : '-';
                    div.innerHTML += '<span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:' + warnaMap[nama] + ';margin-right:6px;vertical-align:middle;"></span>' + nama + ' <b>' + jumlah + '</b><br>';
                });
                return div;
            };
            legend.addTo(map);
        });

    // Marker Kelurahan
    var kelurahanData = {!! json_encode($perKelurahan->map(fn($k) => [
        'nama'      => $k->nama_kelurahan,
        'lat'       => (float) $k->latitude,
        'lng'       => (float) $k->longitude,
        'jumlah'    => $k->jumlah_penduduk,
        'kecamatan' => $k->kecamatan->nama_kecamatan,
    ])) !!};

    var allMarkers = [];

    kelurahanData.forEach(function(k) {
        if (!k.lat || !k.lng) return;
        var warna = warnaMap[k.kecamatan.toUpperCase()] || '#ccc';
        var icon = L.divIcon({
            className: '',
            html: '<div style="background:' + warna + ';opacity:0.8;width:10px;height:10px;border-radius:50%;border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,0.4);"></div>',
            iconSize: [10, 10],
            iconAnchor: [5, 5],
        });
        var marker = L.marker([k.lat, k.lng], { icon: icon })
            .addTo(map)
            .bindPopup('<b>' + k.nama + '</b><br>Kecamatan: ' + k.kecamatan + '<br>👥 ' + k.jumlah.toLocaleString('id-ID') + ' jiwa');
        marker._kecamatan = k.kecamatan;
        allMarkers.push(marker);
    });

    function filterMarkerMap(namaKec) {
        allMarkers.forEach(function(m) {
            m.setOpacity(m._kecamatan.toUpperCase() === namaKec.toUpperCase() ? 1 : 0.1);
        });
    }

    function resetMarkerMap() {
        allMarkers.forEach(function(m) { m.setOpacity(1); });
    }
</script>
@endpush