@extends('landing-page.layout.app')
@section('page_title', __('overview.page_title') . ' - Jakarta Barat')

@push('styles')
<style>
    .statistik-wrapper { display:flex; gap:24px; padding:40px 0; }
    .statistik-content { flex:1; min-width:0; }

    .stat-header {
        background:#ffbf00; color:white; text-align:center;
        padding:14px; border-radius:8px; font-weight:700;
        font-size:18px; letter-spacing:1px; margin-bottom:18px;
    }

    .chart-card {
        background:white; border:1px solid #eee; border-radius:12px;
        padding:14px; margin-bottom:14px;
    }
    .chart-title {
        font-size:12px; font-weight:700; color:#555; margin-bottom:10px;
        display:flex; align-items:center; gap:8px;
    }
    .chart-hint { font-size:10px; color:#aaa; font-weight:500; margin:-6px 0 8px; }
    /* Bagian tebal dibuat sedikit lebih gelap; #aaa yang ditebalkan saja masih pucat. */
    .chart-hint strong { color:#777; }
    .chart-box { height:300px; }

    /* Kartu indikator lintas modul — seluruh kartu jadi tautan ke modulnya. */
    .ov-card {
        background:#fafafa; border-radius:12px; padding:14px; text-align:center;
        height:100%; display:block; text-decoration:none; color:inherit;
        border:1px solid transparent; transition:all .2s;
    }
    .ov-card:hover { border-color:#ffbf00; background:#fff8e1; transform:translateY(-2px); }
    .ov-icon {
        width:48px; height:48px; border-radius:12px; display:flex;
        align-items:center; justify-content:center; margin:0 auto 8px;
        font-size:22px; color:#fff;
    }
    /* Satu modul = satu warna, diambil dari palet kategorikal tervalidasi. */
    .ov-icon.ic-blue   { background:#2a78d6; }
    .ov-icon.ic-teal   { background:#1baf7a; }
    .ov-icon.ic-green  { background:#008300; }
    .ov-icon.ic-red    { background:#e34948; }
    .ov-icon.ic-amber  { background:#eda100; }
    .ov-icon.ic-violet { background:#4a3aa7; }
    .ov-icon.ic-orange { background:#eb6834; }
    .ov-icon.ic-pink   { background:#e87ba4; }

    .ov-label {
        font-size:11px; color:#888; font-weight:600;
        letter-spacing:1px; text-transform:uppercase;
    }
    .ov-value { font-size:20px; font-weight:700; color:#333; line-height:1.15; }
    .ov-satuan { font-size:12px; font-weight:600; color:#999; }
    .ov-sub { font-size:11px; color:#aaa; margin-top:2px; }
    .ov-tahun {
        display:inline-block; margin-top:6px; font-size:10px; font-weight:700;
        color:#b8860b; background:#fff3cd; border-radius:20px; padding:1px 8px;
    }
    .tren-up   { color:#008300; font-weight:700; }
    .tren-down { color:#e34948; font-weight:700; }

    .sumber { text-align:right; font-size:12px; color:#999; margin-top:10px; }

    @media (max-width: 768px) {
        .statistik-wrapper { flex-direction:column; padding:20px 0; gap:16px; }
        .stat-header       { font-size:15px; padding:12px; }
        .table-responsive  { -webkit-overflow-scrolling:touch; }
    }
</style>
@endpush


@section('content')

<div class="container-fluid px-4">
<div class="statistik-wrapper">

    @include('statistik.partials.sidebar')

    {{-- CONTENT --}}
    <div class="statistik-content">

        <div class="stat-header">{{ __('overview.header') }}</div>

        @if (empty($kartu))
            {{-- Database masih kosong: jangan tampilkan grafik & tabel hampa. --}}
            <div class="chart-card text-center text-muted py-5">
                <div style="font-size:32px; color:#ddd;"><i class="fa fa-chart-simple"></i></div>
                <div class="fw-bold mt-2">{{ __('overview.empty_title') }}</div>
                <div class="chart-hint" style="margin-top:4px;">
                    {{ __('overview.empty_hint') }}
                </div>
            </div>
        @else

        {{-- KARTU INDIKATOR KUNCI LINTAS MODUL --}}
        <div class="chart-card">
            {{-- Tanpa chart-title di atasnya, margin negatif bawaan .chart-hint
                 harus dinolkan agar teksnya tidak menempel ke tepi kartu. --}}
            <div class="chart-hint" style="margin-top:0;">
                {!! __('overview.cards_hint') !!}
            </div>
            <div class="row g-2">
                @foreach ($kartu as $k)
                <div class="col-6 col-md-3">
                    <a class="ov-card" href="{{ route($k['route']) }}">
                        <div class="ov-icon {{ $k['warna'] }}"><i class="fa {{ $k['icon'] }}"></i></div>
                        <div class="ov-label">{{ $k['label'] }}</div>
                        <div class="ov-value">
                            {{ $k['nilai'] }}
                            @if ($k['satuan'])<span class="ov-satuan">{{ $k['satuan'] }}</span>@endif
                        </div>
                        <div class="ov-sub">
                            {!! $k['sub'] !!}
                            @if (!empty($k['tren']))
                                {{-- Untuk kemiskinan, angka turun justru kabar baik. --}}
                                @php
                                    $naik = $k['tren'] >= 0;
                                    $baik = ($k['tren_baik'] ?? 'naik') === 'turun' ? !$naik : $naik;
                                @endphp
                                &middot;
                                <span class="{{ $baik ? 'tren-up' : 'tren-down' }}">
                                    <i class="fa fa-arrow-{{ $naik ? 'up' : 'down' }}"></i>
                                    {{ nf(abs($k['tren']), 2) }}
                                </span>
                            @endif
                        </div>
                        <div class="ov-tahun">{{ $k['tahun'] }}</div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        {{-- GRAFIK — urutannya mengikuti urutan modul di sidebar --}}

        {{-- Kependudukan & Pendidikan --}}
        <div class="row g-2">
            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">{{ __('overview.chart_penduduk_title') }}</div>
                    <div class="chart-hint">{{ __('overview.chart_penduduk_hint') }}</div>
                    <div id="chart-ov-penduduk" class="chart-box"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">
                        {{ __('overview.chart_pendidikan_title') }}
                        @if ($pendidikanIndikator) <span class="ov-tahun">{{ $pendidikanIndikator['tahun'] }}</span> @endif
                    </div>
                    <div class="chart-hint">
                        {{ __('overview.chart_pendidikan_hint') }}
                    </div>
                    <div id="chart-ov-pendidikan" class="chart-box"></div>
                </div>
            </div>
        </div>

        {{-- Kesehatan & Kebencanaan --}}
        <div class="row g-2">
            <div class="col-md-4">
                <div class="chart-card">
                    <div class="chart-title">{{ __('overview.chart_faskes_title') }}</div>
                    <div class="chart-hint">{{ __('overview.chart_faskes_hint') }}</div>
                    <div id="chart-ov-faskes" class="chart-box"></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-card">
                    <div class="chart-title">{{ __('overview.chart_nakes_title') }}</div>
                    <div class="chart-hint">{{ __('overview.chart_nakes_hint') }}</div>
                    <div id="chart-ov-nakes" class="chart-box"></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-card">
                    <div class="chart-title">{{ __('overview.chart_bencana_title') }}</div>
                    <div class="chart-hint">{{ __('overview.chart_bencana_hint') }}</div>
                    <div id="chart-ov-bencana" class="chart-box"></div>
                </div>
            </div>
        </div>

        {{-- Kemiskinan & Perekonomian --}}
        @if (count($trenGabungan['labels']))
        <div class="chart-card">
            <div class="chart-title">
                {!! __('overview.chart_tren_title') !!}
                <span class="ov-tahun">
                    {{ $trenGabungan['labels']->first() }}&ndash;{{ $trenGabungan['labels']->last() }}
                </span>
            </div>
            <div class="chart-hint">
                {{ __('overview.chart_tren_hint') }}
            </div>
            <div id="chart-ov-tren" class="chart-box"></div>
        </div>
        @endif

        {{-- TABEL LINTAS MODUL PER KECAMATAN --}}
        @if ($perKecamatan->isNotEmpty())
        <div class="chart-card">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;">
                <div class="chart-title">{{ __('overview.table_title') }}</div>
                @include('statistik.partials.unduh-tabel', [
                    'target' => '#tabel-overview-kecamatan',
                    'nama'   => __('overview.table_file'),
                ])
            </div>
            <div class="chart-hint">
                {!! __('overview.table_hint') !!}
            </div>
            <div class="table-responsive">
                <table class="table table-sm" id="tabel-overview-kecamatan" data-unduh-angka="{{ app()->getLocale() }}">
                    <thead>
                        <tr>
                            <th>{{ __('overview.col_kecamatan') }}</th>
                            <th class="text-end">{{ __('overview.col_luas') }}</th>
                            <th class="text-end">{{ __('overview.col_penduduk') }}</th>
                            <th class="text-end">{{ __('overview.col_kepadatan') }}</th>
                            <th class="text-end">{{ __('overview.col_pelajar') }}</th>
                            <th class="text-end">{{ __('overview.col_faskes') }}</th>
                            <th class="text-end">{{ __('overview.col_miskin') }}</th>
                            <th class="text-end">{{ __('overview.col_digital') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($perKecamatan as $r)
                        <tr>
                            <td>{{ $r['nama'] }}</td>
                            <td class="text-end">{{ $r['luas'] ? nf($r['luas'], 2) : '—' }}</td>
                            <td class="text-end">{{ $r['penduduk'] ? nf($r['penduduk'], 0) : '—' }}</td>
                            <td class="text-end">{{ $r['kepadatan'] ? nf($r['kepadatan'], 0) : '—' }}</td>
                            <td class="text-end">{{ $r['pelajar'] ? nf($r['pelajar'], 0) : '—' }}</td>
                            <td class="text-end">{{ $r['faskes'] ? nf($r['faskes'], 0) : '—' }}</td>
                            <td class="text-end">{{ $r['miskin'] ? nf($r['miskin'], 0) : '—' }}</td>
                            <td class="text-end">{{ $r['digital'] ? nf($r['digital'], 0) : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="sumber">
            {!! __('overview.source') !!}
        </div>

        @endif

    </div>
</div>
</div>
@endsection


@push('scripts')
@include('statistik.partials.warna-kecamatan')
<script>
// ── Data dari server (urut sesuai modul) ──────────────────────
const ovKecNama   = {!! json_encode($perKecamatan->pluck('nama')) !!};
const ovKecJiwa   = {!! json_encode($perKecamatan->pluck('penduduk')) !!};
const ovJenjang   = {!! json_encode($pendidikanIndikator['jenjang'] ?? []) !!};
const ovApm       = {!! json_encode($pendidikanIndikator['apm'] ?? []) !!};
const ovApk       = {!! json_encode($pendidikanIndikator['apk'] ?? []) !!};
const ovFaskesL   = {!! json_encode($faskesJenis->keys()) !!};
const ovFaskesV   = {!! json_encode($faskesJenis->values()) !!};
const ovNakesL    = {!! json_encode($nakesJenis->keys()) !!};
const ovNakesV    = {!! json_encode($nakesJenis->values()) !!};
const ovBencanaL  = {!! json_encode($bencanaJenis->keys()) !!};
const ovBencanaV  = {!! json_encode($bencanaJenis->values()) !!};
const ovTahun     = {!! json_encode($trenGabungan['labels']) !!};
const ovPdrb      = {!! json_encode($trenGabungan['pdrb']) !!};
const ovMiskin    = {!! json_encode($trenGabungan['miskin']) !!};

// Pemisah ribuan/desimal ikut bahasa aktif: 1.234,5 di Indonesia, 1,234.5 di Inggris.
const idID   = '{{ locale_angka_js() }}';
const fmt0   = v => v === null ? '—' : Number(v).toLocaleString(idID, { maximumFractionDigits: 0 });
const fmt2   = v => v === null ? '—' : Number(v).toLocaleString(idID, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

const BIRU = '#2a78d6', MERAH = '#e34948', ORANYE = '#eb6834';

// Donut komposisi dipakai tiga kali (faskes, nakes, bencana) — bentuknya sama,
// hanya datanya berbeda, jadi konfigurasinya dibuat satu kali di sini.
const donutKomposisi = (el, labels, values, satuan) => {
    if (!labels.length) return;
    new ApexCharts(document.querySelector(el), {
        chart: { type: 'donut', height: 300, fontFamily: 'inherit' },
        series: values,
        labels: labels,
        colors: window.CAT_COLORS,
        dataLabels: { enabled: true, style: { fontSize: '10px', fontWeight: 700 } },
        legend: { position: 'bottom', fontSize: '11px', markers: { width: 11, height: 11, radius: 3 } },
        plotOptions: { pie: { donut: { size: '58%' } } },
        tooltip: { y: { formatter: v => fmt0(v) + ' ' + satuan } }
    }).render();
};

// ── Penduduk per kecamatan ────────────────────────────────────
if (ovKecNama.length) {
    new ApexCharts(document.querySelector('#chart-ov-penduduk'), {
        chart: {
            type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'inherit',
            animations: { enabled: true, easing: 'easeinout', speed: 550 }
        },
        series: [{ name: @json(__('overview.series_penduduk')), data: ovKecJiwa }],
        // Satu warna per kecamatan (bukan per seri) agar konsisten lintas modul.
        colors: ovKecNama.map(window.warnaKecamatan),
        plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '68%', distributed: true } },
        dataLabels: { enabled: false },
        xaxis: {
            categories: ovKecNama,
            labels: { formatter: fmt0, style: { fontSize: '10px', colors: '#aaa' } },
            axisBorder: { show: false }, axisTicks: { show: false }
        },
        yaxis: { labels: { style: { fontSize: '10px', colors: '#888' } } },
        legend: { show: false },
        grid: { borderColor: '#f5f5f5', strokeDashArray: 4 },
        tooltip: { y: { formatter: v => fmt0(v) + ' ' + @json(__('overview.tooltip_jiwa')) } }
    }).render();
}

// ── Pendidikan: APM vs APK per jenjang ────────────────────────
// Dua indikator sejenis pada satuan sama (%), jadi cukup satu sumbu.
if (ovJenjang.length) {
    new ApexCharts(document.querySelector('#chart-ov-pendidikan'), {
        chart: {
            type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'inherit',
            animations: { enabled: true, easing: 'easeinout', speed: 550 }
        },
        series: [
            { name: 'APM', data: ovApm },
            { name: 'APK', data: ovApk }
        ],
        colors: [BIRU, ORANYE],
        plotOptions: { bar: { horizontal: false, borderRadius: 4, columnWidth: '55%' } },
        dataLabels: {
            enabled: true, formatter: v => fmt2(v) + '%',
            offsetY: -18, style: { fontSize: '9px', fontWeight: 700, colors: ['#666'] }
        },
        xaxis: {
            categories: ovJenjang,
            labels: { style: { fontSize: '11px', colors: '#888' } },
            axisBorder: { show: false }, axisTicks: { show: false }
        },
        // APK bisa melewati 100% (siswa di luar usia jenjang), jadi sumbunya
        // tidak dikunci di 100 — dibiarkan mengikuti nilai tertinggi.
        yaxis: { labels: { formatter: v => fmt0(v) + '%', style: { fontSize: '10px', colors: '#aaa' } } },
        legend: { position: 'bottom', fontSize: '11px', markers: { width: 11, height: 11, radius: 3 } },
        grid: { borderColor: '#f5f5f5', strokeDashArray: 4 },
        tooltip: { shared: true, intersect: false, y: { formatter: v => fmt2(v) + '%' } }
    }).render();
}

// ── Kesehatan & kebencanaan: komposisi ────────────────────────
donutKomposisi('#chart-ov-faskes',  ovFaskesL,  ovFaskesV,  @json(__('overview.tooltip_unit')));
donutKomposisi('#chart-ov-nakes',   ovNakesL,   ovNakesV,   @json(__('overview.tooltip_orang')));
donutKomposisi('#chart-ov-bencana', ovBencanaL, ovBencanaV, @json(__('overview.tooltip_kejadian')));

// ── Tren ekonomi vs kemiskinan: dua satuan → dua sumbu ────────
if (ovTahun.length) {
    new ApexCharts(document.querySelector('#chart-ov-tren'), {
        chart: {
            type: 'line', height: 300, toolbar: { show: false }, fontFamily: 'inherit',
            animations: { enabled: true, easing: 'easeinout', speed: 550 }
        },
        series: [
            { name: @json(__('overview.series_pdrb')), data: ovPdrb },
            { name: @json(__('overview.series_miskin')), data: ovMiskin }
        ],
        colors: [BIRU, MERAH],
        stroke: { curve: 'smooth', width: 2 },
        markers: { size: 4, strokeWidth: 0, hover: { size: 7 } },
        dataLabels: { enabled: false },
        xaxis: {
            categories: ovTahun,
            labels: { style: { fontSize: '11px', colors: '#888' } },
            axisBorder: { show: false }, axisTicks: { show: false }
        },
        yaxis: [
            {
                seriesName: @json(__('overview.series_pdrb')),
                labels: { formatter: fmt2, style: { fontSize: '10px', colors: '#aaa' } },
                title: { text: @json(__('overview.axis_pdrb')), style: { fontSize: '10px', color: '#aaa', fontWeight: 600 } }
            },
            {
                seriesName: @json(__('overview.series_miskin')), opposite: true,
                labels: { formatter: fmt2, style: { fontSize: '10px', colors: '#aaa' } },
                title: { text: @json(__('overview.axis_miskin')), style: { fontSize: '10px', color: '#aaa', fontWeight: 600 } }
            }
        ],
        legend: { position: 'bottom', fontSize: '11px', markers: { width: 11, height: 11, radius: 3 } },
        grid: { borderColor: '#f5f5f5', strokeDashArray: 4 },
        tooltip: { shared: true, intersect: false }
    }).render();
}
</script>
@endpush
