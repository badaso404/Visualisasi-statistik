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

    .statistik-sidebar .nav-link:hover {
        background: #f0f0f0;
        color: #ffbf00;
    }

    .statistik-sidebar .nav-link.active {
        background: #ffbf00;
        color: #fff;
    }

    .statistik-content {
        flex: 1;
        min-width: 0;
    }

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
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f9f9f9;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 16px 24px;
    }

    .stat-summary-card .value {
        font-size: 28px;
        font-weight: 700;
        color: #333;
    }

    .stat-summary-card .label {
        font-size: 12px;
        font-weight: 600;
        color: #888;
        letter-spacing: 1px;
    }

    .chart-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .chart-card .chart-title {
        font-size: 13px;
        font-weight: 600;
        color: #555;
        letter-spacing: 1px;
        margin-bottom: 16px;
    }

    .sumber {
        text-align: right;
        font-size: 12px;
        color: #999;
        margin-top: 16px;
    }
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
                    <a class="nav-link active" href="{{ route('statistik.geografis') }}">
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
                </nav>
            </div>

            {{-- KONTEN --}}
            <div class="statistik-content">
                <div class="stat-header">GEOGRAFIS JAKARTA BARAT {{ $geo->tahun }}</div>

                {{-- Summary --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="stat-summary-card">
                            <i class="fas fa-vector-square" style="font-size: 2.2rem; color: #ffbf00;"></i>
                            <div>
                                <div class="label">LUAS WILAYAH (km2)</div>
                                <div class="value">{{ number_format($geo->luas_kota_km2, 2, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-summary-card">
                            <i class="fas fa-mountain" style="font-size: 2.2rem; color: #ffbf00;"></i>
                            <div>
                                <div class="label">KETINGGIAN (M dpl)</div>
                                <div class="value">{{ $geo->ketinggian_mdpl }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Chart Luas Kecamatan --}}
                <div class="chart-card">
                    <div class="chart-title">LUAS WILAYAH KECAMATAN (km2)</div>
                    <div id="chart-luas-kecamatan"></div>
                </div>

                {{-- Chart Persentase --}}
                <div class="chart-card">
                    <div class="chart-title">PERSENTASE TERHADAP LUAS KOTA (%)</div>
                    <div id="chart-persentase"></div>
                </div>

                <div class="sumber">Sumber: {{ $geo->sumber }}</div>
            </div>

        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Data dari Laravel
    var namaKecamatan = {!! json_encode($luas->pluck('kecamatan.nama_kecamatan')) !!};
    var luasKm2 = {!! json_encode($luas->pluck('luas_km2')->map(fn($v) => (float)$v)) !!};
    var persentase = {!! json_encode($luas->pluck('persentase')->map(fn($v) => (float)$v)) !!};

    // Chart Bar - Luas Kecamatan
    var chartLuas = new ApexCharts(document.querySelector("#chart-luas-kecamatan"), {
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        series: [{ name: 'Luas (km2)', data: luasKm2 }],
        xaxis: { categories: namaKecamatan },
        colors: ['#5aabff'],
        dataLabels: { enabled: true },
        plotOptions: { bar: { borderRadius: 4 } },
    });
    chartLuas.render();

    // Chart Donut - Persentase
    var chartPersen = new ApexCharts(document.querySelector("#chart-persentase"), {
        chart: { type: 'donut', height: 350 },
        series: persentase,
        labels: namaKecamatan,
        dataLabels: { enabled: true },
        legend: { position: 'right' },
    });
    chartPersen.render();
</script>
@endpush