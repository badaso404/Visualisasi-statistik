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
    .stat-header {
        background: #ffbf00; color: white; text-align: center;
        padding: 14px; border-radius: 8px; font-weight: 700;
        font-size: 18px; margin-bottom: 24px; letter-spacing: 1px;
    }
    .stat-summary-card {
        background: #f9f9f9; border: 1px solid #eee;
        border-radius: 8px; padding: 16px 24px; margin-bottom: 8px;
    }
    .stat-summary-card .value { font-size: 24px; font-weight: 700; color: #333; }
    .stat-summary-card .label { font-size: 11px; font-weight: 600; color: #888; letter-spacing: 1px; }
    .stat-summary-card .sublabel { font-size: 10px; color: #aaa; }
    .chart-card { background: #fff; border: 1px solid #eee; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
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
                <a class="nav-link" href="{{ route('statistik.iklim') }}"><i class="fa fa-cloud"></i> Iklim</a>
                <a class="nav-link" href="{{ route('statistik.kependudukan') }}"><i class="fa fa-users"></i> Kependudukan</a>
                <a class="nav-link active" href="{{ route('statistik.pendidikan') }}"><i class="fa fa-graduation-cap"></i> Pendidikan</a>
                <a class="nav-link" href="{{ route('statistik.kesehatan') }}"><i class="fa fa-plus-circle"></i> Kesehatan</a>
            </nav>
        </div>

        {{-- KONTEN --}}
        <div class="statistik-content">
            <div class="stat-header">PENDIDIKAN JAKARTA BARAT 2024</div>

            {{-- Summary APM & APK --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-title">ANGKA PARTISIPASI MURNI (APM)</div>
                        <div class="row">
                            <div class="col-4">
                                <div class="stat-summary-card text-center">
                                    <div class="sublabel">SD/MI</div>
                                    <div class="value">{{ $summary->apm_sd_mi }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-summary-card text-center">
                                    <div class="sublabel">SMP/MTS</div>
                                    <div class="value">{{ $summary->apm_smp_mts }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-summary-card text-center">
                                    <div class="sublabel">SMA/SMK/MAN</div>
                                    <div class="value">{{ $summary->apm_sma_smk_man }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-title">ANGKA PARTISIPASI KASAR (APK)</div>
                        <div class="row">
                            <div class="col-4">
                                <div class="stat-summary-card text-center">
                                    <div class="sublabel">SD/MI</div>
                                    <div class="value">{{ $summary->apk_sd_mi }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-summary-card text-center">
                                    <div class="sublabel">SMP/MTS</div>
                                    <div class="value">{{ $summary->apk_smp_mts }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-summary-card text-center">
                                    <div class="sublabel">SMA/SMK/MAN</div>
                                    <div class="value">{{ $summary->apk_sma_smk_man }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-title">JUMLAH MURID PADA KECAMATAN</div>
                        <div id="chart-murid"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-title">JUMLAH GURU PADA KECAMATAN</div>
                        <div id="chart-guru"></div>
                    </div>
                </div>
            </div>

            <div class="sumber">Sumber: {{ $summary->sumber }}</div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    var namaKecamatan = {!! json_encode($perKecamatan->pluck('kecamatan.nama_kecamatan')) !!};
    var jumlahMurid   = {!! json_encode($perKecamatan->pluck('jumlah_murid')->map(fn($v) => (int)$v)) !!};
    var jumlahGuru    = {!! json_encode($perKecamatan->pluck('jumlah_guru')->map(fn($v) => (int)$v)) !!};

    // Chart Murid
    new ApexCharts(document.querySelector("#chart-murid"), {
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        series: [{ name: 'Murid', data: jumlahMurid }],
        xaxis: { categories: namaKecamatan, labels: { style: { fontSize: '10px' } } },
        colors: ['#4caf50'],
        dataLabels: { enabled: true, style: { fontSize: '9px' } },
        plotOptions: { bar: { borderRadius: 3 } },
    }).render();

    // Chart Guru
    new ApexCharts(document.querySelector("#chart-guru"), {
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        series: [{ name: 'Guru', data: jumlahGuru }],
        xaxis: { categories: namaKecamatan, labels: { style: { fontSize: '10px' } } },
        colors: ['#9c27b0'],
        dataLabels: { enabled: true, style: { fontSize: '9px' } },
        plotOptions: { bar: { borderRadius: 3 } },
    }).render();
</script>
@endpush