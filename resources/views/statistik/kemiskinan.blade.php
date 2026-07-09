@extends('landing-page.layout.app')

@push('styles')
<style>
    .statistik-wrapper { display:flex; gap:24px; padding:40px 0; }
    .statistik-content { flex:1; min-width:0; }

    .stat-header {
        background:#ffbf00; color:white; text-align:center;
        padding:14px; border-radius:8px; font-weight:700;
        font-size:18px; letter-spacing:1px; margin-bottom:18px;
    }

    /* Dropdown tahun (selaras dengan modul lain) */
    .stat-header-wrap { display:flex; align-items:center; gap:12px; margin-bottom:24px; }
    .stat-header-wrap .stat-header { flex:1; margin-bottom:0; }
    .dropdown-tahun { position:relative; flex-shrink:0; }
    .dropdown-tahun-btn {
        display:flex; align-items:center; gap:8px;
        border:2px solid #ffbf00; border-radius:6px; background:#fff;
        color:#b8860b; font-weight:700; font-size:14px;
        padding:6px 12px; cursor:pointer; white-space:nowrap; user-select:none;
    }
    .dropdown-tahun-btn .arrow { font-size:10px; transition:transform .2s; }
    .dropdown-tahun-btn.open .arrow { transform:rotate(180deg); }
    .dropdown-tahun-menu {
        display:none; position:absolute; top:calc(100% + 4px); right:0;
        background:#fff; border:2px solid #ffbf00; border-radius:6px;
        min-width:100%; z-index:9999; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.1);
    }
    .dropdown-tahun-menu.show { display:block; }
    .dropdown-tahun-menu a {
        display:block; padding:8px 16px; color:#555;
        font-weight:600; font-size:14px; text-decoration:none; transition:background .15s;
    }
    .dropdown-tahun-menu a:hover { background:#fff8e1; color:#b8860b; }
    .dropdown-tahun-menu a.active { background:#ffbf00; color:#fff; }

    .chart-card {
        background:white; border:1px solid #eee; border-radius:12px;
        padding:14px; margin-bottom:14px;
    }
    .chart-title {
        font-size:12px; font-weight:700; color:#555; margin-bottom:10px;
        display:flex; align-items:center; gap:8px;
    }
    .chart-hint { font-size:10px; color:#aaa; font-weight:500; margin:-6px 0 8px; }
    .chart-box { height:250px; }

    /* Kartu ringkasan indikator */
    .poverty-card { background:#fafafa; border-radius:12px; padding:14px; text-align:center; height:100%; }
    .poverty-icon {
        width:48px; height:48px; border-radius:12px; display:flex;
        align-items:center; justify-content:center; margin:0 auto 8px;
        font-size:22px; color:#fff;
    }
    .poverty-icon.ic-red    { background:#e34948; }
    .poverty-icon.ic-orange { background:#eb6834; }
    .poverty-icon.ic-amber  { background:#eda100; }
    .poverty-icon.ic-violet { background:#4a3aa7; }
    .poverty-icon.ic-blue   { background:#2a78d6; }
    .poverty-label {
        font-size:11px; color:#888; font-weight:600;
        letter-spacing:1px; text-transform:uppercase;
    }
    .poverty-value { font-size:22px; font-weight:700; color:#333; line-height:1.15; }
    .poverty-sub { font-size:11px; color:#aaa; margin-top:2px; }
    .tren-up   { color:#e34948; font-weight:700; }
    .tren-down { color:#008300; font-weight:700; }

    .sumber { text-align:right; font-size:12px; color:#999; margin-top:10px; }

    @media (max-width: 768px) {
        .statistik-wrapper { flex-direction: column; padding: 20px 0; gap: 16px; }
        .stat-header       { font-size: 15px; padding: 12px; }
        .table-responsive  { -webkit-overflow-scrolling: touch; }
    }
</style>
@endpush


@section('content')

<div class="container-fluid px-4">
<div class="statistik-wrapper">

    @include('statistik.partials.sidebar')

    {{-- CONTENT --}}
    <div class="statistik-content">

        <div class="stat-header-wrap">
            <div class="stat-header">KEMISKINAN JAKARTA BARAT {{ $tahun }}</div>
            <div class="dropdown-tahun">
                <div class="dropdown-tahun-btn" id="dropdownTahunBtn">
                    <i class="fa fa-calendar"></i>
                    {{ $tahun }}
                    <span class="arrow">&#9660;</span>
                </div>
                <div class="dropdown-tahun-menu" id="dropdownTahunMenu">
                    @foreach($availableTahun as $t)
                    <a href="{{ route('statistik.kemiskinan', ['tahun' => $t]) }}"
                       class="{{ (int) $t === (int) $tahun ? 'active' : '' }}">{{ $t }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        @if($summary)
        {{-- RINGKASAN INDIKATOR --}}
        <div class="chart-card">
            <div class="chart-title">Ringkasan Indikator</div>
            <div class="row g-2">
                <div class="col-6 col-md">
                    <div class="poverty-card">
                        <div class="poverty-icon ic-red"><i class="fa fa-people-group"></i></div>
                        <div class="poverty-label">Penduduk Miskin</div>
                        <div class="poverty-value">{{ number_format($summary->jumlah_penduduk_miskin, 0, ',', '.') }}</div>
                        <div class="poverty-sub">
                            jiwa
                            @if($tren !== null)
                                &middot;
                                <span class="{{ $tren > 0 ? 'tren-up' : 'tren-down' }}">
                                    <i class="fa fa-arrow-{{ $tren > 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($tren) }}%
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md">
                    <div class="poverty-card">
                        <div class="poverty-icon ic-orange"><i class="fa fa-percent"></i></div>
                        <div class="poverty-label">Persentase</div>
                        <div class="poverty-value">{{ $summary->persentase_penduduk_miskin }}%</div>
                        <div class="poverty-sub">dari total penduduk</div>
                    </div>
                </div>
                <div class="col-6 col-md">
                    <div class="poverty-card">
                        <div class="poverty-icon ic-amber"><i class="fa fa-money-bill-wave"></i></div>
                        <div class="poverty-label">Garis Kemiskinan</div>
                        <div class="poverty-value" style="font-size:16px;">Rp {{ number_format($summary->garis_kemiskinan, 0, ',', '.') }}</div>
                        <div class="poverty-sub">per kapita/bulan</div>
                    </div>
                </div>
                <div class="col-6 col-md">
                    <div class="poverty-card">
                        <div class="poverty-icon ic-violet"><i class="fa fa-arrow-down-wide-short"></i></div>
                        <div class="poverty-label">Indeks Kedalaman (P1)</div>
                        <div class="poverty-value">{{ $summary->indeks_kedalaman }}</div>
                        <div class="poverty-sub">jarak ke garis miskin</div>
                    </div>
                </div>
                <div class="col-6 col-md">
                    <div class="poverty-card">
                        <div class="poverty-icon ic-blue"><i class="fa fa-chart-simple"></i></div>
                        <div class="poverty-label">Indeks Keparahan (P2)</div>
                        <div class="poverty-value">{{ $summary->indeks_keparahan }}</div>
                        <div class="poverty-sub">ketimpangan antar-miskin</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CHART PER KECAMATAN --}}
        <div class="row g-2">
            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">Penduduk Miskin per Kecamatan</div>
                    <div class="chart-hint">Klik salah satu batang untuk menyorot kecamatan</div>
                    <div id="chart-miskin" class="chart-box"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">Penerima Bantuan Sosial per Kecamatan</div>
                    <div class="chart-hint">Klik salah satu batang untuk menyorot kecamatan</div>
                    <div id="chart-bantuan" class="chart-box"></div>
                </div>
            </div>
        </div>

        <div class="row g-2">
            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">Persentase Penduduk Miskin per Kecamatan (%)</div>
                    <div class="chart-hint">Klik salah satu batang untuk menyorot kecamatan</div>
                    <div id="chart-persen" class="chart-box"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">Keluarga Miskin per Kecamatan (KK)</div>
                    <div class="chart-hint">Klik salah satu batang untuk menyorot kecamatan</div>
                    <div id="chart-keluarga" class="chart-box"></div>
                </div>
            </div>
        </div>

        {{-- DATA KECAMATAN --}}
        <div class="chart-card">
            <div class="chart-title">Data Per Kecamatan</div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Kecamatan</th>
                            <th>Penduduk Miskin</th>
                            <th>Keluarga Miskin (KK)</th>
                            <th>Penerima Bantuan</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($perKecamatan as $row)
                        <tr>
                            <td>{{ $row->kecamatan->nama_kecamatan }}</td>
                            <td>{{ number_format($row->jumlah_penduduk_miskin, 0, ',', '.') }}</td>
                            <td>{{ number_format($row->jumlah_keluarga_miskin, 0, ',', '.') }}</td>
                            <td>{{ number_format($row->penerima_bantuan, 0, ',', '.') }}</td>
                            <td>{{ $row->persentase }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="sumber">
            Sumber: {{ $summary->sumber }}
        </div>
        @else
        <div class="chart-card text-center text-muted py-5">
            Belum ada data kemiskinan untuk ditampilkan.
        </div>
        @endif

    </div>
</div>
</div>
@endsection


@push('scripts')
@include('statistik.partials.warna-kecamatan')
<script>
    // Dropdown tahun (selaras dengan modul lain)
    (function () {
        var btn  = document.getElementById('dropdownTahunBtn');
        var menu = document.getElementById('dropdownTahunMenu');
        if (!btn) return;
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            btn.classList.toggle('open');
            menu.classList.toggle('show');
        });
        document.addEventListener('click', function () {
            btn.classList.remove('open');
            menu.classList.remove('show');
        });
    })();
</script>
@if($summary)
<script>
const nama     = {!! json_encode($perKecamatan->pluck('kecamatan.nama_kecamatan')) !!};
const miskin   = {!! json_encode($perKecamatan->pluck('jumlah_penduduk_miskin')->map(fn($v)=>(int)$v)) !!};
const keluarga = {!! json_encode($perKecamatan->pluck('jumlah_keluarga_miskin')->map(fn($v)=>(int)$v)) !!};
const bantuan  = {!! json_encode($perKecamatan->pluck('penerima_bantuan')->map(fn($v)=>(int)$v)) !!};
const persen   = {!! json_encode($perKecamatan->pluck('persentase')->map(fn($v)=>(float)$v)) !!};

// ── Util angka ────────────────────────────────────────────────
const idID = 'id-ID';
const fmt  = v => Number(v).toLocaleString(idID);

// ── Bar distribusi + interaktif (klik untuk menyorot) ──
function distributedBar(sel, data, label, fmtY) {
    // Warna per kecamatan (konsisten antar modul) — categories = `nama`
    const base   = nama.map(function (n) { return window.warnaKecamatan(n); });
    let   active = null;
    const fY = fmtY || fmt;

    const chart = new ApexCharts(document.querySelector(sel), {
        chart: {
            type: 'bar', height: 250, toolbar: { show: false },
            fontFamily: 'inherit',
            animations: { enabled: true, easing: 'easeinout', speed: 550 },
            events: {
                dataPointSelection: function (e, ctx, cfg) {
                    const i = cfg.dataPointIndex;
                    if (active === i) {
                        active = null;
                        chart.updateOptions({ colors: base, fill: { colors: base } });
                        return;
                    }
                    active = i;
                    const faded = base.map(function (c, j) { return j === i ? c : c + '38'; });
                    chart.updateOptions({ colors: faded, fill: { colors: faded } });
                },
                click: function (e, ctx, cfg) {
                    if (cfg.dataPointIndex === undefined || cfg.dataPointIndex < 0) {
                        active = null;
                        chart.updateOptions({ colors: base, fill: { colors: base } });
                    }
                }
            }
        },
        series: [{ name: label, data: data }],
        xaxis: {
            categories: nama,
            labels: { rotate: -30, rotateAlways: true, trim: false, style: { fontSize: '10px', colors: '#888' } },
            axisBorder: { show: false }, axisTicks: { show: false }
        },
        yaxis: { labels: { formatter: fY, style: { fontSize: '10px', colors: '#aaa' } } },
        colors: base, fill: { colors: base },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '58%', distributed: true, dataLabels: { position: 'top' } } },
        dataLabels: {
            enabled: true, formatter: fY, offsetY: -16,
            style: { fontSize: '9px', fontWeight: 700, colors: ['#666'] }
        },
        legend: { show: false },
        grid: { borderColor: '#f5f5f5', strokeDashArray: 4 },
        states: { hover: { filter: { type: 'lighten', value: 0.06 } }, active: { filter: { type: 'none' } } },
        tooltip: { y: { formatter: fY } }
    });
    chart.render();
    return chart;
}

const fmtPersen = v => fmt(v) + '%';

distributedBar('#chart-miskin',   miskin,   'Penduduk Miskin');
distributedBar('#chart-bantuan',  bantuan,  'Penerima Bantuan');
distributedBar('#chart-persen',   persen,   'Persentase', fmtPersen);
distributedBar('#chart-keluarga', keluarga, 'Keluarga Miskin');
</script>
@endif
@endpush
