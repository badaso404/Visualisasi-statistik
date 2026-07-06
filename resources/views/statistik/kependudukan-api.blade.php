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
    .stat-header-wrap { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
    .stat-header {
        background: #ffbf00; color: white; text-align: center;
        padding: 14px; border-radius: 8px; font-weight: 700;
        font-size: 18px; letter-spacing: 1px; flex: 1; min-width: 260px;
    }
    .badge-uji { background:#fff3cd; color:#856404; border:1px solid #ffe08a;
        font-size:11px; font-weight:700; padding:4px 10px; border-radius:20px; }
    .chart-card { background: #fff; border: 1px solid #eee; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
    .chart-card .chart-title { font-size: 13px; font-weight: 600; color: #555; letter-spacing: 1px; margin-bottom: 16px; }
    .stat-summary-card {
        background: #f9f9f9; border: 1px solid #eee; border-radius: 8px;
        padding: 16px 24px; display: flex; align-items: center; gap: 16px;
    }
    .stat-summary-card .card-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; flex-shrink: 0;
    }
    .stat-summary-card .value { font-size: 26px; font-weight: 700; color: #333; }
    .stat-summary-card .label { font-size: 12px; font-weight: 600; color: #888; letter-spacing: 1px; }
    .filter-select {
        border: 2px solid #ffbf00; border-radius: 6px; background: #fff;
        color: #b8860b; font-weight: 700; font-size: 14px; padding: 8px 12px; cursor: pointer;
    }
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
                <a class="nav-link active" href="{{ route('statistik.kependudukan') }}"><i class="fa fa-users"></i> Kependudukan</a>
                <a class="nav-link" href="{{ route('statistik.pendidikan') }}"><i class="fa fa-graduation-cap"></i> Pendidikan</a>
                <a class="nav-link" href="{{ route('statistik.kesehatan') }}"><i class="fa fa-plus-circle"></i> Kesehatan</a>
            </nav>
        </div>

        {{-- KONTEN --}}
        <div class="statistik-content">
            <div class="stat-header-wrap">
                <div class="stat-header">KEPENDUDUKAN {{ $kota }} {{ $tahun }}</div>
                <span class="badge-uji">UJI COBA · API Satu Data</span>

                <form method="GET" action="{{ route('statistik.kependudukan-api') }}" class="d-flex gap-2">
                    <select name="tahun" class="filter-select" onchange="this.form.submit()">
                        @foreach($availableTahun as $t)
                            <option value="{{ $t }}" {{ (string)$t === (string)$tahun ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            @if($inflated)
            <div class="alert alert-warning py-2 px-3 mb-3" style="font-size:13px; border-radius:8px;">
                <i class="fa fa-exclamation-triangle"></i>
                <b>Perhatian:</b> data tahun 2013 pada sumber mengalami inflasi (~1,7×), jadi angka absolutnya tidak akurat. Pilih tahun 2014 ke atas untuk angka yang wajar.
            </div>
            @endif

            {{-- Summary --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stat-summary-card">
                        <div class="card-icon" style="background:#e3f0ff;"><i class="fa fa-male" style="color:#2196f3;"></i></div>
                        <div><div class="label">LAKI-LAKI</div><div class="value">{{ number_format($totalL) }}</div></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-summary-card">
                        <div class="card-icon" style="background:#fde8f5;"><i class="fa fa-female" style="color:#e91e8c;"></i></div>
                        <div><div class="label">PEREMPUAN</div><div class="value">{{ number_format($totalP) }}</div></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-summary-card">
                        <div class="card-icon" style="background:#fff8e1;"><i class="fa fa-users" style="color:#ffbf00;"></i></div>
                        <div><div class="label">TOTAL PENDUDUK</div><div class="value">{{ number_format($totalL + $totalP) }}</div></div>
                    </div>
                </div>
            </div>

            {{-- Chart per kecamatan & kelurahan (mirip modul kependudukan) --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="chart-title mb-0">POPULASI PENDUDUK KELURAHAN</div>
                            <button id="btn-back" onclick="backToKecamatan()"
                                style="display:none; font-size:11px; padding:4px 10px; border:1px solid #ccc; border-radius:4px; background:#f9f9f9; cursor:pointer;">
                                ← Kembali
                            </button>
                        </div>
                        <div id="chart-kelurahan"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-title">POPULASI PENDUDUK KECAMATAN</div>
                        <div id="chart-kecamatan"></div>
                    </div>
                </div>
            </div>

            {{-- Piramida --}}
            <div class="chart-card">
                <div class="chart-title">PIRAMIDA PENDUDUK BERDASARKAN KELOMPOK USIA & JENIS KELAMIN</div>
                @if($totalL + $totalP === 0)
                    <p class="text-muted text-center py-5">Data tidak tersedia untuk kombinasi ini.</p>
                @else
                    <div id="chart-piramida"></div>
                @endif
            </div>

            <div class="sumber">Sumber: Portal Satu Data Jakarta (ws.jakarta.go.id) — data mentah dinormalisasi otomatis.</div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
@if($totalL + $totalP > 0)
    var labels    = {!! json_encode($labels) !!};
    var dataLaki  = {!! json_encode($laki) !!};      // negatif
    var dataPr    = {!! json_encode($perempuan) !!};

    var fmt = function(v) { return Math.abs(v).toLocaleString('id-ID'); };

    var chart = new ApexCharts(document.querySelector("#chart-piramida"), {
        chart: { type: 'bar', height: 480, stacked: true, toolbar: { show: false } },
        series: [
            { name: 'Laki-laki', data: dataLaki },
            { name: 'Perempuan', data: dataPr },
        ],
        colors: ['#2196f3', '#e91e8c'],
        plotOptions: { bar: { horizontal: true, barHeight: '80%', borderRadius: 2 } },
        dataLabels: { enabled: false },
        xaxis: {
            categories: labels,
            labels: { formatter: fmt },
            title: { text: 'Jumlah Penduduk (jiwa)' },
        },
        yaxis: { title: { text: 'Kelompok Usia' } },
        tooltip: { shared: false, y: { formatter: fmt } },
        legend: { position: 'top' },
        title: { text: '◄ Laki-laki   |   Perempuan ►', align: 'center', style: { fontSize: '12px', color: '#999' } }
    });
    chart.render();
@endif
</script>

<script>
    // ===== Chart per kecamatan & drill-down kelurahan =====
    var namaKecamatan = {!! json_encode($perKecamatan->pluck('nama')) !!};
    var popKecamatan  = {!! json_encode($perKecamatan->pluck('jumlah')->map(fn($v) => (int)$v)) !!};
    var kelurahanPerKecamatan = {!! json_encode($kelurahanPerKecamatan) !!};

    var warnaMap = {
        'CENGKARENG'        : '#2196f3',
        'KALI DERES'        : '#e91e8c',
        'KEBON JERUK'       : '#ff9800',
        'KEMBANGAN'         : '#4caf50',
        'TAMBORA'           : '#8bc34a',
        'GROGOL PETAMBURAN' : '#9c27b0',
        'PALMERAH'          : '#f44336',
        'TAMAN SARI'        : '#00bcd4',
    };
    var warnaChart = namaKecamatan.map(function(n){ return warnaMap[n.toUpperCase()] || '#ccc'; });

    // Chart Kelurahan (drill-down)
    var chartKelurahan = new ApexCharts(document.querySelector("#chart-kelurahan"), {
        chart: { type: 'bar', height: 340, toolbar: { show: false } },
        series: [{ name: 'Penduduk', data: [] }],
        xaxis: { categories: [], labels: { style: { fontSize: '10px' } } },
        colors: ['#26a0fc'],
        dataLabels: { enabled: false },
        plotOptions: { bar: { borderRadius: 3, horizontal: true } },
        noData: { text: 'Klik kecamatan di sebelah kanan →', style: { fontSize: '13px', color: '#999' } }
    });
    chartKelurahan.render();

    // Tampilan awal: kelurahan terpadat dari setiap kecamatan
    function showTopKelurahan() {
        var lbl = [], dt = [], col = [];
        namaKecamatan.forEach(function(nama) {
            var k = kelurahanPerKecamatan[nama];
            if (!k || !k.data || !k.data.length) return;
            var maxIdx = 0;
            for (var i = 1; i < k.data.length; i++) if (k.data[i] > k.data[maxIdx]) maxIdx = i;
            lbl.push(k.labels[maxIdx]); dt.push(k.data[maxIdx]);
            col.push(warnaMap[nama.toUpperCase()] || '#26a0fc');
        });
        chartKelurahan.updateOptions({
            series: [{ name: 'Penduduk', data: dt }],
            xaxis: { categories: lbl },
            colors: col,
            plotOptions: { bar: { borderRadius: 3, horizontal: true, distributed: true } },
            legend: { show: false },
            title: { text: 'Kelurahan Terpadat per Kecamatan', align: 'left', style: { fontSize: '12px', fontWeight: 600 } }
        });
    }
    showTopKelurahan();

    // Chart Kecamatan
    var chartKecamatan = new ApexCharts(document.querySelector("#chart-kecamatan"), {
        chart: {
            type: 'bar', height: 340, toolbar: { show: false },
            events: {
                dataPointSelection: function(e, ctx, cfg) { drillDown(namaKecamatan[cfg.dataPointIndex]); }
            }
        },
        series: [{ name: 'Penduduk', data: popKecamatan }],
        xaxis: { categories: namaKecamatan, labels: { style: { fontSize: '10px' } } },
        colors: warnaChart,
        dataLabels: { enabled: false },
        plotOptions: { bar: { borderRadius: 3, distributed: true } },
        legend: { show: false },
        title: { text: '👆 Klik bar untuk lihat kelurahan', align: 'center', style: { fontSize: '11px', color: '#999' } }
    });
    chartKecamatan.render();

    function drillDown(namaKec) {
        var data = kelurahanPerKecamatan[namaKec];
        if (!data) return;
        var warna = warnaMap[namaKec.toUpperCase()] || '#26a0fc';
        chartKelurahan.updateOptions({
            series: [{ name: 'Penduduk', data: data.data }],
            xaxis: { categories: data.labels },
            colors: [warna],
            plotOptions: { bar: { borderRadius: 3, horizontal: true, distributed: false } },
            legend: { show: false },
            title: { text: 'Kelurahan - ' + namaKec, align: 'left', style: { fontSize: '12px', fontWeight: 600 } }
        });
        chartKecamatan.updateOptions({
            title: { text: '← Sedang melihat: ' + namaKec, align: 'center', style: { fontSize: '11px', color: '#999' } }
        });
        document.getElementById('btn-back').style.display = 'inline-block';
    }

    function backToKecamatan() {
        showTopKelurahan();
        chartKecamatan.updateOptions({
            title: { text: '👆 Klik bar untuk lihat kelurahan', align: 'center', style: { fontSize: '11px', color: '#999' } }
        });
        document.getElementById('btn-back').style.display = 'none';
    }
</script>
@endpush
