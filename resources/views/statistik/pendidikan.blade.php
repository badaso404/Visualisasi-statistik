@extends('landing-page.layout.app')

@push('styles')
<style>
    .statistik-wrapper {
        display:flex;
        gap:20px;
        padding:30px 0;
    }

    .statistik-sidebar {
        width:220px;
        flex-shrink:0;
    }

    .statistik-sidebar .nav-link {
        display:flex;
        align-items:center;
        gap:10px;
        padding:10px 14px;
        border-radius:8px;
        color:#555;
        font-weight:500;
        margin-bottom:4px;
    }

    .statistik-sidebar .nav-link:hover {
        background:#f0f0f0;
        color:#ffbf00;
    }

    .statistik-sidebar .nav-link.active {
        background:#ffbf00;
        color:white;
    }

    .statistik-content {
        flex:1;
        min-width:0;
    }

    .stat-header {
        background:linear-gradient(135deg,#ffbf00,#ff9900);
        color:white;
        text-align:center;
        padding:12px;
        border-radius:10px;
        font-weight:700;
        font-size:16px;
        margin-bottom:18px;
    }

    .chart-card {
        background:white;
        border:1px solid #eee;
        border-radius:12px;
        padding:14px;
        margin-bottom:14px;
    }

    .chart-title {
        font-size:12px;
        font-weight:700;
        color:#555;
        margin-bottom:10px;
        display:flex;
        align-items:center;
        gap:8px;
    }

    .education-card {
        background:#fafafa;
        border-radius:12px;
        padding:12px;
        text-align:center;
        transition:.3s;
    }

    .education-card:hover {
        transform:translateY(-3px);
        box-shadow:0 5px 15px rgba(0,0,0,.08);
    }

    .education-icon {
        width:38px;
        height:38px;
        border-radius:50%;
        display:flex;
        align-items:center;
        justify-content:center;
        margin:auto;
        margin-bottom:6px;
        font-size:20px;
        background:#fff3cd;
    }

    .education-label {
        font-size:11px;
        color:#777;
        font-weight:600;
    }

    .education-value {
        font-size:20px;
        font-weight:800;
        color:#333;
    }

    .total-badge {
        background:#fff3cd;
        border:1px solid #ffc107;
        padding:2px 8px;
        border-radius:6px;
        font-size:11px;
    }

    .sumber {
        text-align:right;
        font-size:12px;
        color:#999;
        margin-top:10px;
    }

    .chart-box {
        height:220px;
    }
</style>
@endpush


@section('content')

<div class="container-fluid px-4">

<div class="statistik-wrapper">

    {{-- SIDEBAR --}}
    <div class="statistik-sidebar">
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

            <a class="nav-link active" href="{{ route('statistik.pendidikan') }}">
                <i class="fa fa-graduation-cap"></i> Pendidikan
            </a>

            <a class="nav-link" href="{{ route('statistik.kesehatan') }}">
                <i class="fa fa-plus-circle"></i> Kesehatan
            </a>

            <a class="nav-link" href="{{ route('statistik.bencana') }}">
                <i class="fa fa-house-flood-water"></i> Bencana
            </a>

        </nav>
    </div>


    {{-- CONTENT --}}
    <div class="statistik-content">

        <div class="stat-header">
            PENDIDIKAN JAKARTA BARAT 2024
        </div>

        {{-- APM APK --}}
        <div class="row g-2 mb-2">

            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">📊 Angka Partisipasi Murni (APM)</div>

                    <div class="row g-2">
                        <div class="col-4">
                            <div class="education-card">
                                <div class="education-icon">📘</div>
                                <div class="education-label">SD</div>
                                <div class="education-value">{{ $summary->apm_sd_mi }}</div>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="education-card">
                                <div class="education-icon">🎒</div>
                                <div class="education-label">SMP</div>
                                <div class="education-value">{{ $summary->apm_smp_mts }}</div>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="education-card">
                                <div class="education-icon">🎓</div>
                                <div class="education-label">SMA</div>
                                <div class="education-value">{{ $summary->apm_sma_smk_man }}</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">📊 Angka Partisipasi Kasar (APK)</div>

                    <div class="row g-2">
                        <div class="col-4">
                            <div class="education-card">
                                <div class="education-icon">📘</div>
                                <div class="education-label">SD</div>
                                <div class="education-value">{{ $summary->apk_sd_mi }}</div>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="education-card">
                                <div class="education-icon">🎒</div>
                                <div class="education-label">SMP</div>
                                <div class="education-value">{{ $summary->apk_smp_mts }}</div>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="education-card">
                                <div class="education-icon">🎓</div>
                                <div class="education-label">SMA</div>
                                <div class="education-value">{{ $summary->apk_sma_smk_man }}</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>


        {{-- CHART PELAJAR & PENDIDIK --}}
        <div class="row g-2">

            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">👨‍🎓 Pelajar</div>
                    <div id="chart-pelajar" class="chart-box"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">👨‍🏫 Pendidik</div>
                    <div id="chart-pendidik" class="chart-box"></div>
                </div>
            </div>

        </div>


        {{-- 🔥 FIX: NEGERI vs SWASTA + TOTAL SEJAJAR --}}
        <div class="row g-2">

            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">🏫 Negeri vs Swasta</div>
                    <div id="chart-sekolah" class="chart-box"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">🏫 Total Sekolah</div>
                    <div id="chart-total-sekolah" class="chart-box"></div>
                </div>
            </div>

        </div>


        {{-- DATA KECAMATAN --}}
        <div class="chart-card">
            <div class="chart-title">📍 Data Per Kecamatan</div>

            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Kecamatan</th>
                            <th>Pelajar</th>
                            <th>Pendidik</th>
                            <th>Negeri</th>
                            <th>Swasta</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($perKecamatan as $row)
                        <tr>
                            <td>📍 {{ $row->kecamatan->nama_kecamatan }}</td>
                            <td>{{ number_format($row->jumlah_pelajar) }}</td>
                            <td>{{ number_format($row->jumlah_pendidik) }}</td>
                            <td>{{ $row->jumlah_sekolah_negeri }}</td>
                            <td>{{ $row->jumlah_sekolah_swasta }}</td>
                            <td>
                                <span class="total-badge">
                                    {{ $row->jumlah_sekolah_negeri + $row->jumlah_sekolah_swasta }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="sumber">
            Sumber: {{ $summary->sumber }}
        </div>

    </div>
</div>
</div>
@endsection


@push('scripts')
<script>

const nama = {!! json_encode($perKecamatan->pluck('kecamatan.nama_kecamatan')) !!};
const pelajar = {!! json_encode($perKecamatan->pluck('jumlah_pelajar')->map(fn($v)=>(int)$v)) !!};
const pendidik = {!! json_encode($perKecamatan->pluck('jumlah_pendidik')->map(fn($v)=>(int)$v)) !!};
const negeri = {!! json_encode($perKecamatan->pluck('jumlah_sekolah_negeri')->map(fn($v)=>(int)$v)) !!};
const swasta = {!! json_encode($perKecamatan->pluck('jumlah_sekolah_swasta')->map(fn($v)=>(int)$v)) !!};

const total = negeri.map((n,i)=>n+swasta[i]);

function opt(data,color){
    return {
        chart:{type:'bar',height:220,toolbar:{show:false}},
        series:[{data:data}],
        xaxis:{categories:nama,labels:{style:{fontSize:'10px'}}},
        colors:[color],
        dataLabels:{enabled:false}
    }
}

new ApexCharts(document.querySelector("#chart-pelajar"), opt(pelajar,'#22c55e')).render();
new ApexCharts(document.querySelector("#chart-pendidik"), opt(pendidik,'#a855f7')).render();

new ApexCharts(document.querySelector("#chart-sekolah"), {
    chart:{type:'bar',height:220,stacked:true,toolbar:{show:false}},
    series:[
        {name:'Negeri',data:negeri},
        {name:'Swasta',data:swasta}
    ],
    xaxis:{categories:nama},
    colors:['#3b82f6','#f97316'],
    dataLabels:{enabled:false}
}).render();

new ApexCharts(document.querySelector("#chart-total-sekolah"), opt(total,'#f59e0b')).render();

</script>
@endpush