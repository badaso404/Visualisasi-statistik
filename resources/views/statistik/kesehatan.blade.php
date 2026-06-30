@extends('landing-page.layout.app')

@push('styles')
<style>
    /* ── Layout ─────────────────────────────────────────────── */
    .kes-wrapper  { display:flex; gap:24px; padding:32px 0; }
    .kes-sidebar  { width:220px; flex-shrink:0; }
    .kes-content  { flex:1; min-width:0; }

    /* ── Sidebar ────────────────────────────────────────────── */
    .kes-sidebar .nav-link {
        display:flex; align-items:center; gap:10px;
        padding:11px 16px; border-radius:8px; color:#555;
        font-weight:500; margin-bottom:4px; transition:all .2s;
        text-decoration:none; font-size:14px;
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
</style>
@endpush

@section('content')
@php
    $tahun          = $summary->tahun ?? 2024;
    $totalTenaga    = $tenaga->sum('jumlah_total');
    $totalFasilitas = $fasilitas->sum('jumlah_total');
    $maxTenaga      = $tenaga->max('jumlah_total') ?: 1;
    $maxFasilitas   = $fasilitas->max('jumlah_total') ?: 1;
    $topTenaga      = $tenaga->sortByDesc('jumlah_total')->first();
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
                    <i class="fa fa-cloud-sun"></i> Iklim
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
            </div>

            {{-- ── 4 Stat Cards ────────────────────────── --}}
            <div class="stat-grid">
                {{-- Tempat Tidur RS --}}
                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">Tempat Tidur Rumah Sakit</div>
                            <div class="sc-value">{{ number_format($summary->jumlah_tempat_tidur_rs) }}</div>
                            <div class="sc-desc">Total ketersediaan TT di RS</div>
                        </div>
                        <div class="sc-icon yellow"><i class="fa fa-bed-pulse"></i></div>
                    </div>
                </div>
                {{-- Imunisasi --}}
                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">Cakupan Imunisasi Dasar</div>
                            <div class="sc-value">{{ $summary->cakupan_imunisasi_dasar }}%</div>
                            <div class="sc-desc">Pencapaian target IDL Kelurahan</div>
                        </div>
                        <div class="sc-icon green"><i class="fa fa-syringe"></i></div>
                    </div>
                </div>
                {{-- Tenaga Kesehatan --}}
                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">Total Tenaga Kesehatan</div>
                            <div class="sc-value">{{ number_format($totalTenaga) }}</div>
                            <div class="sc-desc">Terbanyak: {{ $topTenaga?->kecamatan->nama_kecamatan ?? '-' }}</div>
                        </div>
                        <div class="sc-icon blue"><i class="fa fa-stethoscope"></i></div>
                    </div>
                </div>
                {{-- Fasilitas --}}
                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">Total Fasilitas Kesehatan</div>
                            <div class="sc-value">{{ number_format($totalFasilitas) }}</div>
                            <div class="sc-desc">RS, Puskesmas, Klinik &amp; Posyandu</div>
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
                            <div class="pc-subtitle">Distribusi personel medis aktif — {{ $tahun }}</div>
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
                            <div class="pc-subtitle">Jumlah unit fasilitas — {{ $tahun }}</div>
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
                        <span><span class="leg-dot" style="background:#a8d5b5;"></span>Fasilitas</span>
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
(function () {
    var barColors = ['#00bcd4','#e91e8c','#ff9800','#4caf50','#cddc39','#9c27b0','#f44336','#26c6da','#ff5722','#2196f3'];

    // ── Chart Tenaga (horizontal, warna per bar) ──────────────
    var tenagaKec  = {!! json_encode($tenaga->sortByDesc('jumlah_total')->pluck('kecamatan.nama_kecamatan')->values()) !!};
    var tenagaData = {!! json_encode($tenaga->sortByDesc('jumlah_total')->pluck('jumlah_total')->map(fn($v) => (int)$v)->values()) !!};

    new ApexCharts(document.querySelector("#chart-tenaga"), {
        chart: { type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'inherit' },
        series: [{ name: 'Tenaga Kesehatan', data: tenagaData }],
        xaxis: {
            categories: tenagaKec,
            labels: { style: { fontSize: '11px', colors: '#888' }, formatter: v => v.toLocaleString('id-ID') },
            axisBorder: { show: false }, axisTicks: { show: false },
        },
        yaxis: { labels: { style: { fontSize: '11px', colors: '#aaa' } } },
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 3,
                barHeight: '60%',
                distributed: true,
                dataLabels: { position: 'center' },
            }
        },
        dataLabels: {
            enabled: true,
            style: { fontSize: '11px', fontWeight: '700', colors: ['#fff'] },
            formatter: v => v.toLocaleString('id-ID'),
        },
        colors: barColors,
        legend: { show: false },
        grid: { borderColor: '#f0f0f0', strokeDashArray: 3, xaxis: { lines: { show: true } }, yaxis: { lines: { show: false } } },
        tooltip: { theme: 'light', y: { formatter: v => v.toLocaleString('id-ID') } },
    }).render();

    // ── Chart Fasilitas (horizontal, warna per bar) ───────────
    var fasKec  = {!! json_encode($fasilitas->sortByDesc('jumlah_total')->pluck('kecamatan.nama_kecamatan')->values()) !!};
    var fasData = {!! json_encode($fasilitas->sortByDesc('jumlah_total')->pluck('jumlah_total')->map(fn($v) => (int)$v)->values()) !!};

    new ApexCharts(document.querySelector("#chart-fasilitas"), {
        chart: { type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'inherit' },
        series: [{ name: 'Fasilitas Kesehatan', data: fasData }],
        xaxis: {
            categories: fasKec,
            labels: { style: { fontSize: '11px', colors: '#888' }, formatter: v => v.toLocaleString('id-ID') },
            axisBorder: { show: false }, axisTicks: { show: false },
        },
        yaxis: { labels: { style: { fontSize: '11px', colors: '#aaa' } } },
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 3,
                barHeight: '60%',
                distributed: true,
                dataLabels: { position: 'center' },
            }
        },
        dataLabels: {
            enabled: true,
            style: { fontSize: '11px', fontWeight: '700', colors: ['#fff'] },
            formatter: v => v.toLocaleString('id-ID'),
        },
        colors: barColors,
        legend: { show: false },
        grid: { borderColor: '#f0f0f0', strokeDashArray: 3, xaxis: { lines: { show: true } }, yaxis: { lines: { show: false } } },
        tooltip: { theme: 'light', y: { formatter: v => v.toLocaleString('id-ID') } },
    }).render();

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
        colors: ['#ffbf00', '#a8d5b5'],
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
