@extends('landing-page.layout.app')

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

    /* Dropdown tahun */
    .dropdown-tahun { position:relative; flex-shrink:0; }
    .dropdown-tahun-btn {
        display:flex; align-items:center; gap:8px;
        border:2px solid #ffbf00; border-radius:6px; background:#fff;
        color:#b8860b; font-weight:700; font-size:14px;
        padding:6px 12px; cursor:pointer; white-space:nowrap; user-select:none;
    }
    .dropdown-tahun-btn .arrow { font-size:10px; transition:transform .2s; }
    .dropdown-tahun-btn.open .arrow { transform:rotate(180deg); }
    .dropdown-tahun-menu {
        display:none; position:absolute; top:calc(100% + 4px); right:0;
        background:#fff; border:2px solid #ffbf00; border-radius:6px;
        min-width:100%; z-index:9999; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.1);
    }
    .dropdown-tahun-menu.show { display:block; }
    .dropdown-tahun-menu a {
        display:block; padding:8px 16px; color:#555;
        font-weight:600; font-size:14px; text-decoration:none; transition:background .15s;
    }
    .dropdown-tahun-menu a:hover { background:#fff8e1; color:#b8860b; }
    .dropdown-tahun-menu a.active { background:#ffbf00; color:#fff; }

    /* ── Stat cards ─────────────────────────────────────────── */
    .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:16px; }
    .stat-card {
        background:#f9f9f9; border:1px solid #eee; border-radius:8px;
        padding:16px 24px; position:relative; overflow:hidden;
    }
    .sc-card-body  { display:flex; justify-content:space-between; align-items:flex-start; margin-top:8px; }
    .sc-card-left  { flex:1; }
    .sc-icon {
        width:48px; height:48px; border-radius:12px;
        display:flex; align-items:center; justify-content:center;
        font-size:22px; flex-shrink:0; margin-left:12px;
        background:#2a78d6; color:#fff;   /* WARNA LAMA: #ffbf00 */
    }
    /* Icon warna-warni per kartu */
    .sc-icon.ic-blue   { background:#2a78d6; }
    .sc-icon.ic-orange { background:#eb6834; }
    .sc-icon.ic-green  { background:#008300; }
    .sc-icon.ic-violet { background:#4a3aa7; }
    .sc-label { font-size:12px; font-weight:600; color:#888; letter-spacing:1px; text-transform:uppercase; margin-bottom:4px; }
    .sc-value { font-size:28px; font-weight:700; color:#333; line-height:1.15; margin-bottom:6px; }
    .sc-desc  { font-size:11px; color:#aaa; }
    .sc-trend { font-weight:700; padding:1px 6px; border-radius:20px; font-size:10px; margin-right:4px; }
    .sc-trend.up { background:#e8f5e9; color:#2e7d32; }
    .sc-strong { color:#666; font-weight:700; }

    /* ── Panel card ─────────────────────────────────────────── */
    .panel-card { background:#fff; border:1px solid #ebebeb; border-radius:12px; padding:22px; }
    .pc-header  { display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; }
    .pc-title    { font-size:15px; font-weight:700; color:#1a1a1a; margin:0; display:flex; align-items:center; gap:8px; }
    .pc-title i  { color:#ffbf00; }
    .pc-link     { font-size:12px; font-weight:600; color:#b8860b; text-decoration:none; }
    .pc-legend   { display:flex; gap:14px; font-size:11px; color:#666; font-weight:600; }
    .pc-legend .dot { width:9px; height:9px; border-radius:50%; display:inline-block; margin-right:5px; }

    /* ── Distribusi + alert grid ────────────────────────────── */
    .infra-grid { display:grid; grid-template-columns:2fr 1fr; gap:16px; margin-bottom:16px; }

    /* ── Live alerts ────────────────────────────────────────── */
    .alert-list { display:flex; flex-direction:column; gap:10px; }
    .alert-item { display:flex; gap:12px; padding:12px 14px; border-radius:8px; background:#fafafa; border-left:3px solid #ccc; }
    .alert-item.red  { border-color:#e53935; background:#fff5f5; }
    .alert-item.warn { border-color:#ffbf00; background:#fff8e1; }
    .alert-item.blue { border-color:#5B82C0; background:#f4f7fc; }
    .alert-item.info { border-color:#bbb;    background:#f6f6f6; }
    .alert-ic { width:34px; height:34px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:14px; }
    .alert-ic.red  { background:#fdecea; color:#e53935; }
    .alert-ic.warn { background:#fff3cd; color:#b8860b; }
    .alert-ic.blue { background:#e7edf9; color:#5B82C0; }
    .alert-ic.info { background:#eee;    color:#888; }
    .alert-title { font-size:13px; font-weight:700; color:#333; }
    .alert-meta  { font-size:11px; color:#999; margin-top:2px; }

    /* ── Table card ─────────────────────────────────────────── */
    .table-card   { background:#fff; border:1px solid #ebebeb; border-radius:12px; padding:22px; margin-bottom:16px; }
    .table-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:12px; }
    .table-title  { font-size:15px; font-weight:700; color:#1a1a1a; margin:0; }
    .table-sub    { font-size:11px; color:#aaa; margin:2px 0 0; }
    .tbl-tools { display:flex; gap:8px; }
    .tbl-btn {
        display:inline-flex; align-items:center; gap:6px; border:1px solid #e0e0e0;
        background:#fff; color:#555; font-size:13px; font-weight:600;
        padding:7px 12px; border-radius:8px; cursor:pointer; transition:all .15s;
    }
    .tbl-btn:hover { border-color:#ffbf00; color:#b8860b; }

    .kes-table { width:100%; border-collapse:collapse; }
    .kes-table th {
        font-size:11px; font-weight:700; color:#9e9e9e;
        text-transform:uppercase; letter-spacing:.5px;
        padding:10px 14px; border-bottom:1px solid #f0f0f0; text-align:left;
    }
    .kes-table td { padding:12px 14px; font-size:13px; color:#333; border-bottom:1px solid #f9f9f9; }
    .kes-table tr:last-child td { border-bottom:none; }
    .kes-table tr:hover td { background:#fafafa; }
    .td-num { font-weight:600; }

    .badge-type { display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:700; padding:4px 10px; border-radius:20px; }
    .badge-type.wifi { background:#fff8e1; color:#b8860b; }
    .badge-type.cctv { background:#eef2fb; color:#3b5a9a; }

    .status { display:inline-flex; align-items:center; gap:6px; font-weight:600; font-size:13px; }
    .status .dot { width:8px; height:8px; border-radius:50%; }
    .status.on  { color:#2e7d32; } .status.on  .dot { background:#2e7d32; }
    .status.off { color:#e53935; } .status.off .dot { background:#e53935; }

    /* ── Pagination ─────────────────────────────────────────── */
    .pager { display:flex; align-items:center; justify-content:space-between; margin-top:16px; flex-wrap:wrap; gap:12px; }
    .pager-info { font-size:12px; color:#999; }
    .pager-btns { display:flex; gap:6px; }
    .page-btn {
        min-width:34px; height:34px; padding:0 10px; border:1px solid #e0e0e0;
        border-radius:8px; background:#fff; color:#555; font-weight:600; font-size:13px; cursor:pointer; transition:all .15s;
    }
    .page-btn:hover:not(:disabled) { border-color:#ffbf00; color:#b8860b; }
    .page-btn.active { background:#ffbf00; border-color:#ffbf00; color:#fff; }
    .page-btn:disabled { opacity:.45; cursor:not-allowed; }

    /* ── Peta sebaran ───────────────────────────────────────── */
    .map-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .map-cap  { font-size:12px; font-weight:700; color:#555; margin-bottom:8px; }
    .infra-map { width:100%; height:360px; border-radius:8px; border:1px solid #eee; z-index:0; }

    /* ── Footer ─────────────────────────────────────────────── */
    .kes-footer { font-size:11px; color:#bbb; text-align:right; margin-top:8px; }

    /* ── Responsive ─────────────────────────────────────────── */
    @media (max-width: 992px) {
        .stat-grid  { grid-template-columns: repeat(2,1fr); }
        .infra-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .kes-wrapper { flex-direction: column; padding: 20px 0; gap: 16px; }
        .stat-header { font-size: 15px; padding: 12px; }
        .table-card  { overflow-x: auto; }
        .kes-table   { min-width: 640px; }
        .map-grid    { grid-template-columns: 1fr; }
    }
    @media (max-width: 520px) {
        .stat-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
@php
    $tahun   = $tahun ?? date('Y');
    $topWifi = $jakWifi->sortByDesc('jumlah_titik')->first();

    // Kecamatan dengan perangkat offline terbanyak (untuk notifikasi)
    $offWifi = $distribusi->map(fn ($r) => ['nama' => $r['nama'], 'off' => $r['wifi'] - $r['wifi_aktif']])
        ->sortByDesc('off')->first();
    $offCctv = $distribusi->map(fn ($r) => ['nama' => $r['nama'], 'off' => $r['cctv'] - $r['cctv_aktif']])
        ->sortByDesc('off')->first();
@endphp

<div class="container-fluid px-4">
    <div class="kes-wrapper">

        @include('statistik.partials.sidebar')

        {{-- ── KONTEN ───────────────────────────────────── --}}
        <div class="kes-content">

            {{-- Header --}}
            <div class="stat-header-wrap">
                <div class="stat-header">INFRASTRUKTUR DIGITAL JAKARTA BARAT {{ $tahun }}</div>
                <div class="dropdown-tahun">
                    <div class="dropdown-tahun-btn" id="dropdownTahunBtn">
                        <i class="fa fa-calendar"></i>
                        {{ $tahun }}
                        <span class="arrow">&#9660;</span>
                    </div>
                    <div class="dropdown-tahun-menu" id="dropdownTahunMenu">
                        @forelse($availableTahun as $t)
                        <a href="{{ route('statistik.infrastruktur-digital', ['tahun' => $t]) }}"
                           class="{{ (int) $t === (int) $tahun ? 'active' : '' }}">{{ $t }}</a>
                        @empty
                        <a class="active" href="#">{{ $tahun }}</a>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ── 4 Stat Cards ────────────────────────── --}}
            <div class="stat-grid">
                {{-- Total JakWiFi --}}
                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">Total JakWiFi</div>
                            <div class="sc-value">{{ number_format($ringkasan['total_titik_wifi']) }}</div>
                            <div class="sc-desc">
                                @if(!is_null($ringkasan['tren_wifi']))
                                    <span class="sc-trend up"><i class="fa fa-arrow-up"></i> {{ $ringkasan['tren_wifi'] }}%</span>
                                @endif
                                Aktif: {{ number_format($ringkasan['wifi_aktif']) }} titik
                            </div>
                        </div>
                        <div class="sc-icon ic-blue"><i class="fa fa-wifi"></i></div>
                    </div>
                </div>
                {{-- Total Unit CCTV --}}
                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">Total Unit CCTV</div>
                            <div class="sc-value">{{ number_format($ringkasan['total_cctv']) }}</div>
                            <div class="sc-desc">
                                <span class="sc-trend up"><i class="fa fa-check"></i> {{ $ringkasan['cctv_online_pct'] }}%</span>
                                Online: {{ number_format($ringkasan['cctv_aktif']) }} unit
                            </div>
                        </div>
                        <div class="sc-icon ic-orange"><i class="fa fa-video"></i></div>
                    </div>
                </div>
                {{-- Perangkat Aktif --}}
                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">Perangkat Aktif</div>
                            <div class="sc-value">{{ $ringkasan['perangkat_aktif_pct'] }}%</div>
                            <div class="sc-desc">WiFi {{ $ringkasan['wifi_online_pct'] }}% &middot; CCTV {{ $ringkasan['cctv_online_pct'] }}% online</div>
                        </div>
                        <div class="sc-icon ic-green"><i class="fa fa-gauge-high"></i></div>
                    </div>
                </div>
                {{-- Total Pengguna --}}
                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">Pengguna JakWiFi</div>
                            <div class="sc-value">{{ number_format($ringkasan['total_pengguna']) }}</div>
                            <div class="sc-desc"><span class="sc-strong">Terbanyak:</span> {{ $topWifi?->kecamatan->nama_kecamatan ?? '-' }}</div>
                        </div>
                        <div class="sc-icon ic-violet"><i class="fa fa-users"></i></div>
                    </div>
                </div>
            </div>

            {{-- ── Distribusi Regional | Notifikasi ─────── --}}
            <div class="infra-grid">
                {{-- Distribusi per kecamatan --}}
                <div class="panel-card">
                    <div class="pc-header">
                        <div class="pc-title"><i class="fa fa-compass"></i> Distribusi Infrastruktur Regional</div>
                        <div class="pc-legend">
                            <span><span class="dot" style="background:#ffbf00;"></span>JakWiFi</span>
                            <span><span class="dot" style="background:#5B82C0;"></span>CCTV</span>
                        </div>
                    </div>
                    <div id="chart-distribusi" style="min-height:340px;"></div>
                </div>

                {{-- Notifikasi terkini --}}
                <div class="panel-card">
                    <div class="pc-header">
                        <div class="pc-title"><i class="fa fa-triangle-exclamation"></i> Notifikasi Terkini</div>
                        <a href="#" class="pc-link">Lihat Semua</a>
                    </div>
                    <div class="alert-list">
                        @if($offWifi && $offWifi['off'] > 0)
                        <div class="alert-item red">
                            <div class="alert-ic red"><i class="fa fa-wifi"></i></div>
                            <div>
                                <div class="alert-title">Titik JakWiFi Offline</div>
                                <div class="alert-meta">Kecamatan {{ $offWifi['nama'] }} &bull; {{ number_format($offWifi['off']) }} titik</div>
                            </div>
                        </div>
                        @endif

                        <div class="alert-item warn">
                            <div class="alert-ic warn"><i class="fa fa-screwdriver-wrench"></i></div>
                            <div>
                                <div class="alert-title">Pemeliharaan Terjadwal</div>
                                <div class="alert-meta">Cengkareng Sektor B &bull; 1 jam lalu</div>
                            </div>
                        </div>

                        @if($offCctv && $offCctv['off'] > 0)
                        <div class="alert-item blue">
                            <div class="alert-ic blue"><i class="fa fa-video-slash"></i></div>
                            <div>
                                <div class="alert-title">CCTV Kehilangan Koneksi</div>
                                <div class="alert-meta">Kecamatan {{ $offCctv['nama'] }} &bull; {{ number_format($offCctv['off']) }} unit</div>
                            </div>
                        </div>
                        @endif

                        <div class="alert-item info">
                            <div class="alert-ic info"><i class="fa fa-circle-info"></i></div>
                            <div>
                                <div class="alert-title">Pembaruan Firmware Berhasil</div>
                                <div class="alert-meta">Seluruh node region &bull; 5 jam lalu</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Peta Sebaran ─────────────────────────── --}}
            <div class="panel-card" style="margin-bottom:16px;">
                <div class="pc-header">
                    <div class="pc-title"><i class="fa fa-map-location-dot"></i> Peta Sebaran Infrastruktur Digital</div>
                    <div class="pc-legend">
                        <span><span class="dot" style="background:#ffbf00;"></span>JakWiFi</span>
                        <span><span class="dot" style="background:#5B82C0;"></span>CCTV</span>
                    </div>
                </div>
                <div class="map-grid">
                    <div>
                        <div class="map-cap">Heat Map Sebaran</div>
                        <div id="map-heat" class="infra-map"></div>
                    </div>
                    <div>
                        <div class="map-cap">Titik Sebaran</div>
                        <div id="map-titik" class="infra-map"></div>
                    </div>
                </div>
                <div class="sc-desc" style="margin-top:10px;">
                    Kepadatan titik mengikuti data asli per kecamatan; posisi tiap titik bersifat ilustratif.
                </div>
            </div>

            {{-- ── Rincian Unit ─────────────────────────── --}}
            <div class="table-card">
                <div class="table-header">
                    <div>
                        <p class="table-title">Rincian Unit Infrastruktur</p>
                        <p class="table-sub">Rekap JakWiFi &amp; CCTV per kecamatan — {{ $tahun }}</p>
                    </div>
                    <div class="tbl-tools">
                        <select class="tbl-btn" id="filterType">
                            <option value="ALL">Semua Jenis</option>
                            <option value="JAKWIFI">JakWiFi</option>
                            <option value="CCTV">CCTV</option>
                        </select>
                        <button class="tbl-btn" id="exportCsv"><i class="fa fa-download"></i> Export CSV</button>
                    </div>
                </div>
                <table class="kes-table" id="unitTable">
                    <thead>
                        <tr>
                            <th>Kecamatan</th>
                            <th>Jenis</th>
                            <th>Total Unit</th>
                            <th>Aktif</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="unit-tbody">
                        @forelse($unitRows as $row)
                        @php $offline = $row['total'] - $row['aktif']; @endphp
                        <tr class="unit-row" data-type="{{ $row['tipe'] }}">
                            <td><strong>{{ $row['kecamatan'] }}</strong></td>
                            <td>
                                @if($row['tipe'] === 'JAKWIFI')
                                    <span class="badge-type wifi"><i class="fa fa-wifi"></i> JAKWIFI</span>
                                @else
                                    <span class="badge-type cctv"><i class="fa fa-video"></i> CCTV</span>
                                @endif
                            </td>
                            <td class="td-num">{{ number_format($row['total']) }}</td>
                            <td class="td-num">{{ number_format($row['aktif']) }}</td>
                            <td>
                                @if($offline <= 0)
                                    <span class="status on"><span class="dot"></span> Aktif</span>
                                @else
                                    <span class="status off"><span class="dot"></span> {{ number_format($offline) }} offline</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center" style="color:#bbb;padding:20px;">Belum ada data untuk tahun {{ $tahun }}.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pager">
                    <div class="pager-info" id="pagerInfo"></div>
                    <div class="pager-btns" id="pagerBtns"></div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="kes-footer">
                Sumber: Diskominfotik Jakarta Barat &bull; Data Tahun {{ $tahun }}
            </div>

        </div>{{-- /.kes-content --}}
    </div>{{-- /.kes-wrapper --}}
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js"></script>
<script>
(function () {
    var sebaranKec = {!! json_encode($sebaranKec) !!};
    if (typeof L === 'undefined' || !sebaranKec.length) return;

    var warna  = { jakwifi: '#ffbf00', cctv: '#5B82C0' };
    var tipeCctv = ['Type A', 'Type B', 'NON'];
    var center = [-6.168, 106.785];
    var zoom   = 12;

    // Hitungan titik per kecamatan (uppercase agar cocok dgn properti GeoJSON)
    var counts = {};
    sebaranKec.forEach(function (k) { counts[k.nama.toUpperCase()] = k; });
    var namesJakbar = Object.keys(counts);

    function baseTile() {
        return L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 19, attribution: '&copy; OpenStreetMap &copy; CARTO'
        });
    }

    // PRNG deterministik (mulberry32) supaya posisi titik stabil tiap reload
    function strSeed(s) { var h = 2166136261; for (var i = 0; i < s.length; i++) { h ^= s.charCodeAt(i); h = Math.imul(h, 16777619); } return h >>> 0; }
    function mulberry32(a) { return function () { a |= 0; a = a + 0x6D2B79F5 | 0; var t = Math.imul(a ^ a >>> 15, 1 | a); t = t + Math.imul(t ^ t >>> 7, 61 | t) ^ t; return ((t ^ t >>> 14) >>> 0) / 4294967296; }; }

    // Point-in-polygon (ray casting) untuk Polygon & MultiPolygon GeoJSON
    function inRing(x, y, ring) {
        var inside = false;
        for (var i = 0, j = ring.length - 1; i < ring.length; j = i++) {
            var xi = ring[i][0], yi = ring[i][1], xj = ring[j][0], yj = ring[j][1];
            if (((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi)) inside = !inside;
        }
        return inside;
    }
    function inPolygon(x, y, poly) {
        if (!inRing(x, y, poly[0])) return false;
        for (var k = 1; k < poly.length; k++) if (inRing(x, y, poly[k])) return false;
        return true;
    }
    function inFeature(lng, lat, geom) {
        if (geom.type === 'Polygon') return inPolygon(lng, lat, geom.coordinates);
        if (geom.type === 'MultiPolygon') {
            for (var i = 0; i < geom.coordinates.length; i++) if (inPolygon(lng, lat, geom.coordinates[i])) return true;
        }
        return false;
    }
    function pointsInFeature(feature, n, rnd) {
        var b = L.geoJSON(feature).getBounds();
        var s = b.getSouth(), no = b.getNorth(), w = b.getWest(), e = b.getEast();
        var out = [], tries = 0, max = n * 400;
        while (out.length < n && tries < max) {
            tries++;
            var lat = s + rnd() * (no - s);
            var lng = w + rnd() * (e - w);
            if (inFeature(lng, lat, feature.geometry)) out.push([lat, lng]);
        }
        return out;
    }

    // Inisialisasi dua peta
    var hm = document.getElementById('map-heat') ? L.map('map-heat', { scrollWheelZoom: false }).setView(center, zoom) : null;
    var tm = document.getElementById('map-titik') ? L.map('map-titik', { scrollWheelZoom: false }).setView(center, zoom) : null;
    if (hm) baseTile().addTo(hm);
    if (tm) baseTile().addTo(tm);

    var boundaryStyle = { color: '#ffbf00', weight: 1.6, fillColor: '#ffbf00', fillOpacity: 0.06, dashArray: '4' };

    fetch('{{ asset("assets/geojson/kecamatan.geojson") }}')
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var feats = data.features.filter(function (f) {
                return namesJakbar.includes((f.properties.name || '').toUpperCase());
            });
            var jakbar = { type: 'FeatureCollection', features: feats };

            // Gambar batas Jakarta Barat di kedua peta
            if (hm) { var gh = L.geoJSON(jakbar, { style: function () { return boundaryStyle; } }).addTo(hm); if (gh.getBounds().isValid()) hm.fitBounds(gh.getBounds(), { padding: [15, 15] }); }
            if (tm) {
                var gt = L.geoJSON(jakbar, {
                    style: function () { return boundaryStyle; },
                    onEachFeature: function (f, layer) { layer.bindTooltip(f.properties.name || '', { sticky: true, direction: 'top' }); }
                }).addTo(tm);
                if (gt.getBounds().isValid()) tm.fitBounds(gt.getBounds(), { padding: [15, 15] });
            }

            // Generate titik di dalam tiap kecamatan sesuai jumlah dari DB
            var heatPts = [], allPts = [];
            feats.forEach(function (f) {
                var nm = (f.properties.name || '').toUpperCase();
                var cfg = counts[nm]; if (!cfg) return;
                var rnd = mulberry32(strSeed(nm));
                pointsInFeature(f, cfg.wifi, rnd).forEach(function (ll) {
                    heatPts.push([ll[0], ll[1], 0.6]);
                    allPts.push({ lat: ll[0], lng: ll[1], jenis: 'jakwifi', tipe: null, kec: cfg.nama });
                });
                pointsInFeature(f, cfg.cctv, rnd).forEach(function (ll) {
                    heatPts.push([ll[0], ll[1], 0.6]);
                    allPts.push({ lat: ll[0], lng: ll[1], jenis: 'cctv', tipe: tipeCctv[Math.floor(rnd() * 3)], kec: cfg.nama });
                });
            });

            if (hm) L.heatLayer(heatPts, {
                radius: 24, blur: 20, maxZoom: 14,
                gradient: { 0.2: '#2e7d32', 0.5: '#ffbf00', 0.8: '#ff8f00', 1.0: '#e53935' }
            }).addTo(hm);

            if (tm) allPts.forEach(function (p) {
                L.circleMarker([p.lat, p.lng], {
                    radius: 5, color: '#fff', weight: 1,
                    fillColor: warna[p.jenis] || '#999', fillOpacity: 0.85
                }).addTo(tm).bindPopup(
                    '<b>' + (p.jenis === 'cctv' ? 'CCTV' : 'JakWiFi') + '</b><br>' + p.kec +
                    (p.tipe ? ('<br>Tiang: ' + p.tipe) : '')
                );
            });
        });
})();
</script>
<script>
    // Dropdown tahun
    (function () {
        var btn  = document.getElementById('dropdownTahunBtn');
        var menu = document.getElementById('dropdownTahunMenu');
        if (!btn) return;
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
<script>
(function () {
    var fmt = function (v) { return Number(v).toLocaleString('id-ID'); };

    // ── Chart distribusi (JakWiFi vs CCTV per kecamatan) ──────────
    var distNama = {!! json_encode($distribusi->pluck('nama')->values()) !!};
    var distWifi = {!! json_encode($distribusi->pluck('wifi')->map(fn($v) => (int) $v)->values()) !!};
    var distCctv = {!! json_encode($distribusi->pluck('cctv')->map(fn($v) => (int) $v)->values()) !!};

    if (document.querySelector('#chart-distribusi') && distNama.length) {
        new ApexCharts(document.querySelector('#chart-distribusi'), {
            chart: { type: 'bar', height: 340, toolbar: { show: false }, fontFamily: 'inherit',
                     animations: { enabled: true, speed: 500 } },
            series: [
                { name: 'JakWiFi', data: distWifi },
                { name: 'CCTV',    data: distCctv },
            ],
            colors: ['#ffbf00', '#5B82C0'],   // JakWiFi kuning, CCTV biru
            plotOptions: { bar: { horizontal: true, borderRadius: 3, barHeight: '70%', columnWidth: '60%' } },
            dataLabels: { enabled: false },
            xaxis: {
                categories: distNama,
                labels: { style: { fontSize: '11px', colors: '#888' }, formatter: fmt },
                axisBorder: { show: false }, axisTicks: { show: false },
            },
            yaxis: { labels: { style: { fontSize: '11px', colors: '#666' } } },
            legend: { show: false },
            grid: { borderColor: '#f0f0f0', strokeDashArray: 3, xaxis: { lines: { show: true } }, yaxis: { lines: { show: false } } },
            tooltip: { theme: 'light', y: { formatter: fmt } },
        }).render();
    }

    // ── Tabel: filter jenis, pagination, export CSV ───────────────
    var pageSize    = 8;
    var currentPage = 1;
    var filterType  = 'ALL';
    var rows = Array.prototype.slice.call(document.querySelectorAll('#unit-tbody tr.unit-row'));

    function filtered() {
        return rows.filter(function (r) {
            return filterType === 'ALL' || r.dataset.type === filterType;
        });
    }

    function render() {
        var fr = filtered();
        var totalPages = Math.max(1, Math.ceil(fr.length / pageSize));
        if (currentPage > totalPages) currentPage = totalPages;

        rows.forEach(function (r) { r.style.display = 'none'; });
        var start = (currentPage - 1) * pageSize;
        fr.slice(start, start + pageSize).forEach(function (r) { r.style.display = ''; });

        var info = document.getElementById('pagerInfo');
        info.textContent = fr.length
            ? 'Menampilkan ' + (start + 1) + '–' + Math.min(start + pageSize, fr.length) + ' dari ' + fr.length + ' unit'
            : 'Tidak ada data';

        buildPager(totalPages);
    }

    function buildPager(totalPages) {
        var box = document.getElementById('pagerBtns');
        box.innerHTML = '';
        var mk = function (label, page, opts) {
            opts = opts || {};
            var b = document.createElement('button');
            b.className = 'page-btn' + (opts.active ? ' active' : '');
            b.innerHTML = label;
            if (opts.disabled) b.disabled = true;
            else b.addEventListener('click', function () { currentPage = page; render(); });
            box.appendChild(b);
        };
        mk('&laquo;', currentPage - 1, { disabled: currentPage === 1 });
        for (var p = 1; p <= totalPages; p++) mk(p, p, { active: p === currentPage });
        mk('&raquo;', currentPage + 1, { disabled: currentPage === totalPages });
    }

    document.getElementById('filterType').addEventListener('change', function () {
        filterType = this.value; currentPage = 1; render();
    });

    document.getElementById('exportCsv').addEventListener('click', function () {
        var header = ['Kecamatan', 'Jenis', 'Total Unit', 'Aktif', 'Offline'];
        var lines = [header.join(',')];
        filtered().forEach(function (r) {
            var c = r.querySelectorAll('td');
            var total = parseInt(c[2].textContent.replace(/\D/g, ''), 10) || 0;
            var aktif = parseInt(c[3].textContent.replace(/\D/g, ''), 10) || 0;
            lines.push([
                '"' + c[0].textContent.trim() + '"',
                r.dataset.type,
                total, aktif, Math.max(0, total - aktif),
            ].join(','));
        });
        // Export ini sengaja tidak memakai statistik.partials.unduh-tabel:
        // ia menghitung kolom turunan (Offline = total - aktif) dan hanya
        // mengambil baris yang lolos filter pencarian, dua hal yang tidak bisa
        // disimpulkan dari tabel apa adanya.
        //
        // BOM agar Excel membaca berkas sebagai UTF-8, sama seperti modul lain.
        var blob = new Blob(['﻿' + lines.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'rincian-infrastruktur-digital-{{ $tahun }}.csv';
        a.click();
        URL.revokeObjectURL(a.href);
    });

    render();
})();
</script>
@endpush
