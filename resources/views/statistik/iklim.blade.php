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

    /* Header */
    .stat-header-wrap { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
    .stat-header {
        flex: 1; background: #ffbf00; color: white; text-align: center;
        padding: 14px; border-radius: 8px; font-weight: 700;
        font-size: 18px; margin-bottom: 0; letter-spacing: 1px;
    }
    .dropdown-tahun { position: relative; flex-shrink: 0; }
    .dropdown-tahun-btn {
        display: flex; align-items: center; gap: 8px;
        border: 2px solid #ffbf00; border-radius: 6px; background: #fff;
        color: #b8860b; font-weight: 700; font-size: 14px;
        padding: 6px 12px; cursor: pointer; white-space: nowrap; user-select: none;
    }
    .dropdown-tahun-btn .arrow { font-size: 10px; transition: transform 0.2s; }
    .dropdown-tahun-btn.open .arrow { transform: rotate(180deg); }
    .dropdown-tahun-menu {
        display: none; position: absolute; top: calc(100% + 4px); right: 0;
        background: #fff; border: 2px solid #ffbf00; border-radius: 6px;
        min-width: 100%; z-index: 9999; overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .dropdown-tahun-menu.show { display: block; }
    .dropdown-tahun-menu a {
        display: block; padding: 8px 16px; color: #555;
        font-weight: 600; font-size: 14px; text-decoration: none; transition: background 0.15s;
    }
    .dropdown-tahun-menu a:hover { background: #fff8e1; color: #b8860b; }
    .dropdown-tahun-menu a.active { background: #ffbf00; color: #fff; }

    /* Summary cards — ikon kanan, label atas, nilai bawah (sama seperti geografis) */
    .stat-summary-card {
        background: #f9f9f9; border: 1px solid #eee;
        border-radius: 8px; padding: 16px 24px;
        display: flex; align-items: center; gap: 16px;
    }
    .stat-summary-card .card-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; flex-shrink: 0;
    }
    .stat-summary-card .card-text .label { font-size: 12px; font-weight: 600; color: #888; letter-spacing: 1px; }
    .stat-summary-card .card-text .value { font-size: 28px; font-weight: 700; color: #333; line-height: 1.15; }
    .stat-summary-card .card-text .value-status { font-size: 28px; font-weight: 700; line-height: 1.15; }

    .chart-card { background: #fff; border: 1px solid #eee; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
    .chart-card .chart-title { font-size: 13px; font-weight: 600; color: #555; letter-spacing: 1px; margin-bottom: 16px; }
    .chart-title-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .chart-title-row .chart-title { margin-bottom: 0; }
    .chart-total-badge {
        background: #fff8e1; border: 1px solid #ffd54f;
        color: #b8860b; border-radius: 6px; font-size: 12px;
        font-weight: 700; padding: 4px 10px;
    }

    /* Chart row 2 col */
    .chart-row { display: grid; grid-template-columns: 1fr 1.7fr; gap: 20px; margin-bottom: 20px; }

    /* Donut legend */
    .donut-legend { margin-top: 16px; }
    .donut-legend-item {
        display: flex; align-items: center; justify-content: space-between;
        font-size: 13px; color: #444; margin-bottom: 6px;
    }
    .donut-legend-dot { width: 10px; height: 10px; border-radius: 50%; margin-right: 8px; display: inline-block; flex-shrink: 0; }
    .donut-legend-left { display: flex; align-items: center; }
    .donut-legend-pct { font-weight: 700; color: #333; }

    /* Table */
    .iklim-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .iklim-table thead th {
        padding: 8px 12px; text-align: center; color: #777; font-weight: 600;
        border-bottom: 2px solid #f0f0f0;
    }
    .iklim-table thead th:first-child { text-align: left; }
    .iklim-table tbody td { padding: 10px 12px; border-bottom: 1px solid #f5f5f5; color: #333; text-align: center; }
    .iklim-table tbody td:first-child { text-align: left; }
    .iklim-table tbody tr:hover { background: #fffbf0; }
    /* Badge kategori BMKG (curah hujan / suhu / kelembaban) */
    .cat-badge {
        display: inline-flex; align-items: center; gap: 5px;
        border-radius: 6px; padding: 3px 10px; font-size: 11.5px; font-weight: 700;
    }
    .cat-badge .dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .cat-green  { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
    .cat-green  .dot { background: #43a047; }
    .cat-yellow { background: #fff8e1; color: #b8860b; border: 1px solid #ffd54f; }
    .cat-yellow .dot { background: #f9a825; }
    .cat-orange { background: #fff3e0; color: #e65100; border: 1px solid #ffb74d; }
    .cat-orange .dot { background: #fb8c00; }
    .cat-red    { background: #ffebee; color: #c62828; border: 1px solid #ef9a9a; }
    .cat-red    .dot { background: #e53935; }

    /* Nilai + dot kategori untuk kolom suhu & kelembaban */
    .cell-val { display: inline-flex; align-items: center; gap: 6px; justify-content: center; }
    .cell-val .dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

    /* Legend kategori */
    .kategori-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
    .kategori-col .kategori-head { font-size: 12px; font-weight: 700; color: #555; letter-spacing: .5px; margin-bottom: 10px; }
    .kategori-item { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #555; margin-bottom: 7px; }
    .kategori-item .dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .kategori-item b { color: #333; }
    @media (max-width: 768px) { .kategori-grid { grid-template-columns: 1fr; } }

    /* Pagination (sama seperti geografis) */
    .geo-pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 14px; font-size: 13px; color: #888; }
    .geo-pager { display: flex; gap: 4px; }
    .geo-pager button {
        width: 30px; height: 30px; border-radius: 6px; border: 1px solid #ddd;
        background: #fff; color: #555; cursor: pointer; font-size: 13px;
    }
    .geo-pager button.active { background: #ffbf00; border-color: #ffbf00; color: #fff; font-weight: 700; }
    .geo-pager button:disabled { opacity: 0.4; cursor: default; }

    .sumber { text-align: right; font-size: 12px; color: #999; margin-top: 16px; }

    @media (max-width: 1100px) { .chart-row { grid-template-columns: 1fr; } }
    @media (max-width: 768px) {
        .statistik-wrapper  { flex-direction: column; padding: 20px 0; gap: 16px; }
        .statistik-sidebar  { width: 100%; }
        .statistik-sidebar .nav {
            flex-direction: row !important; flex-wrap: nowrap;
            overflow-x: auto; gap: 6px; padding-bottom: 4px; -webkit-overflow-scrolling: touch;
        }
        .statistik-sidebar .nav-link { white-space: nowrap; margin-bottom: 0; }
        .stat-header        { font-size: 15px; padding: 12px; }
        #iklim-table        { min-width: 640px; }
    }
    /* Bungkus tabel agar bisa scroll horizontal di layar kecil */
    .iklim-table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
</style>
@endpush

@php
    $bulanLabel = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                   7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];

    $avgSuhu          = $iklim->avg('suhu_udara');
    $avgHariHujan     = $iklim->avg('hari_hujan');          // rata-rata hari hujan/bulan
    $avgKelembaban    = $iklim->avg('kelembaban_udara');
    $avgAngin         = $iklim->avg('kecepatan_angin');
    $avgTekanan       = $iklim->avg('tekanan_udara');
    $avgPenyinaran    = $iklim->avg('penyinaran_matahari');

    // ── Kategori standar (mengikuti acuan BMKG) ───────────────────
    // Curah hujan — berdasarkan jumlah hari hujan per bulan
    $rainCat = function ($h) {
        if ($h >= 25) return ['Awas',    'cat-red'];      // curah hujan ekstrem
        if ($h >= 20) return ['Siaga',   'cat-orange'];   // potensi banjir ringan
        if ($h >= 15) return ['Waspada', 'cat-yellow'];   // di atas normal
        return ['Normal', 'cat-green'];                   // sesuai rata-rata historis
    };
    // Suhu udara (°C)
    $suhuCat = function ($t) {
        if ($t > 33)  return ['Ekstrem', 'cat-red'];
        if ($t >= 30) return ['Panas',   'cat-yellow'];
        return ['Nyaman', 'cat-green'];                   // 24–30°C normal tropis
    };
    // Kelembaban udara (%)
    $lembabCat = function ($k) {
        if ($k > 90)  return ['Sangat Lembab', 'cat-red'];
        if ($k >= 80) return ['Lembab',        'cat-yellow'];
        return ['Ideal', 'cat-green'];                    // 60–80%
    };

    // Warna dot per kelas kategori
    $catDot = ['cat-green' => '#43a047', 'cat-yellow' => '#f9a825', 'cat-orange' => '#fb8c00', 'cat-red' => '#e53935'];

    // Status wilayah berdasarkan rata-rata hari hujan per bulan
    [$statusWilayah, $statusWilayahColor] = $rainCat($avgHariHujan);

    // Bar chart: hari hujan per bulan
    $hariHujanBulanan = $iklim->pluck('hari_hujan')->map(fn($v) => round((float)$v, 1));

    // Line chart: suhu per bulan
    $suhuBulanan = $iklim->pluck('suhu_udara')->map(fn($v) => round((float)$v, 1));

    // Donut: distribusi kelembaban ke 3 band
    $sangat_tinggi = $iklim->filter(fn($d) => $d->kelembaban_udara >= 80)->count();
    $tinggi        = $iklim->filter(fn($d) => $d->kelembaban_udara >= 75 && $d->kelembaban_udara < 80)->count();
    $sedang        = $iklim->filter(fn($d) => $d->kelembaban_udara < 75)->count();
    $totalBulan    = $iklim->count() ?: 1;
    $pctST = round($sangat_tinggi / $totalBulan * 100);
    $pctT  = round($tinggi        / $totalBulan * 100);
    $pctS  = round($sedang        / $totalBulan * 100);

    // Data per bulan untuk relasi card ↔ chart tren hari hujan
    $iklimBulanJs = $iklim->map(function ($d) use ($bulanLabel, $rainCat) {
        [$st, $stClass] = $rainCat($d->hari_hujan);
        return [
            'bulan'       => $bulanLabel[$d->bulan] ?? $d->bulan,
            'suhu'        => round((float) $d->suhu_udara, 1),
            'hari_hujan'  => round((float) $d->hari_hujan, 1),
            'kelembaban'  => round((float) $d->kelembaban_udara, 0),
            'status'      => $st,
            'statusClass' => $stClass,
        ];
    })->values();
@endphp

@section('content')
<div class="container-fluid px-4">
    <div class="statistik-wrapper">

        {{-- SIDEBAR --}}
        <div class="statistik-sidebar">
            <nav class="nav flex-column">
                <a class="nav-link" href="{{ route('statistik.geografis') }}"><i class="fa fa-map"></i> Geografis</a>
                <a class="nav-link active" href="{{ route('statistik.iklim') }}"><i class="fa fa-cloud"></i> Iklim</a>
                <a class="nav-link" href="{{ route('statistik.kependudukan') }}"><i class="fa fa-users"></i> Kependudukan</a>
                <a class="nav-link" href="{{ route('statistik.pendidikan') }}"><i class="fa fa-graduation-cap"></i> Pendidikan</a>
                <a class="nav-link" href="{{ route('statistik.kesehatan') }}"><i class="fa fa-plus-circle"></i> Kesehatan</a>
                <a class="nav-link" href="{{ route('statistik.bencana') }}"><i class="fa fa-house-flood-water"></i> Monitor Bencana</a>
            </nav>
        </div>

        {{-- KONTEN --}}
        <div class="statistik-content">

            {{-- Header --}}
            <div class="stat-header-wrap">
                <div class="stat-header">IKLIM JAKARTA BARAT {{ $tahun }}</div>
                <div class="dropdown-tahun">
                    <div class="dropdown-tahun-btn" id="dropdownTahunBtn">
                        <i class="fa fa-calendar"></i>
                        {{ $tahun }}
                        <span class="arrow">&#9660;</span>
                    </div>
                    <div class="dropdown-tahun-menu" id="dropdownTahunMenu">
                        @foreach($availableTahun as $t)
                        <a href="{{ route('statistik.iklim', ['tahun' => $t]) }}"
                           class="{{ (int) $t === (int) $tahun ? 'active' : '' }}">{{ $t }}</a>
                        @endforeach
                    </div>
                </div>
            </div>

        {{-- Summary cards --}}
        <div class="row mb-4 align-items-stretch">
            <div class="col-md-3 d-flex">
                <div class="stat-summary-card w-100">
                    <div class="card-text">
                        <div class="label" id="lbl-suhu">RATA-RATA SUHU UDARA (°C)</div>
                        <div class="value" id="val-suhu">{{ number_format($avgSuhu, 2) }}</div>
                    </div>
                    <div class="card-icon" style="background:#ffbf00; margin-left:auto;">
                        <i class="fa fa-thermometer-half" style="color:#fff;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex">
                <div class="stat-summary-card w-100">
                    <div class="card-text">
                        <div class="label" id="lbl-hujan">RATA-RATA HARI HUJAN (HARI/BLN)</div>
                        <div class="value" id="val-hujan">{{ number_format($avgHariHujan, 1) }}</div>
                    </div>
                    <div class="card-icon" style="background:#ffbf00; margin-left:auto;">
                        <i class="fa fa-tint" style="color:#fff;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex">
                <div class="stat-summary-card w-100">
                    <div class="card-text">
                        <div class="label" id="lbl-lembab">KELEMBABAN UDARA (%)</div>
                        <div class="value" id="val-lembab">{{ number_format($avgKelembaban, 0) }}<small style="font-size:13px; font-weight:500; color:#888;">%</small></div>
                    </div>
                    <div class="card-icon" style="background:#ffbf00; margin-left:auto;">
                        <i class="fa fa-water" style="color:#fff;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex">
                <div class="stat-summary-card w-100">
                    <div class="card-text">
                        <div class="label" id="lbl-status">STATUS CURAH HUJAN</div>
                        <div class="value-status" id="val-status" style="margin-top:4px;">
                            <span class="cat-badge {{ $statusWilayahColor }}" style="font-size:16px; padding:5px 12px;">
                                <span class="dot"></span>{{ $statusWilayah }}
                            </span>
                        </div>
                    </div>
                    <div class="card-icon" style="background:#ffbf00; margin-left:auto;">
                        <i class="fa fa-info-circle" style="color:#fff;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart row --}}
        <div class="chart-row">

            {{-- Donut: Distribusi Curah Hujan --}}
            <div class="chart-card">
                <div class="chart-title">DISTRIBUSI CURAH HUJAN</div>
                <div id="chart-donut"></div>
                <div class="donut-legend">
                    <div class="donut-legend-item">
                        <div class="donut-legend-left">
                            <span class="donut-legend-dot" style="background:#34527A;"></span> Sangat Tinggi
                        </div>
                        <span class="donut-legend-pct">{{ $pctST }}%</span>
                    </div>
                    <div class="donut-legend-item">
                        <div class="donut-legend-left">
                            <span class="donut-legend-dot" style="background:#5B82C0;"></span> Tinggi
                        </div>
                        <span class="donut-legend-pct">{{ $pctT }}%</span>
                    </div>
                    <div class="donut-legend-item">
                        <div class="donut-legend-left">
                            <span class="donut-legend-dot" style="background:#A9C0E0;"></span> Sedang
                        </div>
                        <span class="donut-legend-pct">{{ $pctS }}%</span>
                    </div>
                </div>
            </div>

            {{-- Bar: Tren Hari Hujan Bulanan --}}
            <div class="chart-card" id="chart-bar">
                <div class="chart-title-row">
                    <div>
                        <div class="chart-title">TREN HARI HUJAN BULANAN</div>
                        <div style="font-size:11px; color:#aaa; margin-top:2px;">Klik salah satu bulan untuk melihat detailnya di kartu ringkasan</div>
                    </div>
                    <span class="chart-total-badge">Rata-rata: {{ number_format($avgHariHujan, 1) }} hari/bln</span>
                </div>
                <div id="chart-bar-hujan"></div>
            </div>
        </div>

        {{-- Table section --}}
        <div class="chart-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <div class="chart-title" style="margin-bottom:0;">DATA IKLIM PER BULAN</div>
            </div>

            <div class="iklim-table-scroll">
            <table class="iklim-table" id="iklim-table">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Hari Hujan</th>
                        <th>Suhu (°C)</th>
                        <th>Kelembaban (%)</th>
                        <th>Angin (km/h)</th>
                        <th>Tekanan (mb)</th>
                        <th>Penyinaran (%)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="iklim-table-body">
                    @foreach ($iklim as $row)
                        @php
                            [$status, $statusClass] = $rainCat($row->hari_hujan);
                            [$suhuLbl, $suhuClass]   = $suhuCat($row->suhu_udara);
                            [$kelLbl, $kelClass]     = $lembabCat($row->kelembaban_udara);
                        @endphp
                        <tr>
                            <td>{{ $bulanLabel[$row->bulan] ?? $row->bulan }}</td>
                            <td>{{ number_format($row->hari_hujan, 1) }}</td>
                            <td>
                                <span class="cell-val" title="{{ $suhuLbl }}">
                                    <span class="dot" style="background:{{ $catDot[$suhuClass] }};"></span>{{ number_format($row->suhu_udara, 1) }}
                                </span>
                            </td>
                            <td>
                                <span class="cell-val" title="{{ $kelLbl }}">
                                    <span class="dot" style="background:{{ $catDot[$kelClass] }};"></span>{{ number_format($row->kelembaban_udara, 1) }}%
                                </span>
                            </td>
                            <td>{{ number_format($row->kecepatan_angin, 1) }}</td>
                            <td>{{ number_format($row->tekanan_udara, 1) }}</td>
                            <td>{{ number_format($row->penyinaran_matahari, 1) }}%</td>
                            <td><span class="cat-badge {{ $statusClass }}"><span class="dot"></span>{{ $status }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="geo-pagination">
                <div id="iklim-pager-info"></div>
                <div class="geo-pager" id="iklim-pager"></div>
            </div>
        </div>

        {{-- Keterangan kategori (acuan BMKG) --}}
        <div class="chart-card">
            <div class="chart-title">KETERANGAN KATEGORI</div>
            <div class="kategori-grid">
                <div class="kategori-col">
                    <div class="kategori-head">CURAH HUJAN</div>
                    <div class="kategori-item"><span class="dot" style="background:#43a047;"></span><span><b>Normal</b> — sesuai rata-rata historis</span></div>
                    <div class="kategori-item"><span class="dot" style="background:#f9a825;"></span><span><b>Waspada</b> — di atas normal, perlu perhatian</span></div>
                    <div class="kategori-item"><span class="dot" style="background:#fb8c00;"></span><span><b>Siaga</b> — potensi banjir ringan</span></div>
                    <div class="kategori-item"><span class="dot" style="background:#e53935;"></span><span><b>Awas</b> — curah hujan ekstrem</span></div>
                </div>
                <div class="kategori-col">
                    <div class="kategori-head">SUHU UDARA</div>
                    <div class="kategori-item"><span class="dot" style="background:#43a047;"></span><span><b>Nyaman</b> — 24–30°C (normal tropis)</span></div>
                    <div class="kategori-item"><span class="dot" style="background:#f9a825;"></span><span><b>Panas</b> — 30–33°C</span></div>
                    <div class="kategori-item"><span class="dot" style="background:#e53935;"></span><span><b>Ekstrem</b> — &gt;33°C</span></div>
                </div>
                <div class="kategori-col">
                    <div class="kategori-head">KELEMBABAN</div>
                    <div class="kategori-item"><span class="dot" style="background:#43a047;"></span><span><b>Ideal</b> — 60–80%</span></div>
                    <div class="kategori-item"><span class="dot" style="background:#f9a825;"></span><span><b>Lembab</b> — 80–90%</span></div>
                    <div class="kategori-item"><span class="dot" style="background:#e53935;"></span><span><b>Sangat Lembab</b> — &gt;90%</span></div>
                </div>
            </div>
            <div style="font-size:11px; color:#aaa; margin-top:14px;">Mengikuti acuan kategori BMKG.</div>
        </div>

        <div style="text-align:right; font-size:12px; color:#bbb; margin-top:16px;">
            Sumber: Kota Jakarta Barat Dalam Angka 2025
        </div>
        </div>{{-- statistik-content --}}

    </div>{{-- statistik-wrapper --}}
</div>{{-- container-fluid --}}
@endsection

@push('scripts')
<script>
    // Dropdown tahun (sama seperti geografis)
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
    var bulanLabels    = {!! json_encode($iklim->map(fn($d) => ($bulanLabel[$d->bulan] ?? $d->bulan))) !!};
    var hariHujan      = {!! json_encode($hariHujanBulanan->values()) !!};
    var avgHariHujan   = {{ round($avgHariHujan, 1) }};

    // Palet biru bertingkat berdasarkan nilai (konsisten dengan tema geografis)
    var BLUE_LIGHT = '#E2ECFA';
    var BLUE_DARK  = '#34527A';
    function lerpColor(a, b, t) {
        var ah = parseInt(a.slice(1), 16), bh = parseInt(b.slice(1), 16);
        var ar = ah >> 16, ag = (ah >> 8) & 0xff, ab = ah & 0xff;
        var br = bh >> 16, bg = (bh >> 8) & 0xff, bb = bh & 0xff;
        var rr = Math.round(ar + (br - ar) * t);
        var rg = Math.round(ag + (bg - ag) * t);
        var rb = Math.round(ab + (bb - ab) * t);
        return '#' + ((1 << 24) + (rr << 16) + (rg << 8) + rb).toString(16).slice(1);
    }
    var hMin = Math.min.apply(null, hariHujan);
    var hMax = Math.max.apply(null, hariHujan);
    var barColors = hariHujan.map(function(v) {
        var t = hMax > hMin ? (v - hMin) / (hMax - hMin) : 0.5;
        var step = Math.round(t * 4) / 4;   // 5 tingkatan agar mudah dibedakan
        return lerpColor(BLUE_LIGHT, BLUE_DARK, step);
    });
    var activeBar = null;

    // ── Relasi card ringkasan ↔ chart tren hari hujan ─────────────
    var iklimData = {!! json_encode($iklimBulanJs) !!};
    var idID = 'id-ID';
    function setText(id, t) { var el = document.getElementById(id); if (el) el.textContent = t; }
    function setHTML(id, h) { var el = document.getElementById(id); if (el) el.innerHTML = h; }
    function fmt1(v) { return Number(v).toLocaleString(idID, { minimumFractionDigits: 1, maximumFractionDigits: 1 }); }

    var cardDefaults = {
        suhuLbl:   'RATA-RATA SUHU UDARA (°C)',          suhuVal:   '{{ number_format($avgSuhu, 2) }}',
        hujanLbl:  'RATA-RATA HARI HUJAN (HARI/BLN)',    hujanVal:  '{{ number_format($avgHariHujan, 1) }}',
        lembabLbl: 'KELEMBABAN UDARA (%)',               lembabVal: '{{ number_format($avgKelembaban, 0) }}<small style="font-size:13px; font-weight:500; color:#888;">%</small>',
        statusLbl: 'STATUS CURAH HUJAN',                 statusVal: '<span class="cat-badge {{ $statusWilayahColor }}" style="font-size:16px; padding:5px 12px;"><span class="dot"></span>{{ $statusWilayah }}</span>',
    };

    function updateCards(i) {
        var d = iklimData[i];
        if (!d) return;
        var bln = d.bulan.toUpperCase();
        setText('lbl-suhu',   'SUHU — ' + bln);          setText('val-suhu',   fmt1(d.suhu));
        setText('lbl-hujan',  'HARI HUJAN — ' + bln);    setText('val-hujan',  fmt1(d.hari_hujan));
        setText('lbl-lembab', 'KELEMBABAN — ' + bln);    setHTML('val-lembab', d.kelembaban + '<small style="font-size:13px; font-weight:500; color:#888;">%</small>');
        setText('lbl-status', 'STATUS — ' + bln);        setHTML('val-status', '<span class="cat-badge ' + d.statusClass + '" style="font-size:16px; padding:5px 12px;"><span class="dot"></span>' + d.status + '</span>');
    }

    function resetCards() {
        setText('lbl-suhu',   cardDefaults.suhuLbl);    setText('val-suhu',   cardDefaults.suhuVal);
        setText('lbl-hujan',  cardDefaults.hujanLbl);   setText('val-hujan',  cardDefaults.hujanVal);
        setText('lbl-lembab', cardDefaults.lembabLbl);  setHTML('val-lembab', cardDefaults.lembabVal);
        setText('lbl-status', cardDefaults.statusLbl);  setHTML('val-status', cardDefaults.statusVal);
    }

    // Label putih untuk bar gelap, abu gelap untuk bar terang
    function labelColor(hex) {
        var c = parseInt(hex.slice(1), 16);
        var lum = 0.299 * (c >> 16) + 0.587 * ((c >> 8) & 0xff) + 0.114 * (c & 0xff);
        return lum > 150 ? '#333' : '#fff';
    }
    var barLabelColors = barColors.map(labelColor);

    var barChart = new ApexCharts(document.querySelector('#chart-bar-hujan'), {
        chart: {
            type: 'bar',
            height: 270,
            toolbar: { show: false },
            animations: { enabled: true, easing: 'easeinout', speed: 600 },
            events: {
                dataPointSelection: function(e, ctx, cfg) {
                    var idx = cfg.dataPointIndex;
                    if (activeBar === idx) {
                        activeBar = null;
                        barChart.updateOptions({
                            colors: barColors,
                            fill:   { colors: barColors },
                            dataLabels: { style: { colors: barLabelColors } }
                        });
                        resetCards();
                        return;
                    }
                    activeBar = idx;
                    var fadedColors = barColors.map(function(c, i) { return i === idx ? c : c + '44'; });
                    var fadedLabels = barLabelColors.map(function(c, i) { return i === idx ? c : c + '44'; });
                    barChart.updateOptions({
                        colors: fadedColors,
                        fill:   { colors: fadedColors },
                        dataLabels: { style: { colors: fadedLabels } }
                    });
                    updateCards(idx);
                },
                click: function(e, ctx, cfg) {
                    if (cfg.dataPointIndex === undefined || cfg.dataPointIndex < 0) {
                        activeBar = null;
                        barChart.updateOptions({
                            colors: barColors,
                            fill:   { colors: barColors },
                            dataLabels: { style: { colors: barLabelColors } }
                        });
                        resetCards();
                    }
                }
            }
        },
        series: [{ name: 'Hari Hujan (hari)', data: hariHujan }],
        xaxis: {
            categories: bulanLabels,
            labels: { style: { fontSize: '11px', colors: '#aaa' } },
            axisBorder: { show: false },
            axisTicks:  { show: false }
        },
        yaxis: {
            labels: { style: { colors: '#aaa', fontSize: '11px' } },
            title: { text: 'Hari', style: { color: '#aaa', fontSize: '11px' } }
        },
        colors: barColors,
        fill: { colors: barColors },
        dataLabels: {
            enabled: true,
            style: { fontSize: '10px', fontWeight: 700, colors: barLabelColors },
            dropShadow: { enabled: false }
        },
        plotOptions: { bar: { borderRadius: 3, columnWidth: '55%', distributed: true } },
        states: {
            hover:  { filter: { type: 'lighten', value: 0.08 } },
            active: { filter: { type: 'darken',  value: 0.15 } }
        },
        annotations: {
            yaxis: [{
                y: avgHariHujan,
                borderColor: '#999',
                borderWidth: 1,
                strokeDashArray: 4,
                label: {
                    text: 'Rata-rata: ' + avgHariHujan + ' hari',
                    style: { color: '#666', fontSize: '10px', background: '#f9f9f9' }
                }
            }]
        },
        legend: { show: false },
        grid: { borderColor: '#f0f0f0', strokeDashArray: 4 },
        tooltip: {
            y: { formatter: function(v) { return v + ' hari'; } }
        }
    });
    barChart.render();

    var donutValues  = [{{ $pctST }}, {{ $pctT }}, {{ $pctS }}];
    var donutLabels  = ['Sangat Tinggi', 'Tinggi', 'Sedang'];
    var donutColors  = ['#34527A', '#5B82C0', '#A9C0E0'];
    var donutMain    = Math.round({{ $pctT + $pctST }});

    var donutChart = new ApexCharts(document.querySelector('#chart-donut'), {
        chart: {
            type: 'donut',
            height: 220,
            toolbar: { show: false },
            animations: { enabled: true, easing: 'easeinout', speed: 500 },
            events: {
                // Klik segment: update center label & highlight legend
                dataPointSelection: function(e, ctx, cfg) {
                    var idx = cfg.dataPointIndex;
                    var el  = document.querySelectorAll('.donut-legend-item')[idx];
                    document.querySelectorAll('.donut-legend-item').forEach(function(item, i) {
                        item.style.opacity   = i === idx ? '1' : '0.35';
                        item.style.fontWeight = i === idx ? '700' : '400';
                        item.style.transform = i === idx ? 'scale(1.03)' : 'scale(1)';
                        item.style.transition = 'all .2s';
                    });
                },
                // Reset legend saat klik di luar
                click: function(e, ctx, cfg) {
                    if (cfg.dataPointIndex === undefined || cfg.dataPointIndex < 0) {
                        document.querySelectorAll('.donut-legend-item').forEach(function(item) {
                            item.style.opacity   = '1';
                            item.style.fontWeight = '400';
                            item.style.transform = 'scale(1)';
                        });
                    }
                }
            }
        },
        series: donutValues,
        labels: donutLabels,
        colors: donutColors,
        dataLabels: { enabled: false },
        legend: { show: false },
        stroke: { width: 3, colors: ['#fff'] },
        states: {
            hover:  { filter: { type: 'lighten', value: 0.05 } },
            active: { filter: { type: 'darken',  value: 0.15 } }
        },
        plotOptions: {
            pie: {
                expandOnClick: true,
                donut: {
                    size: '72%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Tinggi',
                            color: '#333', fontSize: '13px', fontWeight: 600,
                            formatter: function() { return donutMain + '%'; }
                        },
                        value: {
                            show: true, fontSize: '26px', fontWeight: 800,
                            color: '#1a1a1a',
                            formatter: function(v) { return v + '%'; }
                        },
                        name: {
                            show: true, fontSize: '13px', fontWeight: 600, color: '#888'
                        }
                    }
                }
            }
        },
        tooltip: { enabled: false }
    });
    donutChart.render();

    // Legend items — klik untuk toggle segment donut
    document.querySelectorAll('.donut-legend-item').forEach(function(item, idx) {
        item.style.cursor = 'pointer';
        item.style.transition = 'all .2s';
        item.addEventListener('click', function() {
            donutChart.toggleDataPointSelection(0, idx);
        });
        item.addEventListener('mouseenter', function() {
            if (item.style.opacity !== '0.35') item.style.transform = 'scale(1.03)';
        });
        item.addEventListener('mouseleave', function() {
            if (item.style.opacity !== '0.35') item.style.transform = 'scale(1)';
        });
    });

    // ── Pagination tabel iklim (sama seperti geografis) ───────────
    var IKLIM_PAGE_SIZE = 6, iklimPage = 1;

    function iklimRows() { return Array.from(document.querySelectorAll('#iklim-table-body tr')); }

    function renderIklimTable() {
        var rows  = iklimRows();
        var total = rows.length;
        var start = (iklimPage - 1) * IKLIM_PAGE_SIZE;
        var end   = Math.min(start + IKLIM_PAGE_SIZE, total);

        rows.forEach(function(r, i) { r.style.display = (i >= start && i < end) ? '' : 'none'; });

        document.getElementById('iklim-pager-info').textContent =
            'Menampilkan ' + (total ? start + 1 : 0) + '–' + end + ' dari ' + total + ' bulan';

        var pages = Math.ceil(total / IKLIM_PAGE_SIZE) || 1;
        var pager = document.getElementById('iklim-pager');
        pager.innerHTML = '';

        var prev = document.createElement('button');
        prev.innerHTML = '&lsaquo;'; prev.disabled = iklimPage === 1;
        prev.onclick = function() { iklimPage--; renderIklimTable(); };
        pager.appendChild(prev);

        for (var p = 1; p <= pages; p++) {
            (function(pg) {
                var btn = document.createElement('button');
                btn.textContent = pg;
                if (pg === iklimPage) btn.classList.add('active');
                btn.onclick = function() { iklimPage = pg; renderIklimTable(); };
                pager.appendChild(btn);
            })(p);
        }

        var next = document.createElement('button');
        next.innerHTML = '&rsaquo;'; next.disabled = iklimPage === pages;
        next.onclick = function() { iklimPage++; renderIklimTable(); };
        pager.appendChild(next);
    }

    renderIklimTable();
</script>
@endpush