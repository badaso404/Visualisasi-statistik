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
    .stat-summary-card .value { font-size: 28px; font-weight: 700; color: #333; }
    .stat-summary-card .label { font-size: 11px; font-weight: 600; color: #888; letter-spacing: 1px; }
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
                <a class="nav-link" href="{{ route('statistik.pendidikan') }}"><i class="fa fa-graduation-cap"></i> Pendidikan</a>
                <a class="nav-link active" href="{{ route('statistik.kesehatan') }}"><i class="fa fa-plus-circle"></i> Kesehatan</a>
                <a class="nav-link" href="{{ route('statistik.bencana') }}"><i class="fa fa-house-flood-water"></i> Monitor Bencana</a>
            </nav>
        </div>

        {{-- KONTEN --}}
        <div class="statistik-content">
            <div class="stat-header">KESEHATAN JAKARTA BARAT 2024</div>

            {{-- Summary --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="stat-summary-card">
                        <div class="label">JUMLAH TEMPAT TIDUR RUMAH SAKIT</div>
                        <div class="value">{{ number_format($summary->jumlah_tempat_tidur_rs) }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-summary-card">
                        <div class="label">CAKUPAN IMUNISASI DASAR LENGKAP JAKBAR (%)</div>
                        <div class="value">{{ $summary->cakupan_imunisasi_dasar }}</div>
                    </div>
                </div>
            </div>

            {{-- Charts --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-title">JUMLAH TENAGA KESEHATAN PADA KECAMATAN</div>
                        <div id="chart-tenaga"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-title">JUMLAH FASILITAS KESEHATAN PADA KECAMATAN</div>
                        <div id="chart-fasilitas"></div>
                    </div>
                </div>
            </div>

            {{-- Chart Detail Stacked --}}
            <div class="chart-card">
                <div class="chart-title">DETAIL TENAGA DAN FASILITAS KESEHATAN PADA KECAMATAN</div>
                <div id="chart-detail"></div>
            </div>

            <div class="sumber">Sumber: {{ $summary->sumber }}</div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    var namaKecamatan  = {!! json_encode($tenaga->pluck('kecamatan.nama_kecamatan')) !!};
    var jumlahTenaga   = {!! json_encode($tenaga->pluck('jumlah_total')->map(fn($v) => (int)$v)) !!};

    var namaFasilitas  = {!! json_encode($fasilitas->pluck('kecamatan.nama_kecamatan')) !!};
    var jumlahFasilitas = {!! json_encode($fasilitas->pluck('jumlah_total')->map(fn($v) => (int)$v)) !!};

    // Chart Tenaga
    new ApexCharts(document.querySelector("#chart-tenaga"), {
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        series: [{ name: 'Tenaga Kesehatan', data: jumlahTenaga }],
        xaxis: { categories: namaKecamatan, labels: { style: { fontSize: '10px' } } },
        colors: ['#e53935'],
        dataLabels: { enabled: true, style: { fontSize: '9px' } },
        plotOptions: { bar: { borderRadius: 3 } },
    }).render();

    // Chart Fasilitas
    new ApexCharts(document.querySelector("#chart-fasilitas"), {
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        series: [{ name: 'Fasilitas Kesehatan', data: jumlahFasilitas }],
        xaxis: { categories: namaFasilitas, labels: { style: { fontSize: '10px' } } },
        colors: ['#8bc34a'],
        dataLabels: { enabled: true, style: { fontSize: '9px' } },
        plotOptions: { bar: { borderRadius: 3 } },
    }).render();

    // Chart Detail Stacked (Tenaga + Fasilitas per kecamatan)
    new ApexCharts(document.querySelector("#chart-detail"), {
        chart: { type: 'bar', height: 300, stacked: true, toolbar: { show: false } },
        series: [
            { name: 'Tenaga Kesehatan', data: jumlahTenaga },
            { name: 'Fasilitas Kesehatan', data: jumlahFasilitas },
        ],
        xaxis: { categories: namaKecamatan, labels: { style: { fontSize: '10px' } } },
        colors: ['#e53935', '#8bc34a'],
        dataLabels: { enabled: false },
        plotOptions: { bar: { borderRadius: 3 } },
        legend: { position: 'top' },
    }).render();
</script>
@endpush