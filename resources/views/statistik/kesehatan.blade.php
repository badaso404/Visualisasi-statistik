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

    /* Dropdown tahun (selaras dengan modul lain) */
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
        background:#fff; border:1px solid #ebebeb; border-radius:12px;
        padding:20px 22px 18px; position:relative; overflow:hidden;
        transition:box-shadow .2s;
    }
    .stat-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.07); }
    .sc-badge {
        font-size:11px; font-weight:600; padding:3px 9px; border-radius:20px;
        position:absolute; top:16px; right:16px;
        display:flex; align-items:center; gap:4px;
    }
    .sc-badge.up   { background:#e8f5e9; color:#2e7d32; }
    .sc-badge.info { background:#e3f2fd; color:#1565c0; }
    .sc-badge.ok   { background:#e8f5e9; color:#2e7d32; }
    .sc-card-body  { display:flex; justify-content:space-between; align-items:flex-start; margin-top:8px; }
    .sc-card-left  { flex:1; }
    .sc-icon {
        width:48px; height:48px; border-radius:12px;
        display:flex; align-items:center; justify-content:center;
        font-size:22px; flex-shrink:0; margin-left:12px;
    }
    .sc-icon.yellow,
    .sc-icon.green,
    .sc-icon.blue,
    .sc-icon.teal { background:#ffbf00; color:#fff; }
    .sc-label { font-size:10px; font-weight:700; color:#9e9e9e; letter-spacing:.8px; text-transform:uppercase; margin-bottom:4px; }
    .sc-value { font-size:28px; font-weight:800; color:#1a1a1a; line-height:1; margin-bottom:6px; }
    .sc-desc  { font-size:11px; color:#aaa; }

    /* ── Mid grid ───────────────────────────────────────────── */
    .mid-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }

    /* ── Panel card ─────────────────────────────────────────── */
    .panel-card { background:#fff; border:1px solid #ebebeb; border-radius:12px; padding:22px; }
    .pc-header  { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:4px; }
    .pc-title    { font-size:15px; font-weight:700; color:#1a1a1a; margin:0 0 2px; }
    .pc-subtitle { font-size:11px; color:#aaa; margin:0 0 18px; }
    .pc-badge    { font-size:10px; font-weight:600; padding:3px 9px; border-radius:20px; background:#e3f2fd; color:#1565c0; }

    /* ── Horizontal bar – tenaga ─────────────────────────────── */
    .hbar-row   { margin-bottom:14px; }
    .hbar-row:last-child { margin-bottom:0; }
    .hbar-label { display:flex; justify-content:space-between; font-size:13px; margin-bottom:5px; }
    .hb-name  { color:#333; font-weight:500; }
    .hb-value { color:#333; font-weight:700; }
    .hbar-track { height:8px; background:#f0f0f0; border-radius:6px; overflow:hidden; }
    .hbar-fill  { height:100%; border-radius:6px; background:#ffbf00; transition:width .6s ease; }

    /* ── Horizontal bar – fasilitas ──────────────────────────── */
    .fas-row  { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
    .fas-row:last-child { margin-bottom:0; }
    .fas-kec  { font-size:12px; color:#555; font-weight:500; width:110px; flex-shrink:0; }
    .fas-track { flex:1; height:9px; background:#f0f0f0; border-radius:6px; overflow:hidden; }
    .fas-fill  { height:100%; border-radius:6px; background:linear-gradient(90deg,#1a6b3c,#2e9c59); }
    .fas-unit  { font-size:12px; color:#333; font-weight:700; white-space:nowrap; margin-left:4px; }

    /* ── Detail chart card ───────────────────────────────────── */
    .detail-card   { background:#fff; border:1px solid #ebebeb; border-radius:12px; padding:22px; margin-bottom:16px; }
    .detail-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:4px; }
    .detail-title  { font-size:15px; font-weight:700; color:#1a1a1a; }
    .detail-sub    { font-size:11px; color:#aaa; margin-bottom:16px; }
    .detail-legend { display:flex; gap:16px; font-size:11px; color:#555; }
    .leg-dot { width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:5px; vertical-align:middle; }

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

    /* ── Responsive (tablet & HP) ──────────────────────────────── */
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
    $tahun          = $tahun ?? $summary->tahun ?? 2024;
    $totalTenaga    = $tenaga->sum('jumlah_total');
    $totalFasilitas = $fasilitas->sum('jumlah_total');
    $maxTenaga      = $tenaga->max('jumlah_total') ?: 1;
    $maxFasilitas   = $fasilitas->max('jumlah_total') ?: 1;
    $topTenaga      = $tenaga->sortByDesc('jumlah_total')->first();

    // Data per kecamatan untuk relasi card ↔ chart
    $kesStatsData = [];
    foreach ($tenaga as $t) {
        $namaU = strtoupper($t->kecamatan->nama_kecamatan);
        $kesStatsData[$namaU]['nama']   = $t->kecamatan->nama_kecamatan;
        $kesStatsData[$namaU]['tenaga'] = (int) $t->jumlah_total;
    }
    foreach ($fasilitas as $f) {
        $namaU = strtoupper($f->kecamatan->nama_kecamatan);
        $kesStatsData[$namaU]['nama']      = $f->kecamatan->nama_kecamatan;
        $kesStatsData[$namaU]['fasilitas'] = (int) $f->jumlah_total;
        $kesStatsData[$namaU]['rs']        = (int) $f->rumah_sakit;
        $kesStatsData[$namaU]['puskesmas'] = (int) $f->puskesmas;
        $kesStatsData[$namaU]['klinik']    = (int) $f->klinik_kesehatan;
        $kesStatsData[$namaU]['posyandu']  = (int) $f->posyandu;
    }
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
                <a class="nav-link active" href="{{ route('statistik.kesehatan') }}">
                    <i class="fa fa-plus-circle"></i> Kesehatan
                </a>
                <a class="nav-link" href="{{ route('statistik.bencana') }}">
                    <i class="fa fa-house-flood-water"></i> Monitor Bencana
                </a>
            </nav>
        </div>

        {{-- ── KONTEN ───────────────────────────────────── --}}
        <div class="kes-content">

            {{-- Header --}}
            <div class="stat-header-wrap">
                <div class="stat-header">KESEHATAN JAKARTA BARAT {{ $tahun }}</div>
                <div class="dropdown-tahun">
                    <div class="dropdown-tahun-btn" id="dropdownTahunBtn">
                        <i class="fa fa-calendar"></i>
                        {{ $tahun }}
                        <span class="arrow">&#9660;</span>
                    </div>
                    <div class="dropdown-tahun-menu" id="dropdownTahunMenu">
                        @foreach($availableTahun as $t)
                        <a href="{{ route('statistik.kesehatan', ['tahun' => $t]) }}"
                           class="{{ (int) $t === (int) $tahun ? 'active' : '' }}">{{ $t }}</a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ── 4 Stat Cards ────────────────────────── --}}
            <div class="stat-grid">
                {{-- Tempat Tidur RS --}}
                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label" id="kc-lbl-1">Tempat Tidur Rumah Sakit</div>
                            <div class="sc-value" id="kc-val-1">{{ number_format($summary->jumlah_tempat_tidur_rs) }}</div>
                            <div class="sc-desc" id="kc-desc-1">Total ketersediaan TT di RS</div>
                        </div>
                        <div class="sc-icon yellow"><i class="fa fa-bed-pulse"></i></div>
                    </div>
                </div>
                {{-- Imunisasi --}}
                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label" id="kc-lbl-2">Cakupan Imunisasi Dasar</div>
                            <div class="sc-value" id="kc-val-2">{{ $summary->cakupan_imunisasi_dasar }}%</div>
                            <div class="sc-desc" id="kc-desc-2">Pencapaian target IDL Kelurahan</div>
                        </div>
                        <div class="sc-icon green"><i class="fa fa-syringe"></i></div>
                    </div>
                </div>
                {{-- Tenaga Kesehatan --}}
                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label" id="kc-lbl-3">Total Tenaga Kesehatan</div>
                            <div class="sc-value" id="kc-val-3">{{ number_format($totalTenaga) }}</div>
                            <div class="sc-desc" id="kc-desc-3">Terbanyak: {{ $topTenaga?->kecamatan->nama_kecamatan ?? '-' }}</div>
                        </div>
                        <div class="sc-icon blue"><i class="fa fa-stethoscope"></i></div>
                    </div>
                </div>
                {{-- Fasilitas --}}
                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label" id="kc-lbl-4">Total Fasilitas Kesehatan</div>
                            <div class="sc-value" id="kc-val-4">{{ number_format($totalFasilitas) }}</div>
                            <div class="sc-desc" id="kc-desc-4">RS, Puskesmas, Klinik &amp; Posyandu</div>
                        </div>
                        <div class="sc-icon teal"><i class="fa fa-hospital"></i></div>
                    </div>
                </div>
            </div>

            {{-- ── Tenaga | Fasilitas ───────────────────── --}}
            <div class="mid-grid">

                {{-- Tenaga per Kecamatan --}}
                <div class="panel-card">
                    <div class="pc-header">
                        <div>
                            <div class="pc-title">Tenaga Kesehatan per Kecamatan</div>
                            <div class="pc-subtitle">Distribusi personel medis aktif — {{ $tahun }} · klik batang untuk rincian</div>
                        </div>
                        <i class="fa fa-ellipsis-vertical" style="color:#ccc;"></i>
                    </div>
                    <div id="chart-tenaga" style="min-height:280px;"></div>
                </div>

                {{-- Fasilitas per Kecamatan --}}
                <div class="panel-card">
                    <div class="pc-header">
                        <div>
                            <div class="pc-title">Fasilitas Kesehatan per Kecamatan</div>
                            <div class="pc-subtitle">Jumlah unit fasilitas — {{ $tahun }} · klik batang untuk rincian</div>
                        </div>
                        <span class="pc-badge">Data {{ $tahun }}</span>
                    </div>
                    <div id="chart-fasilitas" style="min-height:280px;"></div>
                </div>

            </div>

            {{-- ── Grouped Bar Chart ───────────────────── --}}
            <div class="detail-card">
                <div class="detail-header">
                    <div>
                        <div class="detail-title">Perbandingan Tenaga &amp; Fasilitas per Kecamatan</div>
                        <div class="detail-sub">Data tahun {{ $tahun }} — seluruh kecamatan Jakarta Barat</div>
                    </div>
                    <div class="detail-legend">
                        <span><span class="leg-dot" style="background:#ffbf00;"></span>Tenaga Medis</span>
                        <span><span class="leg-dot" style="background:#5B82C0;"></span>Fasilitas</span>
                    </div>
                </div>
                <div id="chart-detail" style="min-height:280px;"></div>
            </div>

            {{-- ── Tabel Fasilitas per Kecamatan ──────── --}}
            <div class="table-card">
                <div class="table-header">
                    <div>
                        <p class="table-title">Fasilitas per Kecamatan</p>
                        <p class="table-sub">Rincian unit fasilitas kesehatan tahun {{ $tahun }}</p>
                    </div>
                </div>
                <table class="kes-table">
                    <thead>
                        <tr>
                            <th>Kecamatan</th>
                            <th>Total</th>
                            <th>Rumah Sakit</th>
                            <th>Puskesmas</th>
                            <th>Klinik Kesehatan</th>
                            <th>Posyandu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fasilitas->sortByDesc('jumlah_total') as $f)
                        <tr>
                            <td><strong>{{ $f->kecamatan->nama_kecamatan }}</strong></td>
                            <td class="td-num">{{ number_format($f->jumlah_total) }}</td>
                            <td class="{{ $f->rumah_sakit ? 'td-num' : 'td-zero' }}">{{ $f->rumah_sakit ?: '-' }}</td>
                            <td class="{{ $f->puskesmas ? 'td-num' : 'td-zero' }}">{{ $f->puskesmas ?: '-' }}</td>
                            <td class="{{ $f->klinik_kesehatan ? 'td-num' : 'td-zero' }}">{{ $f->klinik_kesehatan ?: '-' }}</td>
                            <td class="{{ $f->posyandu ? 'td-num' : 'td-zero' }}">{{ $f->posyandu ?: '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="kes-footer">
                Sumber: {{ $summary->sumber ?? 'Dinas Kesehatan Jakarta Barat' }} &bull; Data Tahun {{ $tahun }}
            </div>

        </div>{{-- /.kes-content --}}
    </div>{{-- /.kes-wrapper --}}
</div>
@endsection

@push('scripts')
<script>
    // Dropdown tahun (selaras dengan modul lain)
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
    var idID = 'id-ID';
    var fmt  = function (v) { return Number(v).toLocaleString(idID); };

    // ── Palet gradien profesional (muda → tua per nilai) ──────────
    var PALETTE = {
        biru: { light: '#E2ECFA', dark: '#34527A' },
        teal: { light: '#E1F0EE', dark: '#2C6E63' },
    };
    function lerpColor(a, b, t) {
        var ah = parseInt(a.slice(1), 16), bh = parseInt(b.slice(1), 16);
        var ar = ah >> 16, ag = (ah >> 8) & 0xff, ab = ah & 0xff;
        var br = bh >> 16, bg = (bh >> 8) & 0xff, bb = bh & 0xff;
        var rr = Math.round(ar + (br - ar) * t);
        var rg = Math.round(ag + (bg - ag) * t);
        var rb = Math.round(ab + (bb - ab) * t);
        return '#' + ((1 << 24) + (rr << 16) + (rg << 8) + rb).toString(16).slice(1);
    }
    function gradientColors(data, hue) {
        var pal = PALETTE[hue] || PALETTE.biru;
        var min = Math.min.apply(null, data), max = Math.max.apply(null, data);
        return data.map(function (v) {
            var t = max > min ? (v - min) / (max - min) : 0.5;
            var step = Math.round(t * 4) / 4;   // 5 tingkatan
            return lerpColor(pal.light, pal.dark, step);
        });
    }
    // Label putih untuk batang gelap, abu gelap untuk batang terang
    function labelColor(hex) {
        var c = parseInt(hex.slice(1), 16);
        var lum = 0.299 * (c >> 16) + 0.587 * ((c >> 8) & 0xff) + 0.114 * (c & 0xff);
        return lum > 150 ? '#444' : '#fff';
    }

    // ── Relasi card ringkasan ↔ chart per kecamatan ───────────────
    var kesStats = {!! json_encode($kesStatsData) !!};
    function setText(id, t) { var el = document.getElementById(id); if (el) el.textContent = t; }
    var cardDefaults = {
        l1: 'Tempat Tidur Rumah Sakit', v1: '{{ number_format($summary->jumlah_tempat_tidur_rs) }}',          d1: 'Total ketersediaan TT di RS',
        l2: 'Cakupan Imunisasi Dasar',  v2: '{{ $summary->cakupan_imunisasi_dasar }}%',                        d2: 'Pencapaian target IDL Kelurahan',
        l3: 'Total Tenaga Kesehatan',   v3: '{{ number_format($totalTenaga) }}',                               d3: 'Terbanyak: {{ $topTenaga?->kecamatan->nama_kecamatan ?? '-' }}',
        l4: 'Total Fasilitas Kesehatan',v4: '{{ number_format($totalFasilitas) }}',                            d4: 'RS, Puskesmas, Klinik & Posyandu',
    };
    function updateCards(namaUp) {
        var s = kesStats[namaUp];
        if (!s) return;
        var nm = (s.nama || namaUp);
        setText('kc-lbl-1', 'Rumah Sakit');       setText('kc-val-1', fmt(s.rs || 0));        setText('kc-desc-1', 'Unit RS di ' + nm);
        setText('kc-lbl-2', 'Puskesmas');         setText('kc-val-2', fmt(s.puskesmas || 0)); setText('kc-desc-2', 'Unit puskesmas di ' + nm);
        setText('kc-lbl-3', 'Tenaga Kesehatan');  setText('kc-val-3', fmt(s.tenaga || 0));    setText('kc-desc-3', 'Personel medis di ' + nm);
        setText('kc-lbl-4', 'Total Fasilitas');   setText('kc-val-4', fmt(s.fasilitas || 0)); setText('kc-desc-4', 'Klinik ' + fmt(s.klinik || 0) + ' · Posyandu ' + fmt(s.posyandu || 0));
    }
    function resetCards() {
        setText('kc-lbl-1', cardDefaults.l1); setText('kc-val-1', cardDefaults.v1); setText('kc-desc-1', cardDefaults.d1);
        setText('kc-lbl-2', cardDefaults.l2); setText('kc-val-2', cardDefaults.v2); setText('kc-desc-2', cardDefaults.d2);
        setText('kc-lbl-3', cardDefaults.l3); setText('kc-val-3', cardDefaults.v3); setText('kc-desc-3', cardDefaults.d3);
        setText('kc-lbl-4', cardDefaults.l4); setText('kc-val-4', cardDefaults.v4); setText('kc-desc-4', cardDefaults.d4);
    }

    // ── Pabrik bar horizontal: gradien + klik untuk fokus card ────
    function makeKesBar(sel, kecArr, dataArr, label, hue) {
        var base    = gradientColors(dataArr, hue);
        var lblCols = base.map(labelColor);
        var active  = null;
        var chart = new ApexCharts(document.querySelector(sel), {
            chart: {
                type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'inherit',
                animations: { enabled: true, speed: 500 },
                events: {
                    dataPointSelection: function (e, ctx, cfg) {
                        var i = cfg.dataPointIndex;
                        if (active === i) {
                            active = null;
                            chart.updateOptions({ colors: base, fill: { colors: base }, dataLabels: { style: { colors: lblCols } } });
                            resetCards();
                            return;
                        }
                        active = i;
                        var fc = base.map(function (c, j) { return j === i ? c : c + '38'; });
                        var fl = lblCols.map(function (c, j) { return j === i ? c : c + '38'; });
                        chart.updateOptions({ colors: fc, fill: { colors: fc }, dataLabels: { style: { colors: fl } } });
                        updateCards((kecArr[i] || '').toUpperCase());
                    }
                }
            },
            series: [{ name: label, data: dataArr }],
            xaxis: {
                categories: kecArr,
                labels: { style: { fontSize: '11px', colors: '#888' }, formatter: fmt },
                axisBorder: { show: false }, axisTicks: { show: false },
            },
            yaxis: { labels: { style: { fontSize: '11px', colors: '#aaa' } } },
            plotOptions: { bar: { horizontal: true, borderRadius: 3, barHeight: '60%', distributed: true, dataLabels: { position: 'center' } } },
            dataLabels: { enabled: true, style: { fontSize: '11px', fontWeight: '700', colors: lblCols }, formatter: fmt },
            colors: base, fill: { colors: base },
            legend: { show: false },
            grid: { borderColor: '#f0f0f0', strokeDashArray: 3, xaxis: { lines: { show: true } }, yaxis: { lines: { show: false } } },
            states: { active: { filter: { type: 'none' } } },
            tooltip: { theme: 'light', y: { formatter: fmt } },
        });
        chart.render();
        return chart;
    }

    // ── Chart Tenaga (gradien biru) ───────────────────────────────
    var tenagaKec  = {!! json_encode($tenaga->sortByDesc('jumlah_total')->pluck('kecamatan.nama_kecamatan')->values()) !!};
    var tenagaData = {!! json_encode($tenaga->sortByDesc('jumlah_total')->pluck('jumlah_total')->map(fn($v) => (int)$v)->values()) !!};
    makeKesBar('#chart-tenaga', tenagaKec, tenagaData, 'Tenaga Kesehatan', 'biru');

    // ── Chart Fasilitas (gradien teal) ────────────────────────────
    var fasKec  = {!! json_encode($fasilitas->sortByDesc('jumlah_total')->pluck('kecamatan.nama_kecamatan')->values()) !!};
    var fasData = {!! json_encode($fasilitas->sortByDesc('jumlah_total')->pluck('jumlah_total')->map(fn($v) => (int)$v)->values()) !!};
    makeKesBar('#chart-fasilitas', fasKec, fasData, 'Fasilitas Kesehatan', 'teal');

    // ── Chart Detail Grouped Bar ──────────────────────────────
    var kec          = {!! json_encode($tenaga->sortByDesc('jumlah_total')->pluck('kecamatan.nama_kecamatan')->map(fn($v) => \Illuminate\Support\Str::limit($v, 12))->values()) !!};
    var dataTenaga   = {!! json_encode($tenaga->sortByDesc('jumlah_total')->pluck('jumlah_total')->map(fn($v) => (int)$v)->values()) !!};
    var dataFasilitas = {!! json_encode(
        $tenaga->sortByDesc('jumlah_total')->map(fn($t) =>
            (int)($fasilitas->firstWhere('kecamatan_id', $t->kecamatan_id)?->jumlah_total ?? 0)
        )->values()
    ) !!};

    new ApexCharts(document.querySelector("#chart-detail"), {
        chart: {
            type: 'bar', height: 320,
            toolbar: { show: false },
            fontFamily: 'inherit',
            animations: { enabled: true, speed: 600 },
        },
        series: [
            { name: 'Tenaga Medis', data: dataTenaga },
            { name: 'Fasilitas',    data: dataFasilitas },
        ],
        xaxis: {
            categories: kec,
            labels: { style: { fontSize: '11px', colors: '#aaa' } },
            axisBorder: { show: false },
            axisTicks:  { show: false },
        },
        yaxis: {
            labels: {
                style: { fontSize: '11px', colors: '#ccc' },
                formatter: v => v.toLocaleString('id-ID'),
            },
            tickAmount: 4,
        },
        colors: ['#ffbf00', '#5B82C0'],
        plotOptions: {
            bar: {
                borderRadius: 2,
                columnWidth: '65%',
                dataLabels: { position: 'top' },
            }
        },
        dataLabels: { enabled: false },
        legend: {
            show: true,
            position: 'bottom',
            horizontalAlign: 'center',
            fontSize: '12px',
            markers: { width: 12, height: 12, radius: 2 },
            itemMargin: { horizontal: 16 },
        },
        grid: {
            borderColor: '#f0f0f0',
            strokeDashArray: 3,
            xaxis: { lines: { show: false } },
        },
        tooltip: {
            theme: 'light',
            y: { formatter: v => v.toLocaleString('id-ID') }
        },
    }).render();
})();
</script>
@endpush
