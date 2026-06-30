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
    #bencana-map { width: 100%; height: 480px; border-radius: 8px; }
    .stat-card { background: #fff; border: 1px solid #edf1f7; border-radius: 16px; padding: 20px; box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04); display: flex; align-items: center; justify-content: space-between; gap: 16px; min-height: 120px; }
    .stat-card-icon { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
    .stat-card-content { flex: 1; min-width: 0; }
    .stat-card-label { font-size: 12px; font-weight: 700; color: #8c8c8c; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
    .stat-card-value { font-size: 26px; font-weight: 700; color: #2b2b2b; line-height: 1.1; }
    .stat-card-icon.orange { background: #fff4e5; color: #ff9800; }
    .stat-card-icon.red { background: #ffebee; color: #f44336; }
    .stat-card-icon.blue { background: #e3f2fd; color: #2196f3; }
    .stat-card-icon.gold { background: #fffde7; color: #fbc02d; }
    .map-legend { background: #fff; padding: 16px; border-radius: 8px; border: 1px solid #eee; max-width: 300px; }
    .map-legend-title { font-weight: 700; color: #333; margin-bottom: 12px; font-size: 13px; }
    .map-legend-item { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 12px; color: #555; }
    .map-legend-icon { width: 16px; height: 16px; border-radius: 50%; flex-shrink: 0; }
    @media (max-width: 768px) {
        .statistik-wrapper { flex-direction: column; }
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

            <div class="row g-3 mb-4">
                <div class="col-lg-7">
                    <div class="chart-card">
                        <div class="chart-title">Proporsi Jenis Bencana</div>
                        <div id="chart-bencana" style="min-height: 520px;"></div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="chart-card map-container">
                        <div class="d-flex justify-content-between align-items-center mb-3" style="gap: 8px; flex-wrap: wrap;">
                            <div class="chart-title" style="margin-bottom: 0; flex: 1;">Peta Sebaran Bencana</div>
                            <div class="map-tabs" style="flex-wrap: wrap;">
                                <button class="map-tab-btn active" data-filter="all" style="font-size: 11px; padding: 6px 10px;">Semua</button>
                                <button class="map-tab-btn" data-filter="banjir" style="font-size: 11px; padding: 6px 10px;">Banjir</button>
                                <button class="map-tab-btn" data-filter="pos-damkar" style="font-size: 11px; padding: 6px 10px;">Pos</button>
                                <button class="map-tab-btn" data-filter="zona-aman" style="font-size: 11px; padding: 6px 10px;">Zona</button>
                            </div>
                        </div>
                        <div id="bencana-map" style="min-height: 480px;"></div>
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
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Define marker data
        var markerData = {
            'banjir-p1': [
                { lat: -6.1751, lng: 106.7272, name: 'Lokasi Banjir Prioritas 1 - A' },
                { lat: -6.1850, lng: 106.7350, name: 'Lokasi Banjir Prioritas 1 - B' },
                { lat: -6.1650, lng: 106.7150, name: 'Lokasi Banjir Prioritas 1 - C' }
            ],
            'banjir-p2': [
                { lat: -6.1900, lng: 106.7400, name: 'Lokasi Banjir Prioritas 2 - A' },
                { lat: -6.1600, lng: 106.7050, name: 'Lokasi Banjir Prioritas 2 - B' }
            ],
            'banjir-p3': [
                { lat: -6.1700, lng: 106.7500, name: 'Lokasi Banjir Prioritas 3 - A' },
                { lat: -6.1800, lng: 106.7200, name: 'Lokasi Banjir Prioritas 3 - B' },
                { lat: -6.1550, lng: 106.7300, name: 'Lokasi Banjir Prioritas 3 - C' }
            ],
            'pos-damkar': [
                { lat: -6.1950, lng: 106.7100, name: 'Pos Damkar - A' },
                { lat: -6.1500, lng: 106.7450, name: 'Pos Damkar - B' }
            ],
            'zona-aman': [
                { lat: -6.1750, lng: 106.7600, name: 'Zona Aman - A' }
            ]
        };

        var markers = {};
        var colors = {
            'banjir-p1': '#ff6b6b',
            'banjir-p2': '#ffa500',
            'banjir-p3': '#ffeb3b',
            'pos-damkar': '#4caf50',
            'zona-aman': '#2196f3'
        };

        // Create markers
        Object.keys(markerData).forEach(function(type) {
            markers[type] = [];
            markerData[type].forEach(function(point) {
                var marker = L.circleMarker([point.lat, point.lng], {
                    radius: 8,
                    fillColor: colors[type],
                    color: '#fff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8
                }).bindPopup(point.name).addTo(map);
                markers[type].push(marker);
            });
        });

        // Filter functionality
        document.querySelectorAll('.map-tab-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.map-tab-btn').forEach(function(b){ b.classList.remove('active'); });
                this.classList.add('active');
                var currentFilter = this.getAttribute('data-filter');
                
                Object.keys(markers).forEach(function(type) {
                    markers[type].forEach(function(marker) {
                        if (currentFilter === 'all' || type.startsWith(currentFilter)) {
                            map.addLayer(marker);
                        } else {
                            map.removeLayer(marker);
                        }
                    });
                });
            });
        });
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
        'jumlah' => (int) $b->jumlah_kejadian,
    ])->values()) !!};

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

    if (jenisSeries.length) {
        new ApexCharts(document.querySelector("#chart-bencana"), {
            chart: { type: 'donut', height: 420 },
            labels: jenisLabels,
            series: jenisSeries,
            colors: jenisLabels.map(function (j) { return warnaJenis[j] || '#9e9e9e'; }),
            legend: { position: 'bottom', horizontalAlign: 'center' },
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
        }).render();
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
