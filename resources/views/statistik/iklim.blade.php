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
    .stat-header-wrap { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
    .stat-header {
        flex: 1; background: #ffbf00; color: white; text-align: center;
        padding: 14px; border-radius: 8px; font-weight: 700;
        font-size: 18px; letter-spacing: 1px;
    }

    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .kpi-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 18px 20px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }
    .kpi-card.kpi-highlight { border-color: #ffbf00; border-width: 1.5px; }
    .kpi-label {
        font-size: 10.5px;
        font-weight: 700;
        color: #888;
        letter-spacing: .8px;
        text-transform: uppercase;
        margin-bottom: 6px;
        line-height: 1.4;
    }
    .kpi-value {
        font-size: 30px;
        font-weight: 800;
        color: #1a1a1a;
        line-height: 1;
    }
    .kpi-value .kpi-unit { font-size: 16px; font-weight: 600; color: #555; margin-left: 2px; }
    .kpi-value-normal { font-size: 24px; font-weight: 800; color: #1a1a1a; }
    .kpi-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .kpi-icon.yellow  { background: #fdf6e0; color: #ffbf00; }
    .kpi-icon.blue    { background: #e8f4fd; color: #2196f3; }
    .kpi-icon.teal    { background: #e0f7f4; color: #00897b; }
    .kpi-icon.info    { background: #fff8e1; color: #f9a825; }
    .kpi-badge-normal {
        display: inline-block;
        background: #fff8e1;
        border: 1px solid #ffd54f;
        color: #b8860b;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        margin-top: 4px;
    }

    .chart-row {
        display: grid;
        grid-template-columns: 1fr 1.7fr;
        gap: 20px;
        margin-bottom: 24px;
    }
    .chart-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 22px 24px;
    }
    .chart-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 16px;
    }
    .chart-card-icon {
        width: 36px;
        height: 36px;
        background: #e8f4fd;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2196f3;
        font-size: 16px;
        margin-bottom: 10px;
    }
    .chart-card-title {
        font-size: 15px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 2px;
    }
    .chart-card-sub {
        font-size: 12px;
        color: #aaa;
    }
    .chart-total-badge {
        background: #fff8e1;
        border: 1px solid #ffd54f;
        color: #b8860b;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        padding: 4px 10px;
    }

    .donut-legend { margin-top: 18px; }
    .donut-legend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
        color: #444;
        margin-bottom: 6px;
    }
    .donut-legend-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        margin-right: 8px;
        display: inline-block;
        flex-shrink: 0;
    }
    .donut-legend-left { display: flex; align-items: center; }
    .donut-legend-pct { font-weight: 700; color: #333; }

    .table-section {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 22px 24px;
    }
    .table-section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 18px;
    }
    .table-section-title { font-size: 16px; font-weight: 700; color: #1a1a1a; }
    .table-section-sub   { font-size: 12px; color: #aaa; margin-top: 2px; }
    .link-lihat-semua    { font-size: 13px; color: #ffbf00; font-weight: 600; text-decoration: none; }
    .link-lihat-semua:hover { text-decoration: underline; }

    .iklim-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .iklim-table thead th {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .8px;
        color: #999;
        text-transform: uppercase;
        border-bottom: 1px solid #f0f0f0;
        padding: 0 0 12px;
    }
    .iklim-table thead th:not(:first-child) { text-align: right; }
    .iklim-table tbody td {
        padding: 13px 0;
        border-bottom: 1px solid #f7f7f7;
        color: #333;
    }
    .iklim-table tbody td:not(:first-child) { text-align: right; }
    .iklim-table tbody tr:last-child td { border-bottom: none; }

    .status-badge {
        display: inline-block;
        border-radius: 6px;
        padding: 3px 10px;
        font-size: 11.5px;
        font-weight: 700;
    }
    .status-normal   { background: #fff8e1; color: #b8860b; border: 1px solid #ffd54f; }
    .status-waspada  { background: #fff3e0; color: #e65100; border: 1px solid #ffb74d; }
    .status-siaga    { background: #fce4ec; color: #c62828; border: 1px solid #ef9a9a; }

    @media (max-width: 1100px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        .chart-row { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .statistik-sidebar { display: none; }
        .statistik-content { padding: 20px 16px 40px; }
        .kpi-grid { grid-template-columns: 1fr 1fr; }
    }
</style>
@endpush

@php
    $bulanLabel = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                   7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];

    $avgSuhu      = $iklim->avg('suhu_udara');
    $totalHujan   = $iklim->sum('hari_hujan');
    $avgKelembaban= $iklim->avg('kelembaban_udara');

    // Determine overall status based on avg rainfall days
    $statusWilayah = $totalHujan > 200 ? 'Waspada' : ($totalHujan > 150 ? 'Siaga' : 'Normal');

    // For bar chart: curah hujan bulanan (using hari_hujan * 10 as proxy mm)
    $curahHujanBulanan = $iklim->pluck('hari_hujan')->map(fn($v) => round((float)$v * 10, 1));
    $totalCurahHujan   = $curahHujanBulanan->sum();

    // Donut: distribute kelembaban into 3 bands
    $sangat_tinggi = $iklim->filter(fn($d) => $d->kelembaban_udara >= 80)->count();
    $tinggi        = $iklim->filter(fn($d) => $d->kelembaban_udara >= 75 && $d->kelembaban_udara < 80)->count();
    $sedang        = $iklim->filter(fn($d) => $d->kelembaban_udara < 75)->count();
    $totalBulan    = $iklim->count() ?: 1;
    $pctST = round($sangat_tinggi / $totalBulan * 100);
    $pctT  = round($tinggi        / $totalBulan * 100);
    $pctS  = round($sedang        / $totalBulan * 100);

    // Table: use monthly data as "rows" (since no per-kecamatan iklim data)
    // Show first 8 months in the table
    $tableData = $iklim->take(8);
@endphp

@section('content')
<div class="container-fluid px-4">
    <div class="statistik-wrapper">

        {{-- SIDEBAR --}}
        <div class="statistik-sidebar">
            <nav class="nav flex-column">
                <a class="nav-link" href="{{ route('statistik.geografis') }}"><i class="fa fa-map"></i> Geografis</a>
                <a class="nav-link active" href="{{ route('statistik.iklim') }}"><i class="fa fa-cloud"></i> Iklim</a>
                <a class="nav-link" href="{{ route('statistik.kependudukan') }}"><i class="fa fa-users"></i> Kependudukan</a>
                <a class="nav-link" href="{{ route('statistik.pendidikan') }}"><i class="fa fa-graduation-cap"></i> Pendidikan</a>
                <a class="nav-link" href="{{ route('statistik.kesehatan') }}"><i class="fa fa-plus-circle"></i> Kesehatan</a>
                <a class="nav-link" href="{{ route('statistik.bencana') }}"><i class="fa fa-house-flood-water"></i> Monitor Bencana</a>
            </nav>
        </div>

        {{-- KONTEN --}}
        <div class="statistik-content">

            {{-- Header --}}
            <div class="stat-header-wrap">
                <div class="stat-header">IKLIM JAKARTA BARAT 2024</div>
            </div>

        {{-- KPI cards --}}
        <div class="kpi-grid">
            {{-- Suhu --}}
            <div class="kpi-card">
                <div>
                    <div class="kpi-label">Rata-rata Suhu Udara (°C)</div>
                    <div class="kpi-value">{{ number_format($avgSuhu, 2) }}</div>
                </div>
                <div class="kpi-icon yellow"><i class="fa fa-thermometer-half"></i></div>
            </div>

            {{-- Curah Hujan --}}
            <div class="kpi-card">
                <div>
                    <div class="kpi-label">Rata-rata Curah Hujan (mm³)</div>
                    <div class="kpi-value">{{ number_format($totalCurahHujan, 1) }}</div>
                </div>
                <div class="kpi-icon blue"><i class="fa fa-tint"></i></div>
            </div>

            {{-- Kelembaban --}}
            <div class="kpi-card">
                <div>
                    <div class="kpi-label">Kelembaban</div>
                    <div class="kpi-value">{{ number_format($avgKelembaban, 0) }}<span class="kpi-unit">%</span></div>
                </div>
                <div class="kpi-icon teal"><i class="fa fa-water"></i></div>
            </div>

            {{-- Status --}}
            <div class="kpi-card kpi-highlight">
                <div>
                    <div class="kpi-label">Status Wilayah</div>
                    <div class="kpi-value-normal" style="margin-top:6px;">
                        @if($statusWilayah === 'Normal')
                            <span style="color:#b8860b;">Normal</span>
                        @elseif($statusWilayah === 'Waspada')
                            <span style="color:#e65100;">Waspada</span>
                        @else
                            <span style="color:#c62828;">Siaga</span>
                        @endif
                    </div>
                </div>
                <div class="kpi-icon info"><i class="fa fa-info-circle"></i></div>
            </div>
        </div>

        {{-- Chart row --}}
        <div class="chart-row">

            {{-- Donut: Distribusi Curah Hujan --}}
            <div class="chart-card">
                <div>
                    <div class="chart-card-icon"><i class="fa fa-chart-pie"></i></div>
                    <div class="chart-card-title">Distribusi Curah Hujan</div>
                </div>
                <div id="chart-donut" style="margin: 0 auto;"></div>
                <div class="donut-legend">
                    <div class="donut-legend-item">
                        <div class="donut-legend-left">
                            <span class="donut-legend-dot" style="background:#1e3a8a;"></span> Sangat Tinggi
                        </div>
                        <span class="donut-legend-pct">{{ $pctST }}%</span>
                    </div>
                    <div class="donut-legend-item">
                        <div class="donut-legend-left">
                            <span class="donut-legend-dot" style="background:#2563eb;"></span> Tinggi
                        </div>
                        <span class="donut-legend-pct">{{ $pctT }}%</span>
                    </div>
                    <div class="donut-legend-item">
                        <div class="donut-legend-left">
                            <span class="donut-legend-dot" style="background:#93c5fd;"></span> Sedang
                        </div>
                        <span class="donut-legend-pct">{{ $pctS }}%</span>
                    </div>
                </div>
            </div>

            {{-- Bar: Tren Curah Hujan Bulanan --}}
            <div class="chart-card" id="chart-bar">
                <div class="chart-card-header">
                    <div>
                        <div class="chart-card-icon" style="background:#fff8e1; color:#b8860b;">
                            <i class="fa fa-chart-bar"></i>
                        </div>
                        <div class="chart-card-title">Tren Curah Hujan Bulanan</div>
                        <div class="chart-card-sub">Fluktuasi Intensitas (mm) Semester 1 2024</div>
                    </div>
                    <span class="chart-total-badge">Total: {{ number_format($totalCurahHujan, 0) }}mm</span>
                </div>
                <div id="chart-bar-hujan"></div>
            </div>
        </div>

        {{-- Table section --}}
        <div class="table-section" id="table-section">
            <div class="table-section-header">
                <div>
                    <div class="table-section-title">Data Iklim per Bulan</div>
                    <div class="table-section-sub">Rincian parameter cuaca di wilayah Jakarta Barat</div>
                </div>
                <a href="{{ route('admin.iklim.index') }}" class="link-lihat-semua">Lihat Semua</a>
            </div>

            <table class="iklim-table">
                <thead>
                    <tr>
                        <th>BULAN</th>
                        <th>CURAH HUJAN (MM)</th>
                        <th>SUHU (°C)</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tableData as $row)
                        @php
                            $curahMm = $row->hari_hujan * 10;
                            if ($row->suhu_udara >= 30) {
                                $status = 'Waspada'; $statusClass = 'status-waspada';
                            } elseif ($row->suhu_udara >= 28.5) {
                                $status = 'Normal';  $statusClass = 'status-normal';
                            } else {
                                $status = 'Normal';  $statusClass = 'status-normal';
                            }
                            // Flag high-rain months as waspada
                            if ($row->hari_hujan >= 25) {
                                $status = 'Waspada'; $statusClass = 'status-waspada';
                            }
                        @endphp
                        <tr>
                            <td>{{ $bulanLabel[$row->bulan] ?? $row->bulan }}</td>
                            <td>{{ number_format($curahMm, 0) }}</td>
                            <td>{{ number_format($row->suhu_udara, 1) }}</td>
                            <td><span class="status-badge {{ $statusClass }}">{{ $status }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="text-align:right; font-size:12px; color:#bbb; margin-top:16px;">
            Sumber: Kota Jakarta Barat Dalam Angka 2025
        </div>
        </div>{{-- statistik-content --}}

    </div>{{-- statistik-wrapper --}}
</div>{{-- container-fluid --}}
@endsection

@push('scripts')
<script>
    var bulanLabels   = {!! json_encode($iklim->map(fn($d) => ($bulanLabel[$d->bulan] ?? $d->bulan))) !!};
    var curahHujan    = {!! json_encode($curahHujanBulanan->values()) !!};
    var maxCurah      = Math.max(...curahHujan);

    // ── Bar chart: Tren Curah Hujan Bulanan ──────────────────────
    var barColors = curahHujan.map(function(v) {
        return v === maxCurah ? '#8b6914' : '#93c5fd';
    });

    new ApexCharts(document.querySelector('#chart-bar-hujan'), {
        chart: {
            type: 'bar',
            height: 270,
            toolbar: { show: false },
            animations: { enabled: true, easing: 'easeinout', speed: 600 }
        },
        series: [{ name: 'Curah Hujan (mm)', data: curahHujan }],
        xaxis: {
            categories: bulanLabels,
            labels: { style: { fontSize: '11px', colors: '#aaa' } },
            axisBorder: { show: false },
            axisTicks:  { show: false }
        },
        yaxis: { labels: { style: { colors: '#aaa', fontSize: '11px' } } },
        colors: barColors,
        fill: { colors: barColors },
        dataLabels: { enabled: false },
        plotOptions: {
            bar: {
                borderRadius: 5,
                columnWidth: '55%',
                distributed: true
            }
        },
        legend: { show: false },
        grid: { borderColor: '#f0f0f0', strokeDashArray: 4 },
        tooltip: {
            y: { formatter: function(v) { return v + ' mm'; } }
        }
    }).render();

    // ── Donut chart: Distribusi Kelembaban ───────────────────────
    var donutValues = [{{ $pctST }}, {{ $pctT }}, {{ $pctS }}];
    var donutTotal  = donutValues.reduce((a,b) => a+b, 0) || 1;
    var donutMain   = Math.round({{ $pctT + $pctST }});

    new ApexCharts(document.querySelector('#chart-donut'), {
        chart: {
            type: 'donut',
            height: 220,
            toolbar: { show: false }
        },
        series: donutValues,
        labels: ['Sangat Tinggi', 'Tinggi', 'Sedang'],
        colors: ['#1e3a8a', '#2563eb', '#93c5fd'],
        dataLabels: { enabled: false },
        legend: { show: false },
        stroke: { width: 3, colors: ['#fff'] },
        plotOptions: {
            pie: {
                donut: {
                    size: '72%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Tinggi',
                            color: '#333',
                            fontSize: '13px',
                            fontWeight: 600,
                            formatter: function() {
                                return donutMain + '%';
                            }
                        },
                        value: {
                            show: true,
                            fontSize: '26px',
                            fontWeight: 800,
                            color: '#1a1a1a',
                            formatter: function(v) { return v + '%'; }
                        }
                    }
                }
            }
        },
        tooltip: {
            y: { formatter: function(v) { return v + '%'; } }
        }
    }).render();
</script>
@endpush