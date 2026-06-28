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

    .stat-summary-card {
        background: #f9f9f9; border: 1px solid #eee;
        border-radius: 8px; padding: 16px 24px;
        display: flex; flex-direction: row-reverse;
        align-items: center; justify-content: center; gap: 16px;
        margin-bottom: 0; text-align: center;
    }
    .stat-summary-card .card-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; flex-shrink: 0;
    }
    .stat-summary-card .card-text { display: flex; flex-direction: column; align-items: center; }
    .stat-summary-card .card-text .value { font-size: 28px; font-weight: 700; color: #333; }
    .stat-summary-card .card-text .label { font-size: 12px; font-weight: 600; color: #888; letter-spacing: 1px; }
    .stat-summary-card .card-text .value-status { font-size: 24px; font-weight: 700; }

    .chart-card { background: #fff; border: 1px solid #eee; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
    .chart-card .chart-title { font-size: 13px; font-weight: 600; color: #555; letter-spacing: 1px; margin-bottom: 16px; }
    .chart-title-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .chart-title-row .chart-title { margin-bottom: 0; }
    .chart-total-badge {
        background: #fff8e1; border: 1px solid #ffd54f;
        color: #b8860b; border-radius: 6px; font-size: 12px;
        font-weight: 700; padding: 4px 10px;
    }

    /* Chart row 2 col */
    .chart-row { display: grid; grid-template-columns: 1fr 1.7fr; gap: 20px; margin-bottom: 20px; }

    /* Donut legend */
    .donut-legend { margin-top: 16px; }
    .donut-legend-item {
        display: flex; align-items: center; justify-content: space-between;
        font-size: 13px; color: #444; margin-bottom: 6px;
    }
    .donut-legend-dot { width: 10px; height: 10px; border-radius: 50%; margin-right: 8px; display: inline-block; flex-shrink: 0; }
    .donut-legend-left { display: flex; align-items: center; }
    .donut-legend-pct { font-weight: 700; color: #333; }

    /* Table */
    .iklim-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .iklim-table thead th {
        font-size: 11px; font-weight: 700; letter-spacing: .8px;
        color: #999; text-transform: uppercase;
        border-bottom: 1px solid #f0f0f0; padding: 0 0 12px;
        text-align: center;
    }
    .iklim-table thead th:not(:first-child) { text-align: center; }
    .iklim-table tbody td { padding: 13px 0; border-bottom: 1px solid #f7f7f7; color: #333; text-align: center; }
    .iklim-table tbody td:not(:first-child) { text-align: center; }
    .iklim-table tbody tr:last-child td { border-bottom: none; }
    .status-badge { display: inline-block; border-radius: 6px; padding: 3px 10px; font-size: 11.5px; font-weight: 700; }
    .status-normal  { background: #fff8e1; color: #b8860b; border: 1px solid #ffd54f; }
    .status-waspada { background: #fff3e0; color: #e65100; border: 1px solid #ffb74d; }

    .sumber { text-align: right; font-size: 12px; color: #999; margin-top: 16px; }

    @media (max-width: 1100px) { .chart-row { grid-template-columns: 1fr; } }
    @media (max-width: 768px)  { .statistik-sidebar { display: none; } }
</style>
@endpush

@php
    $bulanLabel = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                   7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];

    $avgSuhu          = $iklim->avg('suhu_udara');
    $avgHariHujan     = $iklim->avg('hari_hujan');          // rata-rata hari hujan/bulan
    $avgKelembaban    = $iklim->avg('kelembaban_udara');
    $avgAngin         = $iklim->avg('kecepatan_angin');
    $avgTekanan       = $iklim->avg('tekanan_udara');
    $avgPenyinaran    = $iklim->avg('penyinaran_matahari');

    // Status berdasarkan rata-rata hari hujan per bulan
    $statusWilayah = $avgHariHujan > 20 ? 'Waspada' : ($avgHariHujan > 14 ? 'Siaga' : 'Normal');

    // Bar chart: hari hujan per bulan
    $hariHujanBulanan = $iklim->pluck('hari_hujan')->map(fn($v) => round((float)$v, 1));

    // Line chart: suhu per bulan
    $suhuBulanan = $iklim->pluck('suhu_udara')->map(fn($v) => round((float)$v, 1));

    // Donut: distribusi kelembaban ke 3 band
    $sangat_tinggi = $iklim->filter(fn($d) => $d->kelembaban_udara >= 80)->count();
    $tinggi        = $iklim->filter(fn($d) => $d->kelembaban_udara >= 75 && $d->kelembaban_udara < 80)->count();
    $sedang        = $iklim->filter(fn($d) => $d->kelembaban_udara < 75)->count();
    $totalBulan    = $iklim->count() ?: 1;
    $pctST = round($sangat_tinggi / $totalBulan * 100);
    $pctT  = round($tinggi        / $totalBulan * 100);
    $pctS  = round($sedang        / $totalBulan * 100);

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

        {{-- Summary cards --}}
        <div class="row mb-4 align-items-stretch">
            <div class="col-md-3 d-flex">
                <div class="stat-summary-card w-100">
                    <div class="card-icon" style="background:#fff8e1;">
                        <i class="fa fa-thermometer-half" style="color:#ffbf00;"></i>
                    </div>
                    <div class="card-text">
                        <div class="label">RATA-RATA SUHU UDARA (°C)</div>
                        <div class="value">{{ number_format($avgSuhu, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex">
                <div class="stat-summary-card w-100">
                    <div class="card-icon" style="background:#e3f0ff;">
                        <i class="fa fa-tint" style="color:#2196f3;"></i>
                    </div>
                    <div class="card-text">
                        <div class="label">RATA-RATA HARI HUJAN (HARI/BLN)</div>
                        <div class="value">{{ number_format($avgHariHujan, 1) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex">
                <div class="stat-summary-card w-100">
                    <div class="card-icon" style="background:#e0f2f1;">
                        <i class="fa fa-water" style="color:#00897b;"></i>
                    </div>
                    <div class="card-text">
                        <div class="label">KELEMBABAN UDARA (%)</div>
                        <div class="value">{{ number_format($avgKelembaban, 0) }}%</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex">
                <div class="stat-summary-card w-100" style="border-color:#ffd54f;">
                    <div class="card-icon" style="background:#fff8e1;">
                        <i class="fa fa-info-circle" style="color:#ffbf00;"></i>
                    </div>
                    <div class="card-text">
                        <div class="label">STATUS WILAYAH</div>
                        <div class="value-status" style="font-size:24px; font-weight:700; margin-top:4px;">
                            @if($statusWilayah === 'Normal')
                                <span style="color:#b8860b;">Normal</span>
                            @elseif($statusWilayah === 'Waspada')
                                <span style="color:#e65100;">Waspada</span>
                            @else
                                <span style="color:#c62828;">Siaga</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart row --}}
        <div class="chart-row">

            {{-- Donut: Distribusi Curah Hujan --}}
            <div class="chart-card">
                <div class="chart-title">DISTRIBUSI CURAH HUJAN</div>
                <div id="chart-donut"></div>
                <div class="donut-legend">
                    <div class="donut-legend-item">
                        <div class="donut-legend-left">
                            <span class="donut-legend-dot" style="background:#2196f3;"></span> Sangat Tinggi
                        </div>
                        <span class="donut-legend-pct">{{ $pctST }}%</span>
                    </div>
                    <div class="donut-legend-item">
                        <div class="donut-legend-left">
                            <span class="donut-legend-dot" style="background:#ffbf00;"></span> Tinggi
                        </div>
                        <span class="donut-legend-pct">{{ $pctT }}%</span>
                    </div>
                    <div class="donut-legend-item">
                        <div class="donut-legend-left">
                            <span class="donut-legend-dot" style="background:#e91e8c;"></span> Sedang
                        </div>
                        <span class="donut-legend-pct">{{ $pctS }}%</span>
                    </div>
                </div>
            </div>

            {{-- Bar: Tren Hari Hujan Bulanan --}}
            <div class="chart-card" id="chart-bar">
                <div class="chart-title-row">
                    <div class="chart-title">TREN HARI HUJAN BULANAN</div>
                    <span class="chart-total-badge">Rata-rata: {{ number_format($avgHariHujan, 1) }} hari/bln</span>
                </div>
                <div id="chart-bar-hujan"></div>
            </div>
        </div>

        {{-- Table section --}}
        <div class="chart-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <div class="chart-title" style="margin-bottom:0;">DATA IKLIM PER BULAN</div>
                <a href="{{ route('admin.iklim.index') }}" style="font-size:13px; color:#ffbf00; font-weight:600; text-decoration:none;">Lihat Semua</a>
            </div>

            <table class="iklim-table">
                <thead>
                    <tr>
                        <th>BULAN</th>
                        <th>HARI HUJAN</th>
                        <th>SUHU (°C)</th>
                        <th>KELEMBABAN (%)</th>
                        <th>ANGIN (KM/H)</th>
                        <th>TEKANAN (MB)</th>
                        <th>PENYINARAN (%)</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tableData as $row)
                        @php
                            if ($row->hari_hujan >= 20) {
                                $status = 'Waspada'; $statusClass = 'status-waspada';
                            } else {
                                $status = 'Normal';  $statusClass = 'status-normal';
                            }
                        @endphp
                        <tr>
                            <td>{{ $bulanLabel[$row->bulan] ?? $row->bulan }}</td>
                            <td>{{ number_format($row->hari_hujan, 1) }}</td>
                            <td>{{ number_format($row->suhu_udara, 1) }}</td>
                            <td>{{ number_format($row->kelembaban_udara, 1) }}%</td>
                            <td>{{ number_format($row->kecepatan_angin, 1) }}</td>
                            <td>{{ number_format($row->tekanan_udara, 1) }}</td>
                            <td>{{ number_format($row->penyinaran_matahari, 1) }}%</td>
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
    var bulanLabels    = {!! json_encode($iklim->map(fn($d) => ($bulanLabel[$d->bulan] ?? $d->bulan))) !!};
    var hariHujan      = {!! json_encode($hariHujanBulanan->values()) !!};
    var avgHariHujan   = {{ round($avgHariHujan, 1) }};

    var barColors = ['#2196f3','#e91e8c','#ff9800','#4caf50','#8bc34a','#9c27b0','#f44336','#00bcd4','#2196f3','#e91e8c','#ff9800','#4caf50'];
    var activeBar = null;

    var lightBars = ['#ff9800','#4caf50','#8bc34a','#ffbf00'];
    function labelColor(hexColor) {
        return lightBars.indexOf(hexColor) !== -1 ? '#333' : '#fff';
    }
    var barLabelColors = barColors.map(labelColor);

    var barChart = new ApexCharts(document.querySelector('#chart-bar-hujan'), {
        chart: {
            type: 'bar',
            height: 270,
            toolbar: { show: false },
            animations: { enabled: true, easing: 'easeinout', speed: 600 },
            events: {
                dataPointSelection: function(e, ctx, cfg) {
                    var idx = cfg.dataPointIndex;
                    if (activeBar === idx) {
                        activeBar = null;
                        barChart.updateOptions({
                            colors: barColors,
                            fill:   { colors: barColors },
                            dataLabels: { style: { colors: barLabelColors } }
                        });
                        return;
                    }
                    activeBar = idx;
                    var fadedColors = barColors.map(function(c, i) { return i === idx ? c : c + '44'; });
                    var fadedLabels = barLabelColors.map(function(c, i) { return i === idx ? c : c + '44'; });
                    barChart.updateOptions({
                        colors: fadedColors,
                        fill:   { colors: fadedColors },
                        dataLabels: { style: { colors: fadedLabels } }
                    });
                },
                click: function(e, ctx, cfg) {
                    if (cfg.dataPointIndex === undefined || cfg.dataPointIndex < 0) {
                        activeBar = null;
                        barChart.updateOptions({
                            colors: barColors,
                            fill:   { colors: barColors },
                            dataLabels: { style: { colors: barLabelColors } }
                        });
                    }
                }
            }
        },
        series: [{ name: 'Hari Hujan (hari)', data: hariHujan }],
        xaxis: {
            categories: bulanLabels,
            labels: { style: { fontSize: '11px', colors: '#aaa' } },
            axisBorder: { show: false },
            axisTicks:  { show: false }
        },
        yaxis: {
            labels: { style: { colors: '#aaa', fontSize: '11px' } },
            title: { text: 'Hari', style: { color: '#aaa', fontSize: '11px' } }
        },
        colors: barColors,
        fill: { colors: barColors },
        dataLabels: {
            enabled: true,
            style: { fontSize: '10px', fontWeight: 700, colors: barLabelColors },
            dropShadow: { enabled: false }
        },
        plotOptions: { bar: { borderRadius: 3, columnWidth: '55%', distributed: true } },
        states: {
            hover:  { filter: { type: 'lighten', value: 0.08 } },
            active: { filter: { type: 'darken',  value: 0.15 } }
        },
        annotations: {
            yaxis: [{
                y: avgHariHujan,
                borderColor: '#999',
                borderWidth: 1,
                strokeDashArray: 4,
                label: {
                    text: 'Rata-rata: ' + avgHariHujan + ' hari',
                    style: { color: '#666', fontSize: '10px', background: '#f9f9f9' }
                }
            }]
        },
        legend: { show: false },
        grid: { borderColor: '#f0f0f0', strokeDashArray: 4 },
        tooltip: {
            y: { formatter: function(v) { return v + ' hari'; } }
        }
    });
    barChart.render();

    var donutValues  = [{{ $pctST }}, {{ $pctT }}, {{ $pctS }}];
    var donutLabels  = ['Sangat Tinggi', 'Tinggi', 'Sedang'];
    var donutColors  = ['#2196f3', '#ffbf00', '#e91e8c'];
    var donutMain    = Math.round({{ $pctT + $pctST }});

    var donutChart = new ApexCharts(document.querySelector('#chart-donut'), {
        chart: {
            type: 'donut',
            height: 220,
            toolbar: { show: false },
            animations: { enabled: true, easing: 'easeinout', speed: 500 },
            events: {
                // Klik segment: update center label & highlight legend
                dataPointSelection: function(e, ctx, cfg) {
                    var idx = cfg.dataPointIndex;
                    var el  = document.querySelectorAll('.donut-legend-item')[idx];
                    document.querySelectorAll('.donut-legend-item').forEach(function(item, i) {
                        item.style.opacity   = i === idx ? '1' : '0.35';
                        item.style.fontWeight = i === idx ? '700' : '400';
                        item.style.transform = i === idx ? 'scale(1.03)' : 'scale(1)';
                        item.style.transition = 'all .2s';
                    });
                },
                // Reset legend saat klik di luar
                click: function(e, ctx, cfg) {
                    if (cfg.dataPointIndex === undefined || cfg.dataPointIndex < 0) {
                        document.querySelectorAll('.donut-legend-item').forEach(function(item) {
                            item.style.opacity   = '1';
                            item.style.fontWeight = '400';
                            item.style.transform = 'scale(1)';
                        });
                    }
                }
            }
        },
        series: donutValues,
        labels: donutLabels,
        colors: donutColors,
        dataLabels: { enabled: false },
        legend: { show: false },
        stroke: { width: 3, colors: ['#fff'] },
        states: {
            hover:  { filter: { type: 'lighten', value: 0.05 } },
            active: { filter: { type: 'darken',  value: 0.15 } }
        },
        plotOptions: {
            pie: {
                expandOnClick: true,
                donut: {
                    size: '72%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Tinggi',
                            color: '#333', fontSize: '13px', fontWeight: 600,
                            formatter: function() { return donutMain + '%'; }
                        },
                        value: {
                            show: true, fontSize: '26px', fontWeight: 800,
                            color: '#1a1a1a',
                            formatter: function(v) { return v + '%'; }
                        },
                        name: {
                            show: true, fontSize: '13px', fontWeight: 600, color: '#888'
                        }
                    }
                }
            }
        },
        tooltip: { enabled: false }
    });
    donutChart.render();

    // Legend items — klik untuk toggle segment donut
    document.querySelectorAll('.donut-legend-item').forEach(function(item, idx) {
        item.style.cursor = 'pointer';
        item.style.transition = 'all .2s';
        item.addEventListener('click', function() {
            donutChart.toggleDataPointSelection(0, idx);
        });
        item.addEventListener('mouseenter', function() {
            if (item.style.opacity !== '0.35') item.style.transform = 'scale(1.03)';
        });
        item.addEventListener('mouseleave', function() {
            if (item.style.opacity !== '0.35') item.style.transform = 'scale(1)';
        });
    });
</script>
@endpush