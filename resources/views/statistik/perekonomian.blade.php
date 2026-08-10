@extends('landing-page.layout.app')
@section('page_title', __('perekonomian.page_title') . ' - Jakarta Barat')

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
    /* Dua grafik bersebelahan disamakan tingginya; 17 kategori lapangan usaha
       menuntut ruang vertikal lebih, dan grafik tren mengikuti agar barisnya rata. */
    .chart-box-duo { height:520px; }

    /* Kartu ringkasan indikator */
    .ekon-card { background:#fafafa; border-radius:12px; padding:14px; text-align:center; height:100%; }
    .ekon-icon {
        width:48px; height:48px; border-radius:12px; display:flex;
        align-items:center; justify-content:center; margin:0 auto 8px;
        font-size:22px; color:#fff;
    }
    .ekon-icon.ic-blue   { background:#2a78d6; }
    .ekon-icon.ic-orange { background:#eb6834; }
    .ekon-icon.ic-green  { background:#008300; }
    .ekon-icon.ic-violet { background:#4a3aa7; }
    .ekon-label {
        font-size:11px; color:#888; font-weight:600;
        letter-spacing:1px; text-transform:uppercase;
    }
    .ekon-value { font-size:22px; font-weight:700; color:#333; line-height:1.15; }
    .ekon-sub { font-size:11px; color:#aaa; margin-top:2px; }
    .tren-up   { color:#008300; font-weight:700; }
    .tren-down { color:#e34948; font-weight:700; }

    .sumber { text-align:right; font-size:12px; color:#999; margin-top:10px; }

    @media (max-width: 768px) {
        .statistik-wrapper  { flex-direction: column; padding: 20px 0; gap: 16px; }
        .stat-header        { font-size: 15px; padding: 12px; }
        .chart-box-duo      { height: 460px; }
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
            <div class="stat-header">{{ __('perekonomian.header', ['tahun' => $tahun]) }}</div>
            <div class="dropdown-tahun">
                <div class="dropdown-tahun-btn" id="dropdownTahunBtn">
                    <i class="fa fa-calendar"></i>
                    {{ $tahun }}
                    <span class="arrow">&#9660;</span>
                </div>
                <div class="dropdown-tahun-menu" id="dropdownTahunMenu">
                    @foreach($availableTahun as $t)
                    <a href="{{ route('statistik.perekonomian', ['tahun' => $t]) }}"
                       class="{{ (int) $t === (int) $tahun ? 'active' : '' }}">{{ $t }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- RINGKASAN INDIKATOR --}}
        <div class="chart-card">
            <div class="row g-2">
                <div class="col-6 col-md-3">
                    <div class="ekon-card">
                        <div class="ekon-icon ic-blue"><i class="fa fa-sack-dollar"></i></div>
                        <div class="ekon-label">{{ __('perekonomian.card_adhb') }}</div>
                        <div class="ekon-value">Rp {{ nf($summary->pdrb_adhb / 1000000, 2) }} T</div>
                        <div class="ekon-sub">{{ __('perekonomian.card_adhb_sub') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="ekon-card">
                        <div class="ekon-icon ic-orange"><i class="fa fa-scale-balanced"></i></div>
                        <div class="ekon-label">{{ __('perekonomian.card_adhk') }}</div>
                        <div class="ekon-value">Rp {{ nf($summary->pdrb_adhk / 1000000, 2) }} T</div>
                        <div class="ekon-sub">
                            {{ __('perekonomian.card_adhk_sub') }}
                            @if($tren !== null)
                                &middot;
                                <span class="{{ $tren >= 0 ? 'tren-up' : 'tren-down' }}">
                                    <i class="fa fa-arrow-{{ $tren >= 0 ? 'up' : 'down' }}"></i>
                                    {{ nf(abs($tren), 2) }}%
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="ekon-card">
                        <div class="ekon-icon ic-green"><i class="fa fa-chart-line"></i></div>
                        <div class="ekon-label">{{ __('perekonomian.card_tumbuh') }}</div>
                        <div class="ekon-value {{ $summary->laju_pertumbuhan < 0 ? 'tren-down' : '' }}">
                            {{ nf($summary->laju_pertumbuhan, 2) }}%
                        </div>
                        <div class="ekon-sub">{{ __('perekonomian.card_tumbuh_sub') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="ekon-card">
                        <div class="ekon-icon ic-violet"><i class="fa fa-tags"></i></div>
                        <div class="ekon-label">{{ __('perekonomian.card_deflator') }}</div>
                        <div class="ekon-value">{{ $deflator !== null ? nf($deflator, 2) : '-' }}</div>
                        <div class="ekon-sub">{{ __('perekonomian.card_deflator_sub') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- GRAFIK UTAMA: tren antar-tahun & struktur tahun terpilih --}}
        <div class="row g-2">
            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">{{ __('perekonomian.chart_tren_title') }}</div>
                    <div class="chart-hint">
                        {{ __('perekonomian.chart_tren_hint', ['dari' => $riwayat->first()->tahun, 'sampai' => $riwayat->last()->tahun]) }}
                    </div>
                    <div id="chart-tren-pdrb" class="chart-box-duo"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">{{ __('perekonomian.chart_sektor_title', ['tahun' => $tahun]) }}</div>
                    <div class="chart-hint">{{ __('perekonomian.chart_sektor_hint') }}</div>
                    <div id="chart-sektor-distribusi" class="chart-box-duo"></div>
                </div>
            </div>
        </div>

        {{-- TABEL SEKTOR --}}
        <div class="chart-card">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:8px;">
                <div class="chart-title" style="margin-bottom:0;">{{ __('perekonomian.table_sektor_title', ['tahun' => $tahun]) }}</div>
                @include('statistik.partials.unduh-tabel', [
                    'target' => '#tabel-lapangan-usaha',
                    'nama'   => __('perekonomian.table_sektor_file', ['tahun' => $tahun]),
                ])
            </div>
            <div class="table-responsive">
                <table class="table table-sm" id="tabel-lapangan-usaha" data-unduh-angka="{{ app()->getLocale() }}">
                    <thead>
                        <tr>
                            <th>{{ __('perekonomian.col_kategori') }}</th>
                            <th>{{ __('perekonomian.col_sektor') }}</th>
                            <th class="text-end">{{ __('perekonomian.col_adhb') }}</th>
                            <th class="text-end">{{ __('perekonomian.col_distribusi') }}</th>
                            <th class="text-end">{{ __('perekonomian.col_tumbuh') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sektorUtama as $row)
                        <tr>
                            <td>{{ $row->kategori }}</td>
                            <td>{{ $row->nama_sektor }}</td>
                            <td class="text-end">{{ nf($row->adhb / 1000, 0) }}</td>
                            <td class="text-end">{{ nf($row->distribusi, 2) }}%</td>
                            <td class="text-end {{ $row->laju_pertumbuhan < 0 ? 'tren-down' : '' }}">
                                {{ nf($row->laju_pertumbuhan, 2) }}%
                            </td>
                        </tr>
                        @endforeach

                        @if($sektorLainnya)
                        <tr class="text-muted">
                            <td></td>
                            <td>{{ __('perekonomian.row_lainnya', ['jumlah' => $sektorLainnya['jumlah_sektor']]) }}</td>
                            <td class="text-end">{{ nf($sektorLainnya['adhb'] / 1000, 0) }}</td>
                            <td class="text-end">{{ nf($sektorLainnya['distribusi'], 2) }}%</td>
                            <td class="text-end {{ ($sektorLainnya['laju_pertumbuhan'] ?? 0) < 0 ? 'tren-down' : '' }}">
                                {{ $sektorLainnya['laju_pertumbuhan'] !== null
                                    ? nf($sektorLainnya['laju_pertumbuhan'], 2) . '%*'
                                    : '—' }}
                            </td>
                        </tr>
                        @endif

                        <tr class="fw-bold" style="background:#fff8e1;">
                            <td></td>
                            <td>{{ __('perekonomian.row_total') }}</td>
                            <td class="text-end">{{ nf($summary->pdrb_adhb / 1000, 0) }}</td>
                            {{-- Baris total selalu 100%; lewat nf() supaya pemisah desimalnya ikut bahasa. --}}
                            <td class="text-end">{{ nf(100, 2) }}%</td>
                            <td class="text-end">{{ nf($summary->laju_pertumbuhan, 2) }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if($sektorLainnya && $sektorLainnya['laju_pertumbuhan'] !== null)
            <div class="chart-hint" style="margin:4px 0 0;">
                {{ __('perekonomian.catatan_lainnya') }}
            </div>
            @endif
        </div>

        {{-- RINGKASAN ANTAR-TAHUN --}}
        <div class="chart-card">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:8px;">
                <div class="chart-title" style="margin-bottom:0;">{{ __('perekonomian.table_tahun_title') }}</div>
                @include('statistik.partials.unduh-tabel', [
                    'target' => '#tabel-perekonomian-tahun',
                    'nama'   => __('perekonomian.table_tahun_file'),
                ])
            </div>
            <div class="table-responsive">
                <table class="table table-sm" id="tabel-perekonomian-tahun" data-unduh-angka="{{ app()->getLocale() }}">
                    <thead>
                        <tr>
                            <th>{{ __('perekonomian.col_tahun') }}</th>
                            <th class="text-end">{{ __('perekonomian.col_adhb_t') }}</th>
                            <th class="text-end">{{ __('perekonomian.col_adhk_t') }}</th>
                            <th class="text-end">{{ __('perekonomian.col_tumbuh') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatTabel as $row)
                        <tr @if((int)$row->tahun === (int)$tahun) class="fw-bold" style="background:#fff8e1;" @endif>
                            <td>{{ $row->tahun }}</td>
                            <td class="text-end">{{ nf($row->pdrb_adhb / 1000000, 2) }}</td>
                            <td class="text-end">{{ nf($row->pdrb_adhk / 1000000, 2) }}</td>
                            <td class="text-end {{ $row->laju_pertumbuhan < 0 ? 'tren-down' : '' }}">
                                {{ nf($row->laju_pertumbuhan, 2) }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="sumber">
            {{ __('perekonomian.source', ['sumber' => $summary->sumber]) }}
        </div>

    </div>
</div>
</div>
@endsection


@push('scripts')
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
// ── Data dari server ──────────────────────────────────────────
// PDRB disimpan dalam juta rupiah; grafik memakai triliun agar sumbu terbaca.
const tahunLabels = {!! json_encode($riwayat->pluck('tahun')->map(fn($v)=>(string)$v)) !!};
const trAdhb      = {!! json_encode($riwayat->pluck('pdrb_adhb')->map(fn($v)=>round($v/1000000, 2))) !!};
const trAdhk      = {!! json_encode($riwayat->pluck('pdrb_adhk')->map(fn($v)=>round($v/1000000, 2))) !!};
// Sektor sudah terurut menurun dari controller, jadi batang terbesar otomatis di atas.
const sekNama     = {!! json_encode($sektor->pluck('nama_sektor')) !!};
const sekDistrib  = {!! json_encode($sektor->pluck('distribusi')->map(fn($v)=>(float)$v)) !!};
const sekAdhb     = {!! json_encode($sektor->pluck('adhb')->map(fn($v)=>round($v/1000000, 2))) !!};

// ── Util angka (format Indonesia) ─────────────────────────────
// Pemisah ribuan/desimal ikut bahasa aktif (lihat helper nf()).
const idID      = '{{ locale_angka_js() }}';
const fmt2      = v => Number(v).toLocaleString(idID, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
const fmtRpT    = v => 'Rp ' + fmt2(v) + ' T';
const fmtPersen = v => fmt2(v) + '%';

const BIRU = '#2a78d6', ORANYE = '#eb6834';

// Sisakan ruang di ujung sumbu agar label batang terpanjang tidak terpotong,
// lalu bulatkan ke kelipatan `langkah` supaya angka pada sumbu tetap bulat.
const batasSumbu = (arr, faktor, langkah) =>
    Math.ceil(Math.max(...arr) * faktor / langkah) * langkah;

// ── Tren PDRB: dua seri, satuan sama (triliun) → satu sumbu ────
new ApexCharts(document.querySelector('#chart-tren-pdrb'), {
    chart: {
        type: 'line', height: 520, toolbar: { show: false }, fontFamily: 'inherit',
        animations: { enabled: true, easing: 'easeinout', speed: 550 }
    },
    series: [
        { name: @json(__('perekonomian.series_adhb')), data: trAdhb },
        { name: @json(__('perekonomian.series_adhk')), data: trAdhk }
    ],
    colors: [BIRU, ORANYE],
    stroke: { curve: 'smooth', width: 2 },
    markers: { size: 4, strokeWidth: 0, hover: { size: 7 } },
    dataLabels: { enabled: false },
    xaxis: {
        categories: tahunLabels,
        labels: { style: { fontSize: '11px', colors: '#888' } },
        axisBorder: { show: false }, axisTicks: { show: false }
    },
    yaxis: { labels: { formatter: fmt2, style: { fontSize: '10px', colors: '#aaa' } } },
    legend: { position: 'bottom', fontSize: '11px', markers: { width: 11, height: 11, radius: 3 } },
    grid: { borderColor: '#f5f5f5', strokeDashArray: 4 },
    tooltip: { shared: true, intersect: false, y: { formatter: fmtRpT } }
}).render();

// ── Struktur ekonomi: 17 kategori = tugas magnitudo, satu warna ──
// (17 seri warna berbeda akan melanggar batas palet kategorikal 8 warna)
new ApexCharts(document.querySelector('#chart-sektor-distribusi'), {
    chart: {
        type: 'bar', height: 520, toolbar: { show: false }, fontFamily: 'inherit',
        animations: { enabled: true, easing: 'easeinout', speed: 550 }
    },
    series: [{ name: @json(__('perekonomian.series_distribusi')), data: sekDistrib }],
    colors: [BIRU],
    plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '68%', dataLabels: { position: 'top' } } },
    dataLabels: {
        enabled: true, formatter: fmtPersen, offsetX: 30, textAnchor: 'start',
        style: { fontSize: '10px', fontWeight: 700, colors: ['#666'] }
    },
    xaxis: {
        categories: sekNama,
        // min 0 wajib: tanpa itu ApexCharts menurunkan batas bawah ke angka negatif,
        // padahal distribusi tidak mungkin di bawah nol. Tanpa tickAmount, tiap
        // satu persen dapat satu tick sehingga label sumbu saling menempel.
        min: 0,
        max: batasSumbu(sekDistrib, 1.15, 5),
        tickAmount: 5,
        labels: { formatter: fmtPersen, style: { fontSize: '10px', colors: '#aaa' } },
        axisBorder: { show: false }, axisTicks: { show: false }
    },
    // Kolom separuh lebar: nama sektor dipotong lebih pendek, nama utuhnya
    // tetap terbaca lewat tooltip dan tabel di bawah.
    yaxis: { labels: { style: { fontSize: '10px', colors: '#888' }, maxWidth: 150 } },
    legend: { show: false },
    grid: { borderColor: '#f5f5f5', strokeDashArray: 4 },
    tooltip: {
        y: {
            formatter: function (val, opts) {
                return fmtPersen(val) + ' — ' + fmtRpT(sekAdhb[opts.dataPointIndex]);
            }
        }
    }
}).render();
</script>
@endpush
