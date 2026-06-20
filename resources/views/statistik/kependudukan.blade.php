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
                <a class="nav-link active" href="{{ route('statistik.kependudukan') }}"><i class="fa fa-users"></i> Kependudukan</a>
                <a class="nav-link" href="{{ route('statistik.pendidikan') }}"><i class="fa fa-graduation-cap"></i> Pendidikan</a>
                <a class="nav-link" href="{{ route('statistik.kesehatan') }}"><i class="fa fa-plus-circle"></i> Kesehatan</a>
            </nav>
        </div>

        {{-- KONTEN --}}
        <div class="statistik-content">
            <div class="stat-header">KEPENDUDUKAN JAKARTA BARAT 2024</div>

            {{-- Summary --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stat-summary-card">
                        <div class="label">LAKI-LAKI</div>
                        <div class="value">{{ number_format($summary->jumlah_laki_laki) }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-summary-card">
                        <div class="label">PEREMPUAN</div>
                        <div class="value">{{ number_format($summary->jumlah_perempuan) }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-summary-card">
                        <div class="label">TOTAL PENDUDUK</div>
                        <div class="value">{{ number_format($summary->jumlah_total) }}</div>
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
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Data dari Laravel
    var namaKecamatan = {!! json_encode($perKecamatan->pluck('kecamatan.nama_kecamatan')) !!};
    var popKecamatan  = {!! json_encode($perKecamatan->pluck('jumlah_penduduk')->map(fn($v) => (int)$v)) !!};
    var kelurahanPerKecamatan = {!! json_encode($kelurahanPerKecamatan) !!};

    // Warna sinkron map & chart
    var warnaMap = {
        'CENGKARENG'        : '#2196f3',
        'KALIDERES'         : '#e91e8c',
        'KEBON JERUK'       : '#ff9800',
        'KEMBANGAN'         : '#4caf50',
        'TAMBORA'           : '#8bc34a',
        'GROGOL PETAMBURAN' : '#9c27b0',
        'PALMERAH'          : '#f44336',
        'TAMAN SARI'        : '#00bcd4',
    };

    var warnaChart = namaKecamatan.map(function(nama) {
        return warnaMap[nama.toUpperCase()] || '#ccc';
    });

    var pendudukMap = {};
    namaKecamatan.forEach(function(nama, i) {
        pendudukMap[nama.toUpperCase()] = popKecamatan[i];
    });

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
        document.getElementById('btn-back').style.display = 'inline-block';
    }

    // Kembali
    function backToKecamatan() {
        chartKelurahan.updateOptions({
            series: [{ name: 'Penduduk', data: [] }],
            xaxis: { categories: [] },
            title: { text: '' }
        });
        chartKecamatan.updateOptions({
            title: { text: '👆 Klik bar untuk lihat kelurahan', align: 'center', style: { fontSize: '11px', color: '#999' } }
        });
        resetMarkerMap();
        document.getElementById('btn-back').style.display = 'none';
    }

    // MAP
    var map = L.map('map-kelurahan').setView([-6.15, 106.75], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var kecJakbar = Object.keys(warnaMap);

    // GeoJSON Polygon Kecamatan
    fetch('{{ asset("assets/geojson/kecamatan.geojson") }}')
        .then(res => res.json())
        .then(data => {
            data.features = data.features.filter(f => kecJakbar.includes(f.properties.name));
            L.geoJSON(data, {
                style: function(feature) {
                    return {
                        color: '#fff', weight: 2,
                        fillColor: warnaMap[feature.properties.name] || '#ccc',
                        fillOpacity: 0.5,
                    };
                },
                onEachFeature: function(feature, layer) {
                    var nama = feature.properties.name;
                    var jumlah = pendudukMap[nama] ? pendudukMap[nama].toLocaleString('id-ID') + ' jiwa' : '-';
                    layer.bindPopup('<b>Kec. ' + nama + '</b><br>👥 ' + jumlah);
                    layer.on('mouseover', function() { layer.setStyle({ fillOpacity: 0.8 }); layer.openPopup(); });
                    layer.on('mouseout',  function() { layer.setStyle({ fillOpacity: 0.5 }); layer.closePopup(); });
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
            html: '<div style="background:' + warna + ';width:10px;height:10px;border-radius:50%;border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,0.4);"></div>',
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