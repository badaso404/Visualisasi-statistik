@extends('landing-page.layout.app')

@push('styles')
<style>
    /* ── Layout ─────────────────────────────────────────────── */
    .kes-wrapper { display:flex; gap:24px; padding:32px 0; }
    .kes-sidebar { width:220px; flex-shrink:0; }
    .kes-content { flex:1; min-width:0; }

    /* ── Sidebar ────────────────────────────────────────────── */
    .kes-sidebar .nav-link {
        display:flex; align-items:center; gap:10px;
        padding:11px 16px; border-radius:8px; color:#555;
        font-weight:500; margin-bottom:4px; transition:all .2s;
        text-decoration:none; font-size:14px;
    }
    .kes-sidebar .nav-link:hover { background:#f0f0f0; color:#d4a017; }
    .kes-sidebar .nav-link.active { background:#d4a017; color:#fff; }
    .kes-sidebar .nav-link i { width:18px; text-align:center; }

    /* ── Page header ────────────────────────────────────────── */
    .stat-header-wrap {
        display: flex; align-items: center; gap: 12px; margin-bottom: 24px;
    }
    .stat-header {
        flex: 1;
        background: #ffbf00; color: white; text-align: center;
        padding: 14px; border-radius: 8px; font-weight: 700;
        font-size: 18px; letter-spacing: 1px;
    }

    /* ── Stat cards (top row) ───────────────────────────────── */
    .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:16px; }
    .stat-card {
        background:#fff; border:1px solid #ebebeb; border-radius:12px;
        padding:20px 22px 18px; position:relative; overflow:hidden;
    }
    .stat-card .sc-badge {
        font-size:11px; font-weight:600; padding:3px 9px; border-radius:20px;
        position:absolute; top:16px; right:16px; display:flex; align-items:center; gap:4px;
    }
    .sc-badge.up   { background:#e8f5e9; color:#2e7d32; }
    .sc-badge.down { background:#fce4ec; color:#c62828; }
    .sc-badge.info { background:#e3f2fd; color:#1565c0; }
    .sc-badge.warn { background:#fff8e1; color:#f57f17; }
    .sc-badge.ok   { background:#e8f5e9; color:#2e7d32; }
    .stat-card .sc-icon {
        width:38px; height:38px; border-radius:9px;
        display:flex; align-items:center; justify-content:center;
        font-size:17px; margin-bottom:12px;
    }
    .sc-icon.yellow { background:#fff8e1; color:#d4a017; }
    .sc-icon.green  { background:#e8f5e9; color:#2e7d32; }
    .sc-icon.blue   { background:#e3f2fd; color:#1565c0; }
    .sc-icon.orange { background:#fff3e0; color:#e65100; }
    .sc-icon.teal   { background:#e0f2f1; color:#00695c; }
    .sc-icon.purple { background:#f3e5f5; color:#6a1b9a; }
    .stat-card .sc-label { font-size:10px; font-weight:700; color:#9e9e9e; letter-spacing:.8px; text-transform:uppercase; margin-bottom:4px; }
    .stat-card .sc-value { font-size:28px; font-weight:800; color:#1a1a1a; line-height:1; margin-bottom:6px; }
    .stat-card .sc-desc  { font-size:11px; color:#aaa; }

    /* ── Middle section: tenaga + fasilitas side-by-side ───── */
    .mid-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }

    /* ── Panel card ─────────────────────────────────────────── */
    .panel-card {
        background:#fff; border:1px solid #ebebeb; border-radius:12px; padding:22px;
    }
    .panel-card .pc-title    { font-size:15px; font-weight:700; color:#1a1a1a; margin:0 0 2px; }
    .panel-card .pc-subtitle { font-size:11px; color:#aaa; margin:0 0 18px; }
    .panel-card .pc-header   { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:4px; }
    .panel-card .pc-badge    { font-size:10px; font-weight:600; padding:3px 9px; border-radius:20px; background:#e8f5e9; color:#2e7d32; }
    .panel-card .pc-badge.blue { background:#e3f2fd; color:#1565c0; }

    /* ── Horizontal bar (tenaga) ─────────────────────────────── */
    .hbar-row { margin-bottom:14px; }
    .hbar-row:last-child { margin-bottom:0; }
    .hbar-label { display:flex; justify-content:space-between; font-size:13px; margin-bottom:5px; }
    .hbar-label .hb-name  { color:#333; font-weight:500; }
    .hbar-label .hb-value { color:#333; font-weight:700; }
    .hbar-track { height:8px; background:#f0f0f0; border-radius:6px; overflow:hidden; }
    .hbar-fill  { height:100%; border-radius:6px; background:linear-gradient(90deg,#d4a017,#f5c842); transition:width .6s ease; }

    /* ── Fasilitas ranking ───────────────────────────────────── */
    .fas-row { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
    .fas-row:last-child { margin-bottom:0; }
    .fas-kec { font-size:12px; color:#555; font-weight:500; width:100px; flex-shrink:0; }
    .fas-track { flex:1; height:9px; background:#f0f0f0; border-radius:6px; overflow:hidden; }
    .fas-fill { height:100%; border-radius:6px; background:linear-gradient(90deg,#1a6b3c,#2e9c59); }
    .fas-unit { font-size:12px; color:#333; font-weight:700; white-space:nowrap; margin-left:4px; }

    .fas-link { display:block; text-align:center; margin-top:18px; font-size:12px; color:#d4a017; font-weight:600; text-decoration:none; }
    .fas-link:hover { text-decoration:underline; }

    /* ── Detail grouped bar chart ───────────────────────────── */
    .detail-card { background:#fff; border:1px solid #ebebeb; border-radius:12px; padding:22px; margin-bottom:16px; }
    .detail-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:4px; }
    .detail-title  { font-size:15px; font-weight:700; color:#1a1a1a; }
    .detail-sub    { font-size:11px; color:#aaa; margin-bottom:16px; }
    .detail-legend { display:flex; gap:16px; font-size:11px; color:#555; }
    .leg-dot { width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:5px; }

    /* ── Table card ──────────────────────────────────────────── */
    .table-card { background:#fff; border:1px solid #ebebeb; border-radius:12px; padding:22px; margin-bottom:16px; }
    .table-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
    .table-title  { font-size:15px; font-weight:700; color:#1a1a1a; margin:0; }
    .table-sub    { font-size:11px; color:#aaa; margin:2px 0 0; }
    .btn-filter   {
        font-size:12px; padding:6px 14px; border:1px solid #ddd;
        border-radius:8px; background:#fff; color:#555; cursor:pointer;
        display:flex; align-items:center; gap:6px;
    }
    .btn-filter:hover { background:#f5f5f5; }

    .kes-table { width:100%; border-collapse:collapse; }
    .kes-table th {
        font-size:11px; font-weight:700; color:#9e9e9e; text-transform:uppercase;
        letter-spacing:.5px; padding:10px 14px; border-bottom:1px solid #f0f0f0;
        text-align:left;
    }
    .kes-table td { padding:12px 14px; font-size:13px; color:#333; border-bottom:1px solid #f9f9f9; }
    .kes-table tr:last-child td { border-bottom:none; }
    .kes-table tr:hover td { background:#fafafa; }
    .kes-table td.td-num { font-weight:600; }

    .status-badge {
        display:inline-flex; align-items:center; gap:5px;
        font-size:11px; font-weight:600; padding:3px 10px; border-radius:20px;
    }
    .status-badge::before { content:''; width:7px; height:7px; border-radius:50%; display:inline-block; }
    .status-optimal::before { background:#2e7d32; }
    .status-optimal { background:#e8f5e9; color:#2e7d32; }
    .status-stabil::before   { background:#d4a017; }
    .status-stabil   { background:#fff8e1; color:#d4a017; }
    .status-kritis::before   { background:#c62828; }
    .status-kritis   { background:#fce4ec; color:#c62828; }

    .btn-detail {
        font-size:12px; color:#d4a017; font-weight:600; text-decoration:none;
        padding:4px 0;
    }
    .btn-detail:hover { text-decoration:underline; }

    /* ── Footer ──────────────────────────────────────────────── */
    .kes-footer { font-size:11px; color:#bbb; text-align:right; margin-top:8px; }
</style>
@endpush

@section('content')
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

            {{-- Page Title --}}
            <div class="stat-header-wrap">
                <div class="stat-header">KESEHATAN JAKARTA BARAT 2024</div>
            </div>

            {{-- ── ROW 1: 3 stat cards ─────────────────── --}}
            <div class="stat-grid">
                {{-- Hospital Beds --}}
                <div class="stat-card">
                    <span class="sc-badge up"><i class="fa fa-arrow-trend-up fa-xs"></i> +2.4%</span>
                    <div class="sc-icon yellow"><i class="fa fa-bed-pulse"></i></div>
                    <div class="sc-label">Hospital Beds</div>
                    <div class="sc-value">{{ number_format($summary->jumlah_tempat_tidur_rs) }}</div>
                    <div class="sc-desc">Ketersediaan total TT di RS</div>
                </div>
                {{-- Imunisasi --}}
                <div class="stat-card">
                    <span class="sc-badge ok"><i class="fa fa-check fa-xs"></i> Optimal</span>
                    <div class="sc-icon green"><i class="fa fa-syringe"></i></div>
                    <div class="sc-label">Immunization Coverage</div>
                    <div class="sc-value">{{ $summary->cakupan_imunisasi_dasar }}%</div>
                    <div class="sc-desc">Pencapaian target IDL Kelurahan</div>
                </div>
                {{-- Tenaga Kesehatan --}}
                <div class="stat-card">
                    <span class="sc-badge info"><i class="fa fa-user-nurse fa-xs"></i> Aktif</span>
                    <div class="sc-icon blue"><i class="fa fa-stethoscope"></i></div>
                    <div class="sc-label">Tenaga Kesehatan</div>
                    <div class="sc-value">{{ number_format($tenaga->sum('jumlah_total')) }}</div>
                    <div class="sc-desc">Total Dokter, Perawat, Bidan</div>
                </div>
                {{-- Fasilitas Kesehatan --}}
                <div class="stat-card">
                    <span class="sc-badge info"><i class="fa fa-circle-check fa-xs"></i> Stabil</span>
                    <div class="sc-icon teal"><i class="fa fa-hospital"></i></div>
                    <div class="sc-label">Fasilitas Kesehatan</div>
                    <div class="sc-value">{{ number_format($fasilitas->sum('jumlah_total')) }}</div>
                    <div class="sc-desc">RS, Puskesmas, Klinik &amp; Lab</div>
                </div>
            </div>            {{-- ── ROW 3: Tenaga | Fasilitas ───────────── --}}
            <div class="mid-grid">

                {{-- Tenaga Kesehatan per Kecamatan --}}
                <div class="panel-card">
                    <div class="pc-header">
                        <div>
                            <div class="pc-title">Tenaga Kesehatan per Kecamatan</div>
                            <div class="pc-subtitle">Distribusi personel medis aktif</div>
                        </div>
                        <i class="fa fa-ellipsis-vertical" style="color:#ccc;cursor:pointer;"></i>
                    </div>
                    @php $maxTenaga = $tenaga->max('jumlah_total') ?: 1; @endphp
                    @foreach($tenaga as $t)
                    <div class="hbar-row">
                        <div class="hbar-label">
                            <span class="hb-name">{{ $t->kecamatan->nama_kecamatan }}</span>
                            <span class="hb-value">{{ number_format($t->jumlah_total) }}</span>
                        </div>
                        <div class="hbar-track">
                            <div class="hbar-fill" style="width:{{ round($t->jumlah_total / $maxTenaga * 100) }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Fasilitas Kesehatan --}}
                <div class="panel-card">
                    <div class="pc-header">
                        <div>
                            <div class="pc-title">Fasilitas Kesehatan</div>
                            <div class="pc-subtitle">Jumlah unit per kecamatan</div>
                        </div>
                        <span class="pc-badge blue">UPDATE REALTIME</span>
                    </div>
                    @php $maxFasilitas = $fasilitas->max('jumlah_total') ?: 1; @endphp
                    @foreach($fasilitas as $f)
                    <div class="fas-row">
                        <span class="fas-kec">{{ $f->kecamatan->nama_kecamatan }}</span>
                        <div class="fas-track">
                            <div class="fas-fill" style="width:{{ round($f->jumlah_total / $maxFasilitas * 100) }}%"></div>
                        </div>
                        <span class="fas-unit">{{ $f->jumlah_total }} Unit</span>
                    </div>
                    @endforeach
                    
                </div>

            </div>

            {{-- ── Detail Chart (grouped bar) ─────────── --}}
            <div class="detail-card">
                <div class="detail-header">
                    <div>
                        <div class="detail-title">Detail Tenaga dan Fasilitas Kesehatan pada Kecamatan</div>
                        <div class="detail-sub">Perbandingan tenaga Medis vs fasilitas per Wilayah</div>
                    </div>
                    <div class="detail-legend">
                        <span><span class="leg-dot" style="background:#d4a017;"></span>Tenaga Medis</span>
                        <span><span class="leg-dot" style="background:#1a6b3c;"></span>Fasilitas</span>
                    </div>
                </div>
                <div id="chart-detail" style="min-height:280px;"></div>
            </div>

            {{--Fasilitas Tabel --}}
            <div class="table-card">
                <div class="table-header">
                    <div>
                        <p class="table-title">Fasilitas per Kecamatan</p>
                        <p class="table-sub">Detail operasional dan status kesiapan alat</p>
                    </div>
                    
                </div>

                <table class="kes-table">
                    <thead>
                        <tr>
                            <th>Klinik Kesehatan</th>
                            <th>Posyandu</th>
                            <th>Puskesmas</th>
                            <th>Rumah Sakit</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        
                        @foreach($fasilitas as $f)
                       
                        <tr>
                            <td><strong>{{ $f->kecamatan->nama_kecamatan }}</strong></td>
                            <td class="td-num">{{ $f->posyandu ?? '-' }}</td>
                            <td class="td-num">{{ $f->puskesmas ?? '-' }}</td>
                            <td class="td-num">{{ $f->rumah_sakit ?? '-' }}</td>
                            
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="kes-footer">
                Sumber: {{ $summary->sumber ?? 'Dinas Kesehatan Jakarta Barat' }}
            </div>

        </div>{{-- /.kes-content --}}
    </div>{{-- /.kes-wrapper --}}
</div>
@endsection

@push('scripts')
<script>
(function () {
    var kec      = {!! json_encode($tenaga->pluck('kecamatan.nama_kecamatan')->map(fn($v) => \Illuminate\Support\Str::limit($v, 10))) !!};
    var dataTenaga   = {!! json_encode($tenaga->pluck('jumlah_total')->map(fn($v) => (int)$v)) !!};

    // Map fasilitas by kecamatan id so order matches tenaga
    var fasMap = {};
    @foreach($fasilitas as $f)
    fasMap[{{ $f->kecamatan_id }}] = {{ (int)$f->jumlah_total }};
    @endforeach

    var dataFasilitas = {!! json_encode($tenaga->map(fn($t) => (int)($fasilitas->firstWhere('kecamatan_id', $t->kecamatan_id)->jumlah_total ?? 0))) !!};

    new ApexCharts(document.querySelector("#chart-detail"), {
        chart: {
            type: 'bar',
            height: 280,
            toolbar: { show: false },
            fontFamily: 'inherit',
        },
        series: [
            { name: 'Tenaga Medis', data: dataTenaga },
            { name: 'Fasilitas',    data: dataFasilitas },
        ],
        xaxis: {
        categories: kec,
        labels: { style: { fontSize: '12px', colors: '#888' } },
        axisBorder: {  show: true,
        color: '#e0e0e0',
        height: 1,         
        offsetX: 0,
        offsetY: 0,
    },
        axisTicks:  {
        show: true,
        borderType: 'solid',
        color: '#e0e0e0',  
        height: 6,
        offsetX: 0,
        offsetY: 0,
    },
        },
        yaxis: { labels: { style: { fontSize: '11px', colors: '#aaa' } } },
        colors: ['#e6b12dff', '#367751ff'],
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '55%',
                dataLabels: { position: 'top' },
            }
        },
        dataLabels: { enabled: false },
        legend: { show: false },
        grid: { borderColor: '#f5f5f5', strokeDashArray: 4 },
        tooltip: {
            theme: 'light',
            y: { formatter: v => v.toLocaleString('id-ID') }
        },
    }).render();
})();
</script>
@endpush
