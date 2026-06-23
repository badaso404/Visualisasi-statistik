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
    .chart-card { background: #fff; border: 1px solid #eee; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
    .chart-card .chart-title { font-size: 13px; font-weight: 600; color: #555; letter-spacing: 1px; margin-bottom: 16px; }
    .sumber { text-align: right; font-size: 12px; color: #999; margin-top: 16px; }
    #map-bencana { height: 450px; width: 100%; border-radius: 8px; z-index: 1; }
    .stat-header-wrap { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
    .stat-header { flex: 1; margin-bottom: 0; }
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
        font-weight: 600; font-size: 14px; text-decoration: none; transition: background 0.15s;
    }
    .dropdown-tahun-menu a:hover { background: #fff8e1; color: #b8860b; }
    .dropdown-tahun-menu a.active { background: #ffbf00; color: #fff; }
    .stat-summary-card { background: #f9f9f9; border: 1px solid #eee; border-radius: 8px; padding: 16px 20px; display: flex; align-items: center; gap: 16px; }
    .stat-summary-card .card-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
    .stat-summary-card .card-text .value { font-size: 26px; font-weight: 700; color: #333; }
    .stat-summary-card .card-text .label { font-size: 12px; font-weight: 600; color: #888; letter-spacing: 1px; }
    .bencana-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .bencana-table th, .bencana-table td { padding: 10px 12px; border-bottom: 1px solid #eee; text-align: left; }
    .bencana-table th { background: #fafafa; color: #666; font-weight: 600; font-size: 12px; letter-spacing: .5px; }
    .badge-jenis { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; color: #fff; white-space: nowrap; }
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

        {{-- KONTEN --}}
        <div class="statistik-content">
            <div class="stat-header-wrap">
                <div class="stat-header">MONITOR BENCANA JAKARTA BARAT {{ $tahun }}</div>
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

            {{-- Summary --}}
            <div class="row mb-4 g-3">
                <div class="col-md-3 col-6">
                    <div class="stat-summary-card">
                        <div class="card-icon" style="background:#fff3e0;"><i class="fa fa-triangle-exclamation" style="color:#fb8c00;"></i></div>
                        <div class="card-text"><div class="label">TOTAL KEJADIAN</div><div class="value">{{ number_format($ringkasan['total_kejadian']) }}</div></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-summary-card">
                        <div class="card-icon" style="background:#ffebee;"><i class="fa fa-heart-crack" style="color:#e53935;"></i></div>
                        <div class="card-text"><div class="label">KORBAN JIWA</div><div class="value">{{ number_format($ringkasan['total_korban']) }}</div></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-summary-card">
                        <div class="card-icon" style="background:#e3f2fd;"><i class="fa fa-house-circle-exclamation" style="color:#1e88e5;"></i></div>
                        <div class="card-text"><div class="label">TERDAMPAK</div><div class="value">{{ number_format($ringkasan['total_terdampak']) }}</div></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-summary-card">
                        <div class="card-icon" style="background:#fff8e1;"><i class="fa fa-fire" style="color:#ffbf00;"></i></div>
                        <div class="card-text"><div class="label">PALING SERING</div><div class="value" style="font-size:18px;">{{ $ringkasan['jenis_terbanyak'] }}</div></div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Pie chart --}}
                <div class="col-md-5">
                    <div class="chart-card">
                        <div class="chart-title">PERBANDINGAN JENIS BENCANA</div>
                        <div id="chart-bencana"></div>
                    </div>
                </div>
                {{-- Map --}}
                <div class="col-md-7">
                    <div class="chart-card">
                        <div class="chart-title">PETA SEBARAN LOKASI BENCANA</div>
                        <div id="map-bencana"></div>
                    </div>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="chart-card">
                <div class="chart-title">DAFTAR DAERAH TERDAMPAK</div>
                <div style="overflow-x:auto;">
                    <table class="bencana-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th><th>Jenis</th><th>Lokasi</th><th>Kecamatan</th>
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
<script>
    (function () {
        var btn = document.getElementById('dropdownTahunBtn');
        var menu = document.getElementById('dropdownTahunMenu');
        if (btn) {
            btn.addEventListener('click', function (e) { e.stopPropagation(); btn.classList.toggle('open'); menu.classList.toggle('show'); });
            document.addEventListener('click', function () { btn.classList.remove('open'); menu.classList.remove('show'); });
        }
    })();
</script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var warnaJenis   = {!! json_encode($warnaJenis) !!};
    var jenisLabels  = {!! json_encode($perJenis->keys()) !!};
    var jenisSeries  = {!! json_encode($perJenis->values()->map(fn($v) => (int)$v)) !!};
    var lokasiBencana = {!! json_encode($items->map(fn($b) => [
        'lat'        => $b->latitude,
        'lng'        => $b->longitude,
        'jenis'      => $b->jenis_bencana,
        'lokasi'     => $b->nama_lokasi,
        'kecamatan'  => $b->kecamatan->nama_kecamatan ?? '-',
        'kejadian'   => (int) $b->jumlah_kejadian,
        'terdampak'  => (int) $b->jumlah_terdampak,
    ])->values()) !!};

    // PIE CHART
    if (jenisSeries.length) {
        new ApexCharts(document.querySelector("#chart-bencana"), {
            chart: { type: 'pie', height: 360 },
            labels: jenisLabels,
            series: jenisSeries,
            colors: jenisLabels.map(function (j) { return warnaJenis[j] || '#9e9e9e'; }),
            legend: { position: 'bottom' },
            dataLabels: { enabled: true, formatter: function (val) { return Math.round(val) + '%'; } },
            tooltip: { y: { formatter: function (v) { return v + ' kejadian'; } } }
        }).render();
    } else {
        document.querySelector("#chart-bencana").innerHTML =
            '<p style="text-align:center;color:#999;padding:40px 0;">Belum ada data.</p>';
    }

    // MAP
    var map = L.map('map-bencana').setView([-6.16, 106.77], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors', maxZoom: 19
    }).addTo(map);

    lokasiBencana.forEach(function (b) {
        if (b.lat === null || b.lng === null) return;
        var warna = warnaJenis[b.jenis] || '#9e9e9e';
        L.circleMarker([b.lat, b.lng], {
            radius: Math.min(8 + b.kejadian, 24),
            color: '#fff', weight: 2, fillColor: warna, fillOpacity: 0.85
        }).addTo(map).bindPopup(
            '<b>' + b.jenis + '</b><br>' +
            '📍 ' + b.lokasi + ' (Kec. ' + b.kecamatan + ')<br>' +
            '🔴 ' + b.kejadian + ' kejadian<br>' +
            '🏠 ' + b.terdampak.toLocaleString('id-ID') + ' terdampak'
        );
    });
</script>
@endpush
