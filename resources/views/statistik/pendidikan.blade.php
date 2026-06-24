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
    .total-badge { display: inline-block; background: #fff3cd; border: 1px solid #ffc107; color: #856404; border-radius: 6px; padding: 2px 8px; font-size: 11px; font-weight: 600; }
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
                <a class="nav-link" href="{{ route('statistik.bencana') }}"><i class="fa fa-house-flood-water"></i> Monitor Bencana</a>
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

            {{-- Ringkasan Total per Kecamatan --}}
            <div class="chart-card mb-4">
                <div class="chart-title">RINGKASAN TOTAL PER KECAMATAN</div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0" style="font-size:13px;">
                        <thead class="table-light">
                            <tr>
                                <th>Kecamatan</th>
                                <th class="text-center">Pelajar</th>
                                <th class="text-center">Pendidik</th>
                                <th class="text-center">Sekolah Negeri</th>
                                <th class="text-center">Sekolah Swasta</th>
                                <th class="text-center">Total Sekolah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($perKecamatan as $row)
                            <tr>
                                <td>{{ $row->kecamatan->nama_kecamatan ?? '-' }}</td>
                                <td class="text-center">{{ number_format($row->jumlah_pelajar, 0, ',', '.') }}</td>
                                <td class="text-center">{{ number_format($row->jumlah_pendidik, 0, ',', '.') }}</td>
                                <td class="text-center">{{ number_format($row->jumlah_sekolah_negeri, 0, ',', '.') }}</td>
                                <td class="text-center">{{ number_format($row->jumlah_sekolah_swasta, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="total-badge">{{ number_format($row->jumlah_sekolah_negeri + $row->jumlah_sekolah_swasta, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Charts Pelajar & Pendidik --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-title">JUMLAH PELAJAR PER KECAMATAN</div>
                        <div id="chart-pelajar"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-title">JUMLAH PENDIDIK PER KECAMATAN</div>
                        <div id="chart-pendidik"></div>
                    </div>
                </div>
            </div>

            {{-- Chart Sekolah Negeri vs Swasta --}}
            <div class="chart-card">
                <div class="chart-title">JUMLAH SEKOLAH NEGERI VS SWASTA PER KECAMATAN</div>
                <div id="chart-sekolah"></div>
            </div>

            {{-- Chart Total Sekolah --}}
            <div class="chart-card">
                <div class="chart-title">TOTAL SEKOLAH PER KECAMATAN</div>
                <div id="chart-total-sekolah"></div>
            </div>

            <div class="sumber">Sumber: {{ $summary->sumber }}</div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    var namaKecamatan   = {!! json_encode($perKecamatan->pluck('kecamatan.nama_kecamatan')) !!};
    var jumlahPelajar   = {!! json_encode($perKecamatan->pluck('jumlah_pelajar')->map(fn($v) => (int)$v)) !!};
    var jumlahPendidik  = {!! json_encode($perKecamatan->pluck('jumlah_pendidik')->map(fn($v) => (int)$v)) !!};
    var jumlahNegeri    = {!! json_encode($perKecamatan->pluck('jumlah_sekolah_negeri')->map(fn($v) => (int)$v)) !!};
    var jumlahSwasta    = {!! json_encode($perKecamatan->pluck('jumlah_sekolah_swasta')->map(fn($v) => (int)$v)) !!};
    var totalSekolah    = jumlahNegeri.map((n, i) => n + jumlahSwasta[i]);

    // Chart Pelajar
    new ApexCharts(document.querySelector("#chart-pelajar"), {
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        series: [{ name: 'Pelajar', data: jumlahPelajar }],
        xaxis: { categories: namaKecamatan, labels: { style: { fontSize: '10px' } } },
        colors: ['#4caf50'],
        dataLabels: { enabled: true, style: { fontSize: '9px' } },
        plotOptions: { bar: { borderRadius: 3 } },
    }).render();

    // Chart Pendidik
    new ApexCharts(document.querySelector("#chart-pendidik"), {
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        series: [{ name: 'Pendidik', data: jumlahPendidik }],
        xaxis: { categories: namaKecamatan, labels: { style: { fontSize: '10px' } } },
        colors: ['#9c27b0'],
        dataLabels: { enabled: true, style: { fontSize: '9px' } },
        plotOptions: { bar: { borderRadius: 3 } },
    }).render();

    // Chart Sekolah Negeri vs Swasta (grouped bar)
    new ApexCharts(document.querySelector("#chart-sekolah"), {
        chart: { type: 'bar', height: 320, toolbar: { show: false } },
        series: [
            { name: 'Negeri', data: jumlahNegeri },
            { name: 'Swasta', data: jumlahSwasta },
        ],
        xaxis: { categories: namaKecamatan, labels: { style: { fontSize: '10px' } } },
        colors: ['#1976d2', '#f57c00'],
        dataLabels: { enabled: true, style: { fontSize: '9px' } },
        plotOptions: { bar: { borderRadius: 3, groupPadding: 0.1 } },
        legend: { position: 'top' },
    }).render();

    // Chart Total Sekolah
    new ApexCharts(document.querySelector("#chart-total-sekolah"), {
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        series: [{ name: 'Total Sekolah', data: totalSekolah }],
        xaxis: { categories: namaKecamatan, labels: { style: { fontSize: '10px' } } },
        colors: ['#ffbf00'],
        dataLabels: { enabled: true, style: { fontSize: '9px' } },
        plotOptions: { bar: { borderRadius: 3 } },
    }).render();
</script>
@endpush
