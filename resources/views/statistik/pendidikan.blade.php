@extends('landing-page.layout.app')

@push('styles')
<style>
    .statistik-wrapper {
        display:flex;
        gap:24px;
        padding:40px 0;
    }

    .statistik-sidebar {
        width:220px;
        flex-shrink:0;
    }

    .statistik-sidebar .nav-link {
        display:flex;
        align-items:center;
        gap:10px;
        padding:12px 16px;
        border-radius:8px;
        color:#555;
        font-weight:500;
        margin-bottom:4px;
        transition:all 0.2s;
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
        background:#ffbf00;
        color:white;
        text-align:center;
        padding:14px;
        border-radius:8px;
        font-weight:700;
        font-size:18px;
        letter-spacing:1px;
        margin-bottom:18px;
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
    }

    .education-icon {
        width:48px;
        height:48px;
        border-radius:12px;
        display:flex;
        align-items:center;
        justify-content:center;
        margin:auto;
        margin-bottom:6px;
        font-size:22px;
        background:#2a78d6;   /* WARNA LAMA: #ffbf00 */
        color:#fff;
    }
    /* Icon warna-warni per jenjang */
    .education-icon.ic-blue   { background:#2a78d6; }
    .education-icon.ic-green  { background:#008300; }
    .education-icon.ic-violet { background:#4a3aa7; }

    .education-label {
        font-size:12px;
        color:#888;
        font-weight:600;
        letter-spacing:1px;
        text-transform:uppercase;
    }

    .education-value {
        font-size:22px;
        font-weight:700;
        color:#333;
        line-height:1.15;
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
        height:250px;
    }

    .chart-hint {
        font-size:10px;
        color:#aaa;
        font-weight:500;
        margin:-6px 0 8px;
    }

    /* ── Responsive (tablet & HP) ──────────────────────────────── */
    @media (max-width: 768px) {
        .statistik-wrapper  { flex-direction: column; padding: 20px 0; gap: 16px; }
        .statistik-sidebar  { width: 100%; }
        .statistik-sidebar .nav {
            flex-direction: row !important; flex-wrap: nowrap;
            overflow-x: auto; gap: 6px; padding-bottom: 4px; -webkit-overflow-scrolling: touch;
        }
        .statistik-sidebar .nav-link { white-space: nowrap; margin-bottom: 0; }
        .stat-header        { font-size: 15px; padding: 12px; }
        .table-responsive   { -webkit-overflow-scrolling: touch; }
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
            <div class="stat-header">PENDIDIKAN JAKARTA BARAT {{ $tahun }}</div>
            <div class="dropdown-tahun">
                <div class="dropdown-tahun-btn" id="dropdownTahunBtn">
                    <i class="fa fa-calendar"></i>
                    {{ $tahun }}
                    <span class="arrow">&#9660;</span>
                </div>
                <div class="dropdown-tahun-menu" id="dropdownTahunMenu">
                    @foreach($availableTahun as $t)
                    <a href="{{ route('statistik.pendidikan', ['tahun' => $t]) }}"
                       class="{{ (int) $t === (int) $tahun ? 'active' : '' }}">{{ $t }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- APM APK --}}
        <div class="row g-2 mb-2">

            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">Angka Partisipasi Murni (APM)</div>

                    <div class="row g-2">
                        <div class="col-4">
                            <div class="education-card">
                                <div class="education-icon ic-blue"><i class="fa fa-book"></i></div>
                                <div class="education-label">SD</div>
                                <div class="education-value">{{ $summary->apm_sd_mi }}</div>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="education-card">
                                <div class="education-icon ic-green"><i class="fa fa-book-open"></i></div>
                                <div class="education-label">SMP</div>
                                <div class="education-value">{{ $summary->apm_smp_mts }}</div>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="education-card">
                                <div class="education-icon ic-violet"><i class="fa fa-graduation-cap"></i></div>
                                <div class="education-label">SMA</div>
                                <div class="education-value">{{ $summary->apm_sma_smk_man }}</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">Angka Partisipasi Kasar (APK)</div>

                    <div class="row g-2">
                        <div class="col-4">
                            <div class="education-card">
                                <div class="education-icon ic-blue"><i class="fa fa-book"></i></div>
                                <div class="education-label">SD</div>
                                <div class="education-value">{{ $summary->apk_sd_mi }}</div>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="education-card">
                                <div class="education-icon ic-green"><i class="fa fa-book-open"></i></div>
                                <div class="education-label">SMP</div>
                                <div class="education-value">{{ $summary->apk_smp_mts }}</div>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="education-card">
                                <div class="education-icon ic-violet"><i class="fa fa-graduation-cap"></i></div>
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
                    <div class="chart-title">Pelajar</div>
                    <div class="chart-hint">Klik salah satu batang untuk menyorot kecamatan</div>
                    <div id="chart-pelajar" class="chart-box"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">Pendidik</div>
                    <div class="chart-hint">Klik salah satu batang untuk menyorot kecamatan</div>
                    <div id="chart-pendidik" class="chart-box"></div>
                </div>
            </div>

        </div>


        {{-- NEGERI vs SWASTA + TOTAL SEKOLAH --}}
        <div class="row g-2">

            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">Negeri vs Swasta</div>
                    <div id="chart-sekolah" class="chart-box"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">Total Sekolah</div>
                    <div class="chart-hint">Klik salah satu batang untuk menyorot kecamatan</div>
                    <div id="chart-total-sekolah" class="chart-box"></div>
                </div>
            </div>

        </div>


        {{-- DATA KECAMATAN --}}
        <div class="chart-card">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:8px;">
                <div class="chart-title" style="margin-bottom:0;">Data Per Kecamatan</div>
                @include('statistik.partials.unduh-tabel', [
                    'target' => '#tabel-pendidikan-kecamatan',
                    'nama'   => 'pendidikan-per-kecamatan-' . $tahun,
                ])
            </div>

            <div class="table-responsive">
                <table class="table table-sm" id="tabel-pendidikan-kecamatan" data-unduh-angka="en">
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
                            <td>{{ $row->kecamatan->nama_kecamatan }}</td>
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
<script>

const nama = {!! json_encode($perKecamatan->pluck('kecamatan.nama_kecamatan')) !!};
const pelajar = {!! json_encode($perKecamatan->pluck('jumlah_pelajar')->map(fn($v)=>(int)$v)) !!};
const pendidik = {!! json_encode($perKecamatan->pluck('jumlah_pendidik')->map(fn($v)=>(int)$v)) !!};
const negeri = {!! json_encode($perKecamatan->pluck('jumlah_sekolah_negeri')->map(fn($v)=>(int)$v)) !!};
const swasta = {!! json_encode($perKecamatan->pluck('jumlah_sekolah_swasta')->map(fn($v)=>(int)$v)) !!};

const total = negeri.map((n,i)=>n+swasta[i]);

// ── Util angka & warna ────────────────────────────────────────
const idID = 'id-ID';
const fmt  = v => Number(v).toLocaleString(idID);

// ── Palet kategorikal warna-warni (colorblind-safe, tervalidasi) ──
const CAT_COLORS = ['#2a78d6','#1baf7a','#eda100','#008300','#4a3aa7','#e34948','#e87ba4','#eb6834'];
function lerpColor(a, b, t) {
    const ah = parseInt(a.slice(1), 16), bh = parseInt(b.slice(1), 16);
    const ar = ah >> 16, ag = (ah >> 8) & 0xff, ab = ah & 0xff;
    const br = bh >> 16, bg = (bh >> 8) & 0xff, bb = bh & 0xff;
    const rr = Math.round(ar + (br - ar) * t);
    const rg = Math.round(ag + (bg - ag) * t);
    const rb = Math.round(ab + (bb - ab) * t);
    return '#' + ((1 << 24) + (rr << 16) + (rg << 8) + rb).toString(16).slice(1);
}
// Satu warna khas per batang (urut tetap, warna-warni)
function gradientColors(data, hue) {
    return data.map(function (v, i) { return CAT_COLORS[i % CAT_COLORS.length]; });
}

/* ── WARNA LAMA (gradient monokrom per-hue, muda → tua per nilai) — disimpan untuk referensi ──
const PALETTE = {
    biru:   { light: '#E2ECFA', dark: '#34527A' },
    hijau:  { light: '#E4F3E7', dark: '#2F7D4F' },
    oranye: { light: '#FCEBD6', dark: '#C77A1A' },
};
function gradientColorsLama(data, hue) {
    const pal = PALETTE[hue] || PALETTE.biru;
    const min = Math.min.apply(null, data), max = Math.max.apply(null, data);
    return data.map(function (v) {
        const t = max > min ? (v - min) / (max - min) : 0.5;
        const step = Math.round(t * 4) / 4;   // 5 tingkatan agar mudah dibedakan
        return lerpColor(pal.light, pal.dark, step);
    });
}
*/

// ── Bar distribusi gradien + interaktif (klik untuk menyorot) ──
function distributedBar(sel, data, label, hue) {
    // Warna per kecamatan (konsisten antar modul) — categories = `nama`
    const base   = nama.map(function (n) { return window.warnaKecamatan(n); });
    let   active = null;

    const chart = new ApexCharts(document.querySelector(sel), {
        chart: {
            type: 'bar', height: 250, toolbar: { show: false },
            fontFamily: 'inherit',
            animations: { enabled: true, easing: 'easeinout', speed: 550 },
            events: {
                dataPointSelection: function (e, ctx, cfg) {
                    const i = cfg.dataPointIndex;
                    if (active === i) {           // klik lagi → reset
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
        yaxis: { labels: { formatter: fmt, style: { fontSize: '10px', colors: '#aaa' } } },
        colors: base, fill: { colors: base },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '58%', distributed: true, dataLabels: { position: 'top' } } },
        dataLabels: {
            enabled: true, formatter: fmt, offsetY: -16,
            style: { fontSize: '9px', fontWeight: 700, colors: ['#666'] }
        },
        legend: { show: false },
        grid: { borderColor: '#f5f5f5', strokeDashArray: 4 },
        states: { hover: { filter: { type: 'lighten', value: 0.06 } }, active: { filter: { type: 'none' } } },
        tooltip: { y: { formatter: fmt } }
    });
    chart.render();
    return chart;
}

distributedBar('#chart-pelajar',       pelajar, 'Pelajar',       'biru');
distributedBar('#chart-pendidik',      pendidik, 'Pendidik',      'hijau');
distributedBar('#chart-total-sekolah', total,    'Total Sekolah', 'oranye');

// ── Negeri vs Swasta (AREA tumpang-tindih — tipe yang dianimasikan ApexCharts saat update) ──
// Tidak stacked: tiap area diukur dari nol, jadi nilai lebih besar tampak lebih tinggi.
// Bar tak bisa animasi saat toggle; area/line bisa. Klik legend dialihkan ke updateSeries
// agar dynamicAnimation benar-benar terpicu → transisi mulus.
const sekolahOff = [false, false];   // 0=Negeri, 1=Swasta

const chartSekolah = new ApexCharts(document.querySelector("#chart-sekolah"), {
    chart: {
        type: 'area', height: 250, stacked: false, toolbar: { show: false },
        fontFamily: 'inherit',
        animations: {
            enabled: true, easing: 'easeinout', speed: 550,
            animateGradually: { enabled: false },
            dynamicAnimation: { enabled: true, speed: 700 }   // transisi toggle mengalir
        },
        events: {
            legendClick: function (ctx, seriesIndex) {
                sekolahOff[seriesIndex] = !sekolahOff[seriesIndex];
                chartSekolah.updateSeries([
                    { name: 'Negeri', data: sekolahOff[0] ? negeri.map(function(){return 0;}) : negeri },
                    { name: 'Swasta', data: sekolahOff[1] ? swasta.map(function(){return 0;}) : swasta }
                ]);   // tanpa arg kedua = animate:true → dynamicAnimation aktif
                const items = document.querySelectorAll('#chart-sekolah .apexcharts-legend-series');
                if (items[seriesIndex]) items[seriesIndex].style.opacity = sekolahOff[seriesIndex] ? 0.4 : 1;
            }
        }
    },
    series: [
        { name: 'Negeri', data: negeri },
        { name: 'Swasta', data: swasta }
    ],
    xaxis: {
        categories: nama,
        labels: { rotate: -30, rotateAlways: true, trim: false, style: { fontSize: '10px', colors: '#888' } },
        axisBorder: { show: false }, axisTicks: { show: false }
    },
    yaxis: { min: 0, max: Math.max.apply(null, negeri.concat(swasta)),   // kunci skala (nilai terbesar antar-seri)
             forceNiceScale: true,
             labels: { formatter: function (v) { return fmt(Math.round(v)); },   // bulatkan → tanpa koma
                       style: { fontSize: '10px', colors: '#aaa' } } },
    colors: ['#2a78d6', '#eb6834'],
    stroke: { curve: 'smooth', width: 2 },
    fill: { type: 'gradient', gradient: { opacityFrom: 0.55, opacityTo: 0.15 } },
    markers: { size: 3, strokeWidth: 0, hover: { size: 5 } },
    dataLabels: { enabled: false },
    legend: {
        position: 'bottom', fontSize: '11px', markers: { width: 11, height: 11, radius: 3 },
        onItemClick: { toggleDataSeries: false }   // matikan toggle bawaan (yang snap)
    },
    grid: { borderColor: '#f5f5f5', strokeDashArray: 4 },
    tooltip: { y: { formatter: fmt } }
});
chartSekolah.render();

</script>
@endpush