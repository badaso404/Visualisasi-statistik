@extends('landing-page.layout.app')

@push('styles')
<style>
    .statistik-wrapper {
        display: flex;
        gap: 24px;
        padding: 40px 0;
    }
    .statistik-sidebar {
        width: 220px;
        flex-shrink: 0;
    }
    .statistik-sidebar .nav-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        border-radius: 8px;
        color: #555;
        font-weight: 500;
        margin-bottom: 4px;
        transition: all 0.2s;
    }
    .statistik-sidebar .nav-link:hover { background: #f0f0f0; color: #ffbf00; }
    .statistik-sidebar .nav-link.active { background: #ffbf00; color: #fff; }
    .statistik-content { flex: 1; min-width: 0; }
    .stat-header {
        background: #ffbf00;
        color: white;
        text-align: center;
        padding: 14px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 18px;
        margin-bottom: 24px;
        letter-spacing: 1px;
    }
    .stat-summary-card {
        background: #f9f9f9;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 16px 24px;
    }
    .stat-summary-card .value { font-size: 28px; font-weight: 700; color: #333; }
    .stat-summary-card .label { font-size: 12px; font-weight: 600; color: #888; letter-spacing: 1px; }
    .chart-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .chart-card .chart-title { font-size: 13px; font-weight: 600; color: #555; letter-spacing: 1px; margin-bottom: 16px; }
    .sumber { text-align: right; font-size: 12px; color: #999; margin-top: 16px; }
</style>
@endpush

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
            <div class="stat-header">IKLIM JAKARTA BARAT 2024</div>

            {{-- Summary --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="stat-summary-card">
                        <div class="label">RATA-RATA SUHU UDARA (°C)</div>
                        <div class="value">{{ number_format($iklim->avg('suhu_udara'), 2, ',', '.') }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-summary-card">
                        <div class="label">RATA-RATA CURAH HUJAN (mm³)</div>
                        <div class="value">{{ number_format($iklim->sum('hari_hujan') * 10, 1, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            {{-- Row 1 --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-title">BANYAKNYA HARI HUJAN (hari)</div>
                        <div id="chart-hari-hujan"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-title">TEKANAN UDARA (mb)</div>
                        <div id="chart-tekanan"></div>
                    </div>
                </div>
            </div>

            {{-- Row 2 --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-title">SUHU UDARA (°C)</div>
                        <div id="chart-suhu"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-title">KECEPATAN ANGIN (knot)</div>
                        <div id="chart-angin"></div>
                    </div>
                </div>
            </div>

            {{-- Row 3 --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-title">KELEMBABAN UDARA (%)</div>
                        <div id="chart-kelembaban"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-title">PENYINARAN MATAHARI (jam)</div>
                        <div id="chart-matahari"></div>
                    </div>
                </div>
            </div>

            <div class="sumber">Sumber: Kota Jakarta Barat Dalam Angka 2025</div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    var bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

    var hariHujan    = {!! json_encode($iklim->pluck('hari_hujan')->map(fn($v) => (float)$v)) !!};
    var tekanan      = {!! json_encode($iklim->pluck('tekanan_udara')->map(fn($v) => (float)$v)) !!};
    var suhu         = {!! json_encode($iklim->pluck('suhu_udara')->map(fn($v) => (float)$v)) !!};
    var angin        = {!! json_encode($iklim->pluck('kecepatan_angin')->map(fn($v) => (float)$v)) !!};
    var kelembaban   = {!! json_encode($iklim->pluck('kelembaban_udara')->map(fn($v) => (float)$v)) !!};
    var matahari     = {!! json_encode($iklim->pluck('penyinaran_matahari')->map(fn($v) => (float)$v)) !!};

    function buatChart(id, data, warna) {
        new ApexCharts(document.querySelector(id), {
            chart: { type: 'bar', height: 250, toolbar: { show: false } },
            series: [{ name: '', data: data }],
            xaxis: { categories: bulan },
            colors: [warna],
            dataLabels: { enabled: true, style: { fontSize: '10px' } },
            plotOptions: { bar: { borderRadius: 3 } },
        }).render();
    }

    buatChart('#chart-hari-hujan', hariHujan,  '#e91e8c');
    buatChart('#chart-tekanan',    tekanan,     '#5aabff');
    buatChart('#chart-suhu',       suhu,        '#00bcd4');
    buatChart('#chart-angin',      angin,       '#4caf50');
    buatChart('#chart-kelembaban', kelembaban,  '#ff9800');
    buatChart('#chart-matahari',   matahari,    '#9c27b0');
</script>
@endpush