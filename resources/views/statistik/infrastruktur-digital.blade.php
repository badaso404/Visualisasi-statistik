@extends('landing-page.layout.app')

@push('styles')
<style>
    /* ── Layout ─────────────────────────────────────────────── */
    .kes-wrapper  { display:flex; gap:24px; padding:40px 0; }
    .kes-sidebar  { width:220px; flex-shrink:0; }
    .kes-content  { flex:1; min-width:0; }

    /* ── Sidebar ────────────────────────────────────────────── */
    .kes-sidebar .nav-link {
        display:flex; align-items:center; gap:10px;
        padding:12px 16px; border-radius:8px; color:#555;
        font-weight:500; margin-bottom:4px; transition:all .2s;
        text-decoration:none;
    }
    .kes-sidebar .nav-link:hover  { background:#f0f0f0; color:#ffbf00; }
    .kes-sidebar .nav-link.active { background:#ffbf00; color:#fff; }
    .kes-sidebar .nav-link i      { width:18px; text-align:center; }

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
    .stat-grid {
        display:grid; grid-template-columns:repeat(4,1fr);
        gap:16px; margin-bottom:16px;
    }
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
        background:#ffbf00; color:#fff;
    }
    .sc-icon.blue { background:#5B82C0; }
    .sc-label { font-size:12px; font-weight:600; color:#888; letter-spacing:1px; text-transform:uppercase; margin-bottom:4px; }
    .sc-value { font-size:28px; font-weight:700; color:#333; line-height:1.15; margin-bottom:6px; }
    .sc-desc  { font-size:11px; color:#aaa; }

    /* ── Mid grid ───────────────────────────────────────────── */
    .mid-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }

    /* ── Panel card ─────────────────────────────────────────── */
    .panel-card { background:#fff; border:1px solid #ebebeb; border-radius:12px; padding:22px; }
    .pc-header  { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:4px; }
    .pc-title    { font-size:15px; font-weight:700; color:#1a1a1a; margin:0 0 2px; }
    .pc-subtitle { font-size:11px; color:#aaa; margin:0 0 18px; }
    .pc-badge    { font-size:10px; font-weight:600; padding:3px 9px; border-radius:20px; background:#e3f2fd; color:#1565c0; }

    /* ── Table card ──────────────────────────────────────────── */
    .table-card   { background:#fff; border:1px solid #ebebeb; border-radius:12px; padding:22px; margin-bottom:16px; }
    .table-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
    .table-title  { font-size:15px; font-weight:700; color:#1a1a1a; margin:0; }
    .table-sub    { font-size:11px; color:#aaa; margin:2px 0 0; }

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
    .td-zero { color:#ccc; }

    /* ── Footer ──────────────────────────────────────────────── */
    .kes-footer { font-size:11px; color:#bbb; text-align:right; margin-top:8px; }

    /* ── Responsive ─────────────────────────────────────────── */
    @media (max-width: 992px) {
        .stat-grid { grid-template-columns: repeat(2,1fr); }
        .mid-grid  { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .kes-wrapper { flex-direction: column; padding: 20px 0; gap: 16px; }
        .kes-sidebar { width: 100%; }
        .kes-sidebar .nav {
            flex-direction: row !important; flex-wrap: nowrap;
            overflow-x: auto; gap: 6px; padding-bottom: 4px; -webkit-overflow-scrolling: touch;
        }
        .kes-sidebar .nav-link { white-space: nowrap; margin-bottom: 0; }
        .stat-header { font-size: 15px; padding: 12px; }
        .table-card  { overflow-x: auto; }
        .kes-table   { min-width: 560px; }
    }
    @media (max-width: 520px) {
        .stat-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
@php
    $tahun = $tahun ?? date('Y');
    $topWifi = $jakWifi->sortByDesc('jumlah_titik')->first();
    $topCctv = $cctv->sortByDesc('jumlah_unit')->first();
@endphp

<div class="container-fluid px-4">
    <div class="kes-wrapper">

        {{-- ── SIDEBAR ─────────────────────────────────── --}}
        <div class="kes-sidebar">
            <nav class="nav flex-column">
                <a class="nav-link" href="{{ route('statistik.geografis') }}">
                    <i class="fa fa-map"></i> Geografis
                </a>
                <a class="nav-link" href="{{ route('statistik.iklim') }}">
                    <i class="fa fa-cloud"></i> Iklim
                </a>
                <a class="nav-link" href="{{ route('statistik.kependudukan') }}">
                    <i class="fa fa-users"></i> Kependudukan
                </a>
                <a class="nav-link" href="{{ route('statistik.pendidikan') }}">
                    <i class="fa fa-graduation-cap"></i> Pendidikan
                </a>
                <a class="nav-link" href="{{ route('statistik.kesehatan') }}">
                    <i class="fa fa-plus-circle"></i> Kesehatan
                </a>
                <a class="nav-link" href="{{ route('statistik.bencana') }}">
                    <i class="fa fa-house-flood-water"></i> Kebencanaan
                </a>
                <a class="nav-link active" href="{{ route('statistik.infrastruktur-digital') }}">
                    <i class="fa fa-wifi"></i> Infrastruktur Digital
                </a>
            </nav>
        </div>

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
                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">Total Titik JakWiFi</div>
                            <div class="sc-value">{{ number_format($ringkasan['total_titik_wifi']) }}</div>
                            <div class="sc-desc">Aktif: {{ number_format($ringkasan['wifi_aktif']) }} titik</div>
                        </div>
                        <div class="sc-icon"><i class="fa fa-wifi"></i></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">Pengguna JakWiFi</div>
                            <div class="sc-value">{{ number_format($ringkasan['total_pengguna']) }}</div>
                            <div class="sc-desc">Terbanyak: {{ $topWifi?->kecamatan->nama_kecamatan ?? '-' }}</div>
                        </div>
                        <div class="sc-icon"><i class="fa fa-users"></i></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">Total Unit CCTV</div>
                            <div class="sc-value">{{ number_format($ringkasan['total_cctv']) }}</div>
                            <div class="sc-desc">Aktif: {{ number_format($ringkasan['cctv_aktif']) }} unit</div>
                        </div>
                        <div class="sc-icon"><i class="fa fa-video"></i></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">CCTV Terintegrasi</div>
                            <div class="sc-value">{{ number_format($ringkasan['cctv_terintegrasi']) }}</div>
                            <div class="sc-desc">Terhubung pusat kendali</div>
                        </div>
                        <div class="sc-icon"><i class="fa fa-network-wired"></i></div>
                    </div>
                </div>
            </div>

            {{-- ── JakWiFi | CCTV chart ──────────────────── --}}
            <div class="mid-grid">
                <div class="panel-card">
                    <div class="pc-header">
                        <div>
                            <div class="pc-title">Titik JakWiFi per Kecamatan</div>
                            <div class="pc-subtitle">Distribusi titik akses internet publik — {{ $tahun }}</div>
                        </div>
                        <span class="pc-badge">Data {{ $tahun }}</span>
                    </div>
                    <div id="chart-wifi" style="min-height:280px;"></div>
                </div>

                <div class="panel-card">
                    <div class="pc-header">
                        <div>
                            <div class="pc-title">Unit CCTV per Kecamatan</div>
                            <div class="pc-subtitle">Distribusi kamera pengawas — {{ $tahun }}</div>
                        </div>
                        <span class="pc-badge">Data {{ $tahun }}</span>
                    </div>
                    <div id="chart-cctv" style="min-height:280px;"></div>
                </div>
            </div>

            {{-- ── Tabel JakWiFi ───────────────────────── --}}
            <div class="table-card">
                <div class="table-header">
                    <div>
                        <p class="table-title">JakWiFi per Kecamatan</p>
                        <p class="table-sub">Rincian titik akses internet publik tahun {{ $tahun }}</p>
                    </div>
                </div>
                <table class="kes-table">
                    <thead>
                        <tr>
                            <th>Kecamatan</th>
                            <th>Total Titik</th>
                            <th>Titik Aktif</th>
                            <th>Pengguna</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jakWifi->sortByDesc('jumlah_titik') as $w)
                        <tr>
                            <td><strong>{{ $w->kecamatan->nama_kecamatan ?? '-' }}</strong></td>
                            <td class="{{ $w->jumlah_titik ? 'td-num' : 'td-zero' }}">{{ $w->jumlah_titik ? number_format($w->jumlah_titik) : '-' }}</td>
                            <td class="{{ $w->titik_aktif ? 'td-num' : 'td-zero' }}">{{ $w->titik_aktif ? number_format($w->titik_aktif) : '-' }}</td>
                            <td class="{{ $w->jumlah_pengguna ? 'td-num' : 'td-zero' }}">{{ $w->jumlah_pengguna ? number_format($w->jumlah_pengguna) : '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center" style="color:#bbb;padding:20px;">Belum ada data untuk tahun {{ $tahun }}.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── Tabel CCTV ──────────────────────────── --}}
            <div class="table-card">
                <div class="table-header">
                    <div>
                        <p class="table-title">CCTV per Kecamatan</p>
                        <p class="table-sub">Rincian kamera pengawas tahun {{ $tahun }}</p>
                    </div>
                </div>
                <table class="kes-table">
                    <thead>
                        <tr>
                            <th>Kecamatan</th>
                            <th>Total Unit</th>
                            <th>Unit Aktif</th>
                            <th>Terintegrasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cctv->sortByDesc('jumlah_unit') as $c)
                        <tr>
                            <td><strong>{{ $c->kecamatan->nama_kecamatan ?? '-' }}</strong></td>
                            <td class="{{ $c->jumlah_unit ? 'td-num' : 'td-zero' }}">{{ $c->jumlah_unit ? number_format($c->jumlah_unit) : '-' }}</td>
                            <td class="{{ $c->unit_aktif ? 'td-num' : 'td-zero' }}">{{ $c->unit_aktif ? number_format($c->unit_aktif) : '-' }}</td>
                            <td class="{{ $c->terintegrasi ? 'td-num' : 'td-zero' }}">{{ $c->terintegrasi ? number_format($c->terintegrasi) : '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center" style="color:#bbb;padding:20px;">Belum ada data untuk tahun {{ $tahun }}.</td></tr>
                        @endforelse
                    </tbody>
                </table>
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

    function makeBar(sel, kecArr, dataArr, label, color) {
        if (!document.querySelector(sel)) return;
        new ApexCharts(document.querySelector(sel), {
            chart: { type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'inherit',
                     animations: { enabled: true, speed: 500 } },
            series: [{ name: label, data: dataArr }],
            xaxis: {
                categories: kecArr,
                labels: { style: { fontSize: '11px', colors: '#888' }, formatter: fmt },
                axisBorder: { show: false }, axisTicks: { show: false },
            },
            yaxis: { labels: { style: { fontSize: '11px', colors: '#aaa' } } },
            plotOptions: { bar: { horizontal: true, borderRadius: 3, barHeight: '60%' } },
            dataLabels: { enabled: true, style: { fontSize: '11px', fontWeight: '700', colors: ['#fff'] }, formatter: fmt },
            colors: [color], fill: { colors: [color] },
            legend: { show: false },
            grid: { borderColor: '#f0f0f0', strokeDashArray: 3, xaxis: { lines: { show: true } }, yaxis: { lines: { show: false } } },
            tooltip: { theme: 'light', y: { formatter: fmt } },
        }).render();
    }

    var wifiKec  = {!! json_encode($jakWifi->sortByDesc('jumlah_titik')->pluck('kecamatan.nama_kecamatan')->values()) !!};
    var wifiData = {!! json_encode($jakWifi->sortByDesc('jumlah_titik')->pluck('jumlah_titik')->map(fn($v) => (int) $v)->values()) !!};
    makeBar('#chart-wifi', wifiKec, wifiData, 'Titik JakWiFi', '#ffbf00');

    var cctvKec  = {!! json_encode($cctv->sortByDesc('jumlah_unit')->pluck('kecamatan.nama_kecamatan')->values()) !!};
    var cctvData = {!! json_encode($cctv->sortByDesc('jumlah_unit')->pluck('jumlah_unit')->map(fn($v) => (int) $v)->values()) !!};
    makeBar('#chart-cctv', cctvKec, cctvData, 'Unit CCTV', '#5B82C0');
})();
</script>
@endpush
