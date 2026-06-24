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

    /* Summary cards */
    .geo-summary-grid {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 22px;
    }
    .geo-card {
        background: #fff; border: 1px solid #eee; border-radius: 10px;
        padding: 14px 16px; position: relative; overflow: hidden;
    }
    .geo-card .badge-change {
        position: absolute; top: 10px; right: 10px;
        font-size: 10px; font-weight: 700; border-radius: 20px; padding: 2px 7px;
    }
    .badge-tetap  { background: #f0f0f0; color: #888; }
    .badge-up     { background: #e8f5e9; color: #43a047; }
    .badge-down   { background: #fdecea; color: #e53935; }
    .geo-card .card-icon {
        width: 38px; height: 38px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 17px; margin-bottom: 10px;
    }
    .geo-card .card-val  { font-size: 22px; font-weight: 700; color: #222; line-height: 1.1; }
    .geo-card .card-val span { font-size: 12px; font-weight: 500; color: #888; margin-left: 3px; }
    .geo-card .card-lbl  { font-size: 11px; color: #aaa; margin-top: 2px; }

    /* Two-column middle section */
    .geo-mid-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 18px; }

    /* Chart / map cards */
    .chart-card {
        background: #fff; border: 1px solid #eee; border-radius: 10px;
        padding: 18px; margin-bottom: 18px;
    }
    .chart-card-left { display: flex; flex-direction: column; gap: 18px; }
    .chart-card .chart-title {
        font-size: 13px; font-weight: 600; color: #333; margin-bottom: 14px;
    }
    .chart-card .chart-sub { font-size: 11px; color: #aaa; margin-top: -10px; margin-bottom: 12px; }

    /* Map */
    #geo-map { height: 554px; border-radius: 8px; z-index: 1; }

    /* Map tab buttons */
    .map-tab-btn {
        padding: 6px 16px; border-radius: 6px; border: 1px solid #ddd;
        background: #f5f5f5; color: #555; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.2s;
    }
    .map-tab-btn:hover { background: #e8f5e9; border-color: #4caf50; color: #2e7d32; }
    .map-tab-btn.active { background: #4caf50; border-color: #4caf50; color: #fff; }

    /* Comparison chart */
    .chart-card-compare .chart-title-row {
        display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;
    }
    .chart-toggle-btns { display: flex; gap: 6px; }
    .chart-toggle-btns button {
        font-size: 11px; padding: 3px 12px; border-radius: 5px; border: 1px solid #ddd;
        background: #f5f5f5; color: #666; cursor: pointer; font-weight: 600;
    }
    .chart-toggle-btns button.active { background: #ffbf00; border-color: #ffbf00; color: #fff; }

    /* Highlight cards */
    .geo-highlight-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 18px; }
    .geo-hl-card {
        background: #fff; border: 1px solid #eee; border-radius: 10px;
        padding: 14px 16px; display: flex; align-items: center; gap: 12px;
    }
    .geo-hl-card .hl-icon {
        width: 42px; height: 42px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;
    }
    .geo-hl-card .hl-tag  { font-size: 10px; font-weight: 700; letter-spacing: 1px; margin-bottom: 2px; }
    .geo-hl-card .hl-name { font-size: 13px; font-weight: 700; color: #222; }
    .geo-hl-card .hl-sub  { font-size: 11px; color: #888; }

    /* Table */
    .geo-table-wrap { background: #fff; border: 1px solid #eee; border-radius: 10px; padding: 18px; }
    .geo-table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
    .geo-table-header .tbl-title { font-size: 14px; font-weight: 600; color: #333; }
    .geo-search-input {
        border: 1px solid #ddd; border-radius: 6px; padding: 6px 12px 6px 32px;
        font-size: 13px; background: #f9f9f9; outline: none; width: 200px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23aaa' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: 10px center;
    }
    .geo-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .geo-table th { padding: 8px 12px; text-align: left; color: #777; font-weight: 600; border-bottom: 2px solid #f0f0f0; }
    .geo-table td { padding: 10px 12px; border-bottom: 1px solid #f5f5f5; color: #333; }
    .geo-table tbody tr:hover { background: #fffbf0; }
    .geo-pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 14px; font-size: 13px; color: #888; }
    .geo-pager { display: flex; gap: 4px; }
    .geo-pager button {
        width: 30px; height: 30px; border-radius: 6px; border: 1px solid #ddd;
        background: #fff; color: #555; cursor: pointer; font-size: 13px;
    }
    .geo-pager button.active { background: #ffbf00; border-color: #ffbf00; color: #fff; font-weight: 700; }
    .geo-pager button:disabled { opacity: 0.4; cursor: default; }

    .sumber { text-align: right; font-size: 12px; color: #bbb; margin-top: 10px; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
<section class="about-section">
<div class="auto-container">
<div class="statistik-wrapper">

    {{-- SIDEBAR --}}
    <div class="statistik-sidebar">
        <nav class="nav flex-column">
            <a class="nav-link active" href="{{ route('statistik.geografis') }}"><i class="fa fa-map"></i> Geografis</a>
            <a class="nav-link" href="{{ route('statistik.iklim') }}"><i class="fa fa-cloud"></i> Iklim</a>
            <a class="nav-link" href="{{ route('statistik.kependudukan') }}"><i class="fa fa-users"></i> Kependudukan</a>
            <a class="nav-link" href="{{ route('statistik.pendidikan') }}"><i class="fa fa-graduation-cap"></i> Pendidikan</a>
            <a class="nav-link" href="{{ route('statistik.kesehatan') }}"><i class="fa fa-plus-circle"></i> Kesehatan</a>
            <a class="nav-link" href="{{ route('statistik.bencana') }}"><i class="fa fa-house-flood-water"></i> Monitor Bencana</a>
        </nav>
    </div>

    {{-- KONTEN --}}
    <div class="statistik-content">

        {{-- Header Bar --}}
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
            <div style="flex:1; background:#ffbf00; color:#fff; text-align:center; padding:14px; border-radius:8px; font-weight:700; font-size:15px; letter-spacing:1px;">
                GEOGRAFIS JAKARTA BARAT {{ $geo->tahun }}
            </div>
            <div style="position:relative; flex-shrink:0;">
                <div id="dropdownTahunBtn" style="display:flex; align-items:center; gap:8px; border:2px solid #ffbf00; border-radius:6px; background:#fff; color:#b8860b; font-weight:700; font-size:14px; padding:6px 12px; cursor:pointer; white-space:nowrap; user-select:none;">
                    <i class="fa fa-calendar"></i>
                    {{ $geo->tahun }}
                    <span style="font-size:10px;">&#9660;</span>
                </div>
                <div id="dropdownTahunMenu" style="display:none; position:absolute; top:calc(100% + 4px); right:0; background:#fff; border:2px solid #ffbf00; border-radius:6px; min-width:100%; z-index:9999; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                    <a href="{{ route('statistik.geografis') }}" style="display:block; padding:8px 16px; color:#b8860b; font-weight:700; font-size:14px; text-decoration:none; background:#ffbf00;">{{ $geo->tahun }}</a>
                </div>
            </div>
        </div>

        {{-- Summary Cards --}}
        @php
            $totalKelurahan = $luas->sum(fn($l) => optional($l->kecamatan)->jumlah_kelurahan ?? 0);
            $jumlahKecamatan = $luas->count();
            $kepadatan = $geo->luas_kota_km2 > 0
                ? round($geo->luas_kota_km2 > 0 ? 19243 : 0)
                : 0;
        @endphp
        <div class="geo-summary-grid">
            <div class="geo-card">
                <span class="badge-change badge-up">+0.2%</span>
                <div class="card-icon" style="background:#fff8e1;">
                    <i class="fa fa-map" style="color:#f9a825;"></i>
                </div>
                <div class="card-val">{{ number_format($geo->luas_kota_km2, 2) }}<span>km²</span></div>
                <div class="card-lbl">Luas Wilayah</div>
            </div>
            <div class="geo-card">
                <span class="badge-change badge-tetap">Tetap</span>
                <div class="card-icon" style="background:#e3f0ff;">
                    <i class="fa fa-map-marker-alt" style="color:#1e88e5;"></i>
                </div>
                <div class="card-val">{{ $jumlahKecamatan }}</div>
                <div class="card-lbl">Jumlah Kecamatan</div>
            </div>
            <div class="geo-card">
                <span class="badge-change badge-tetap">Tetap</span>
                <div class="card-icon" style="background:#fce4ec;">
                    <i class="fa fa-building" style="color:#e91e8c;"></i>
                </div>
                <div class="card-val">56</div>
                <div class="card-lbl">Jumlah Kelurahan</div>
            </div>
            <div class="geo-card">
                <span class="badge-change badge-up">+1.4%</span>
                <div class="card-icon" style="background:#f3e5f5;">
                    <i class="fa fa-users" style="color:#9c27b0;"></i>
                </div>
                <div class="card-val">19.243<span>/km²</span></div>
                <div class="card-lbl">Kepadatan Wilayah</div>
            </div>
        </div>

        {{-- Middle: Charts (left) + Map (right) --}}
        <div class="geo-mid-grid">

            {{-- LEFT: Bar chart + Donut --}}
            <div class="chart-card-left">
                <div class="chart-card" style="margin-bottom:0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="chart-title" style="margin-bottom:0;">Luas Wilayah per Kecamatan</div>
                        <i class="fa fa-ellipsis-v" style="color:#ccc;cursor:pointer;"></i>
                    </div>
                    <div id="chart-bar-luas" style="margin-top:10px;"></div>
                </div>
                <div class="chart-card" style="margin-bottom:0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="chart-title" style="margin-bottom:0;">Persentase Luas Wilayah</div>
                        <div style="font-size:11px;color:#aaa;">Total {{ number_format($geo->luas_kota_km2, 1) }} km²</div>
                    </div>
                    <div id="chart-donut-persen" style="margin-top:6px;"></div>
                </div>
            </div>

            {{-- RIGHT: Leaflet Map --}}
            <div class="chart-card" style="margin-bottom:0; display:flex; flex-direction:column; padding:14px;">
                {{-- Tab Pilihan Layer --}}
                <div style="display:flex; gap:8px; margin-bottom:10px; flex-wrap:wrap;">
                    <button class="map-tab-btn active" onclick="switchLayer('banjir')" id="tab-banjir">
                        <i class="fa fa-tint"></i> Banjir
                    </button>
                    <button class="map-tab-btn" onclick="switchLayer('damkar')" id="tab-damkar">
                        <i class="fa fa-fire-extinguisher"></i> Pos Damkar
                    </button>
                    <button class="map-tab-btn" onclick="switchLayer('zona')" id="tab-zona">
                        <i class="fa fa-shield-alt"></i> Zona Aman
                    </button>
                </div>
                <div id="geo-map"></div>
            </div>
        </div>

        {{-- Comparison Chart --}}
        <div class="chart-card chart-card-compare">
            <div class="chart-title-row">
                <div>
                    <div class="chart-title" style="margin-bottom:2px;">Perbandingan Statistik Lanjutan</div>
                    <div class="chart-sub">Komparasi kros-tabulasi antara luas wilayah dan densitas penduduk</div>
                </div>
            </div>
            <div id="chart-compare"></div>
        </div>

        {{-- Highlight Cards --}}
        @php
            $sortedLuas = $luas->sortByDesc('luas_km2');
            $terluas  = $sortedLuas->first();
            $terkecil = $sortedLuas->last();
            $totalLuas = $luas->sum('luas_km2');
        @endphp
        <div class="geo-highlight-grid">
            <div class="geo-hl-card">
                <div class="hl-icon" style="background:#fff8e1;">
                    <i class="fa fa-expand-arrows-alt" style="color:#f9a825;"></i>
                </div>
                <div>
                    <div class="hl-tag" style="color:#f9a825;">TERLUAS</div>
                    <div class="hl-name">Kecamatan {{ $terluas->kecamatan->nama_kecamatan }}</div>
                    <div class="hl-sub">{{ number_format($terluas->luas_km2, 2) }} km² ({{ number_format($terluas->persentase, 1) }}% dari total)</div>
                </div>
            </div>
            <div class="geo-hl-card">
                <div class="hl-icon" style="background:#e3f0ff;">
                    <i class="fa fa-compress-arrows-alt" style="color:#1e88e5;"></i>
                </div>
                <div>
                    <div class="hl-tag" style="color:#1e88e5;">TERKECIL</div>
                    <div class="hl-name">Kecamatan {{ $terkecil->kecamatan->nama_kecamatan }}</div>
                    <div class="hl-sub">{{ number_format($terkecil->luas_km2, 2) }} km² ({{ number_format($terkecil->persentase, 1) }}% dari total)</div>
                </div>
            </div>
            <div class="geo-hl-card">
                <div class="hl-icon" style="background:#fce4ec;">
                    <i class="fa fa-users" style="color:#e91e8c;"></i>
                </div>
                <div>
                    <div class="hl-tag" style="color:#e91e8c;">TERPADAT</div>
                    <div class="hl-name">Kecamatan Tambora</div>
                    <div class="hl-sub">48.243 jiwa/km²</div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="geo-table-wrap">
            <div class="geo-table-header">
                <div class="tbl-title">Tabel Geografis Rinci</div>
                <input class="geo-search-input" type="text" id="geo-search" placeholder="Cari kecamatan..." oninput="filterTable()">
            </div>
            <table class="geo-table" id="geo-table">
                <thead>
                    <tr>
                        <th>Kecamatan</th>
                        <th>Luas (km²)</th>
                        <th>Kelurahan</th>
                        <th>RW</th>
                        <th>RT</th>
                        <th>Populasi</th>
                    </tr>
                </thead>
                <tbody id="geo-table-body">
                    @foreach($luas->sortByDesc('luas_km2') as $row)
                    <tr data-name="{{ strtolower($row->kecamatan->nama_kecamatan) }}">
                        <td>{{ $row->kecamatan->nama_kecamatan }}</td>
                        <td>{{ number_format($row->luas_km2, 2) }}</td>
                        <td>{{ $row->jumlah_kelurahan ?? '—' }}</td>
                        <td>{{ $row->jumlah_rw ?? '—' }}</td>
                        <td>{{ $row->jumlah_rt ?? '—' }}</td>
                        <td>{{ $row->jumlah_penduduk ? number_format($row->jumlah_penduduk) : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="geo-pagination">
                <div id="pager-info">Showing 1–4 of {{ $luas->count() }} Kecamatan</div>
                <div class="geo-pager" id="geo-pager"></div>
            </div>
        </div>

        <div class="sumber">Sumber: {{ $geo->sumber }}</div>

    </div>{{-- end statistik-content --}}
</div>{{-- end statistik-wrapper --}}
</div>{{-- end auto-container --}}
</section>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var btn  = document.getElementById('dropdownTahunBtn');
    var menu = document.getElementById('dropdownTahunMenu');
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    });
    document.addEventListener('click', function () {
        menu.style.display = 'none';
    });
})();
</script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>

var namaKec  = {!! json_encode($luas->sortByDesc('luas_km2')->pluck('kecamatan.nama_kecamatan')) !!};
var luasData = {!! json_encode($luas->sortByDesc('luas_km2')->pluck('luas_km2')->map(fn($v) => (float)$v)) !!};
var persen   = {!! json_encode($luas->sortByDesc('luas_km2')->pluck('persentase')->map(fn($v) => (float)$v)) !!};

// Warna per kecamatan (konsisten map & chart)
var warnaKec = {
    'KALIDERES'         : '#f9a825',
    'CENGKARENG'        : '#fb8c00',
    'KEBON JERUK'       : '#e53935',
    'KEMBANGAN'         : '#8e24aa',
    'GROGOL PETAMBURAN' : '#039be5',
    'PALMERAH'          : '#43a047',
    'TAMBORA'           : '#00897b',
    'TAMAN SARI'        : '#6d4c41',
};

function getWarna(nama) {
    return warnaKec[nama.toUpperCase()] || '#90a4ae';
}

var warnaArr = namaKec.map(function(n) { return getWarna(n); });

// ── Chart Bar Luas ─────────────────────────────────────────────
new ApexCharts(document.querySelector('#chart-bar-luas'), {
    chart: { type: 'bar', height: 240, toolbar: { show: false }, sparkline: { enabled: false } },
    series: [{ name: 'Luas (km²)', data: luasData }],
    xaxis: { categories: namaKec, labels: { style: { fontSize: '10px' } } },
    colors: warnaArr,
    plotOptions: { bar: { borderRadius: 3, distributed: true, horizontal: true } },
    dataLabels: { enabled: true, style: { fontSize: '9px' } },
    legend: { show: false },
    grid: { borderColor: '#f5f5f5' },
}).render();

// ── Chart Donut Persentase ─────────────────────────────────────
new ApexCharts(document.querySelector('#chart-donut-persen'), {
    chart: { type: 'donut', height: 260 },
    series: persen,
    labels: namaKec,
    colors: warnaArr,
    dataLabels: { enabled: true, style: { fontSize: '10px' } },
    legend: { position: 'bottom', fontSize: '11px' },
    plotOptions: { pie: { donut: { labels: {
        show: true,
        total: { show: true, label: 'Total', fontSize: '12px',
                 formatter: function() { return '{!! number_format($geo->luas_kota_km2, 1) !!} km²'; } }
    }}}},
}).render();
</script>

<script>
// ── Chart Comparison ──────────────────────────────────────────
var kepadatanData = [15421, 8543, 11234, 9876, 18234, 12543, 14321, 48243];
var namaCompare = {!! json_encode($luas->sortByDesc('luas_km2')->pluck('kecamatan.nama_kecamatan')) !!};

var chartCompare = new ApexCharts(document.querySelector('#chart-compare'), {
    chart: { type: 'bar', height: 300, toolbar: { show: false } },
    series: [
        { name: 'Luas Wilayah (km²)', data: luasData },
        { name: 'Kepadatan (×100/km²)', data: kepadatanData.map(function(v){ return parseFloat((v/100).toFixed(1)); }) },
    ],
    xaxis: {
        categories: namaCompare,
        labels: {
            rotate: -30,
            rotateAlways: true,
            style: { fontSize: '10px' },
            trim: false,
        }
    },
    colors: ['#f9a825', '#1e88e5'],
    dataLabels: { enabled: false },
    plotOptions: { bar: { borderRadius: 3, columnWidth: '60%' } },
    legend: { position: 'bottom', fontSize: '11px' },
    grid: { borderColor: '#f5f5f5' },
});
chartCompare.render();

function setView(v) {
    document.getElementById('btn-chart-view').classList.toggle('active', v === 'chart');
    document.getElementById('btn-table-view').classList.toggle('active', v === 'table');
}

// ── Leaflet Map ───────────────────────────────────────────────
var map = L.map('geo-map').setView([-6.15, 106.76], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors', maxZoom: 19
}).addTo(map);

var luasByNama = {};
namaKec.forEach(function(n, i) { luasByNama[n.toUpperCase()] = luasData[i]; });

var kecJakbar = Object.keys(warnaKec);

// ── Data titik per layer ────────────────
var layerData = {
    banjir: {
        points: [
            { lat:-6.108, lng:106.700, label:'Banjir Prioritas 1 – Kalideres Barat',  type:'p1' },
            { lat:-6.115, lng:106.715, label:'Banjir Prioritas 1 – Kalideres Timur',  type:'p1' },
            { lat:-6.095, lng:106.735, label:'Banjir Prioritas 1 – Cengkareng Utara', type:'p1' },
            { lat:-6.130, lng:106.745, label:'Banjir Prioritas 2 – Cengkareng Sel.',  type:'p2' },
            { lat:-6.160, lng:106.725, label:'Banjir Prioritas 2 – Kembangan Utara',  type:'p2' },
            { lat:-6.148, lng:106.760, label:'Banjir Prioritas 2 – Kebon Jeruk',      type:'p2' },
            { lat:-6.170, lng:106.800, label:'Banjir Prioritas 3 – Palmerah',         type:'p3' },
            { lat:-6.163, lng:106.820, label:'Banjir Prioritas 3 – Taman Sari',       type:'p3' },
            { lat:-6.172, lng:106.835, label:'Banjir Prioritas 3 – Tambora',          type:'p3' },
            { lat:-6.120, lng:106.780, label:'Rumah Pompa – Grogol',                  type:'pompa' },
            { lat:-6.155, lng:106.745, label:'Rumah Pompa – Kembangan',               type:'pompa' },
            { lat:-6.138, lng:106.810, label:'Pintu Air – Grogol Petamburan',         type:'pintuair' },
        ],
        legend: [
            { color:'#e53935', emoji:'●', label:'Lokasi Banjir Prioritas 1', count:3 },
            { color:'#fb8c00', emoji:'●', label:'Lokasi Banjir Prioritas 2', count:3 },
            { color:'#fdd835', emoji:'●', label:'Lokasi Banjir Prioritas 3', count:3 },
            { color:'#1e88e5', emoji:'⊕', label:'Rumah Pompa',              count:2 },
            { color:'#43a047', emoji:'⊞', label:'Pintu Air',                count:1 },
        ]
    },
    damkar: {
        points: [
            { lat:-6.112, lng:106.705, label:'Pos Damkar Kalideres',          type:'damkar' },
            { lat:-6.100, lng:106.738, label:'Pos Damkar Cengkareng',         type:'damkar' },
            { lat:-6.165, lng:106.728, label:'Pos Damkar Kembangan',          type:'damkar' },
            { lat:-6.152, lng:106.768, label:'Pos Damkar Kebon Jeruk',        type:'damkar' },
            { lat:-6.148, lng:106.800, label:'Pos Damkar Grogol Petamburan',  type:'damkar' },
            { lat:-6.168, lng:106.810, label:'Pos Damkar Palmerah',           type:'damkar' },
            { lat:-6.175, lng:106.830, label:'Pos Damkar Tambora',            type:'damkar' },
            { lat:-6.160, lng:106.840, label:'Pos Damkar Taman Sari',         type:'damkar' },
        ],
        legend: [
            { color:'#e53935', emoji:'🚒', label:'Pos Pemadam Kebakaran', count:8 },
        ]
    },
    zona: {
        points: [
            { lat:-6.118, lng:106.710, label:'Zona Aman – Kalideres',          type:'aman' },
            { lat:-6.107, lng:106.742, label:'Zona Aman – Cengkareng Utara',   type:'aman' },
            { lat:-6.162, lng:106.732, label:'Zona Aman – Kembangan',          type:'aman' },
            { lat:-6.155, lng:106.772, label:'Zona Aman – Kebon Jeruk',        type:'aman' },
            { lat:-6.143, lng:106.806, label:'Zona Aman – Grogol',             type:'aman' },
            { lat:-6.170, lng:106.817, label:'Zona Aman – Palmerah',           type:'aman' },
            { lat:-6.173, lng:106.832, label:'Zona Aman – Tambora',            type:'waspada' },
            { lat:-6.158, lng:106.842, label:'Zona Waspada – Taman Sari',      type:'waspada' },
        ],
        legend: [
            { color:'#43a047', emoji:'●', label:'Zona Aman',     count:6 },
            { color:'#fb8c00', emoji:'●', label:'Zona Waspada',  count:2 },
        ]
    }
};

// Warna/ikon per type marker
var markerStyle = {
    p1:       { color:'#e53935', size:12 },
    p2:       { color:'#fb8c00', size:11 },
    p3:       { color:'#fdd835', size:10 },
    pompa:    { color:'#1e88e5', size:13 },
    pintuair: { color:'#43a047', size:13 },
    damkar:   { color:'#e53935', size:13 },
    aman:     { color:'#43a047', size:12 },
    waspada:  { color:'#fb8c00', size:12 },
};

var activeMarkers = [];
var legendControl = null;
var currentLayerKey = 'banjir';

// ── GeoJSON Polygon ────────────────────────────────────────────
fetch('{{ asset("assets/geojson/kecamatan.geojson") }}')
    .then(function(r) { return r.json(); })
    .then(function(data) {
        data.features = data.features.filter(function(f) {
            return kecJakbar.includes((f.properties.name || '').toUpperCase());
        });
        L.geoJSON(data, {
            style: function(feature) {
                var nama = (feature.properties.name || '').toUpperCase();
                return { color: '#1a237e', weight: 2, fillColor: getWarna(nama), fillOpacity: 0.45 };
            },
            onEachFeature: function(feature, layer) {
                var nama = feature.properties.name || '';
                var namaUp = nama.toUpperCase();
                var luas = luasByNama[namaUp] ? luasByNama[namaUp].toFixed(2) + ' km²' : '—';
                layer.on('mouseover', function() {
                    layer.setStyle({ fillOpacity: 0.7 });
                    layer.bindTooltip('<b>' + nama + '</b><br>' + luas, { sticky: true }).openTooltip();
                });
                layer.on('mouseout', function() { layer.setStyle({ fillOpacity: 0.45 }); });
            }
        }).addTo(map);

        // Render layer default
        renderMapLayer('banjir');
    });

function makeMarkerIcon(type) {
    var s = markerStyle[type] || { color:'#888', size:11 };
    var emoji = (type === 'pompa')    ? '⊕'
              : (type === 'pintuair') ? '⊞'
              : (type === 'damkar')   ? '🚒'
              : '';
    var inner = emoji
        ? '<div style="font-size:' + (s.size+4) + 'px;line-height:1;">' + emoji + '</div>'
        : '<div style="width:' + s.size + 'px;height:' + s.size + 'px;border-radius:50%;background:' + s.color + ';border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,0.4);"></div>';
    return L.divIcon({ className:'', html: inner, iconSize:[s.size+4, s.size+4], iconAnchor:[(s.size+4)/2,(s.size+4)/2] });
}

function renderMapLayer(key) {
    activeMarkers.forEach(function(m) { map.removeLayer(m); });
    activeMarkers = [];

    var data = layerData[key];
    data.points.forEach(function(p) {
        var m = L.marker([p.lat, p.lng], { icon: makeMarkerIcon(p.type) })
            .addTo(map)
            .bindPopup('<b>' + p.label + '</b>');
        activeMarkers.push(m);
    });

    // Update legend
    if (legendControl) { map.removeControl(legendControl); legendControl = null; }
    legendControl = L.control({ position: 'topright' });
    legendControl.onAdd = function() {
        var div = L.DomUtil.create('div');
        div.style.cssText = 'background:#fff;padding:10px 14px;border-radius:8px;font-size:12px;line-height:22px;box-shadow:0 1px 6px rgba(0,0,0,0.2);min-width:200px;';
        div.innerHTML = '<b>Keterangan :</b><br>';
        data.legend.forEach(function(item) {
            div.innerHTML += '<span style="color:' + item.color + ';font-size:15px;margin-right:6px;vertical-align:middle;">' + item.emoji + '</span>'
                + item.label + ' <b>: ' + item.count + '</b><br>';
        });
        return div;
    };
    legendControl.addTo(map);
}

function switchLayer(key) {
    currentLayerKey = key;
    // Update tab aktif
    ['banjir','damkar','zona'].forEach(function(k) {
        document.getElementById('tab-' + k).classList.toggle('active', k === key);
    });
    renderMapLayer(key);
}

// ── Table Pagination & Search ─────────────────────────────────
var PAGE_SIZE = 4;
var currentPage = 1;
var filteredRows = [];

function getAllRows() {
    return Array.from(document.querySelectorAll('#geo-table-body tr'));
}

function filterTable() {
    var q = document.getElementById('geo-search').value.toLowerCase();
    filteredRows = getAllRows().filter(function(r) {
        return r.dataset.name.includes(q);
    });
    currentPage = 1;
    renderTable();
}

function renderTable() {
    var rows = filteredRows.length ? filteredRows : getAllRows();
    var total = rows.length;
    var start = (currentPage - 1) * PAGE_SIZE;
    var end   = Math.min(start + PAGE_SIZE, total);

    getAllRows().forEach(function(r) { r.style.display = 'none'; });
    rows.slice(start, end).forEach(function(r) { r.style.display = ''; });

    document.getElementById('pager-info').textContent =
        'Showing ' + (start+1) + '–' + end + ' of ' + total + ' Kecamatan';

    var pages = Math.ceil(total / PAGE_SIZE);
    var pager = document.getElementById('geo-pager');
    pager.innerHTML = '';

    // Prev
    var prev = document.createElement('button');
    prev.innerHTML = '&lsaquo;';
    prev.disabled = currentPage === 1;
    prev.onclick = function() { currentPage--; renderTable(); };
    pager.appendChild(prev);

    for (var p = 1; p <= pages; p++) {
        (function(pg) {
            var btn = document.createElement('button');
            btn.textContent = pg;
            if (pg === currentPage) btn.classList.add('active');
            btn.onclick = function() { currentPage = pg; renderTable(); };
            pager.appendChild(btn);
        })(p);
    }

    // Next
    var next = document.createElement('button');
    next.innerHTML = '&rsaquo;';
    next.disabled = currentPage === pages;
    next.onclick = function() { currentPage++; renderTable(); };
    pager.appendChild(next);
}

document.addEventListener('DOMContentLoaded', function() { renderTable(); });
</script>
@endpush
