@extends('landing-page.layout.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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

    /* Summary cards — ikon kiri, label atas, nilai bawah (sama seperti kependudukan) */
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
    .stat-summary-card .card-text .label  { font-size: 12px; font-weight: 600; color: #888; letter-spacing: 1px; }
    .stat-summary-card .card-text .value  { font-size: 28px; font-weight: 700; color: #333; line-height: 1.15; }
    .stat-summary-card .card-text .value small { font-size: 13px; font-weight: 500; color: #888; margin-left: 3px; }

    /* Chart cards */
    .chart-card { background: #fff; border: 1px solid #eee; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
    .chart-card .chart-title { font-size: 13px; font-weight: 600; color: #555; letter-spacing: 1px; margin-bottom: 16px; }

    /* Two-column middle section */
    .geo-mid-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 0; }
    .chart-card-left { display: flex; flex-direction: column; gap: 20px; }
    .chart-card-left .chart-card { margin-bottom: 0; }

    /* Map */
    #geo-map { height: 490px; width: 100%; border-radius: 8px; z-index: 1; }

    /* Map */

    /* Comparison chart toggle */
    .chart-title-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px; }
    .chart-sub { font-size: 11px; color: #aaa; margin-bottom: 12px; }
    .chart-toggle-btns { display: flex; gap: 6px; }
    .chart-toggle-btns button {
        font-size: 11px; padding: 3px 12px; border-radius: 5px; border: 1px solid #ddd;
        background: #f5f5f5; color: #666; cursor: pointer; font-weight: 600;
    }
    .chart-toggle-btns button.active { background: #ffbf00; border-color: #ffbf00; color: #fff; }

    /* Highlight cards */
    .geo-highlight-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 20px; }
    .geo-hl-card {
        background: #fff; border: 1px solid #eee; border-radius: 8px;
        padding: 14px 16px; display: flex; align-items: center; gap: 12px;
    }
    .geo-hl-card .hl-icon {
        width: 42px; height: 42px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;
    }
    .geo-hl-card .hl-tag  { font-size: 10px; font-weight: 700; letter-spacing: 1px; margin-bottom: 2px; }
    .geo-hl-card .hl-name { font-size: 13px; font-weight: 700; color: #222; }
    .geo-hl-card .hl-sub  { font-size: 11px; color: #888; }

    /* Table */
    .geo-table-wrap { background: #fff; border: 1px solid #eee; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
    .geo-table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
    .geo-table-header .tbl-title { font-size: 14px; font-weight: 600; color: #333; }
    .geo-search-input {
        border: 1px solid #ddd; border-radius: 6px; padding: 6px 12px 6px 32px;
        font-size: 13px; background: #f9f9f9; outline: none; width: 200px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23aaa' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: 10px center;
    }
    .geo-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .geo-table th { padding: 8px 12px; text-align: left; color: #777; font-weight: 600; border-bottom: 2px solid #f0f0f0; }
    .geo-table td { padding: 10px 12px; border-bottom: 1px solid #f5f5f5; color: #333; }
    .geo-table tbody tr:hover { background: #fffbf0; }
    .geo-pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 14px; font-size: 13px; color: #888; }
    .geo-pager { display: flex; gap: 4px; }
    .geo-pager button {
        width: 30px; height: 30px; border-radius: 6px; border: 1px solid #ddd;
        background: #fff; color: #555; cursor: pointer; font-size: 13px;
    }
    .geo-pager button.active { background: #ffbf00; border-color: #ffbf00; color: #fff; font-weight: 700; }
    .geo-pager button:disabled { opacity: 0.4; cursor: default; }

    .sumber { text-align: right; font-size: 12px; color: #999; margin-top: 16px; }

    /* Tombol export CSV */
    .btn-export-csv {
        display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;
        border: 1px solid #1e8e3e; background: #eaf6ec; color: #1e8e3e;
        font-size: 13px; font-weight: 600; padding: 6px 12px; border-radius: 6px;
        cursor: pointer; transition: background .15s, color .15s;
    }
    .btn-export-csv:hover { background: #1e8e3e; color: #fff; }

    /* Animasi nilai card saat kecamatan dipilih (halus, fade + naik) */
    @keyframes cardValueIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .stat-summary-card .card-anim { animation: cardValueIn .35s ease both; }

    /* ── Responsive (tablet & HP) ──────────────────────────────── */
    @media (max-width: 992px) {
        .geo-mid-grid       { grid-template-columns: 1fr; }
        .geo-highlight-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .statistik-wrapper  { flex-direction: column; padding: 20px 0; gap: 16px; }
        .statistik-sidebar  { width: 100%; }
        .statistik-sidebar .nav {
            flex-direction: row !important; flex-wrap: nowrap;
            overflow-x: auto; gap: 6px; padding-bottom: 4px; -webkit-overflow-scrolling: touch;
        }
        .statistik-sidebar .nav-link { white-space: nowrap; margin-bottom: 0; }
        .stat-header        { font-size: 15px; padding: 12px; }
        #geo-map            { height: 360px; }
        .geo-table-wrap     { overflow-x: auto; }
        .geo-table          { min-width: 700px; }

        /* Legend kecamatan di peta — lebih kecil & tidak menutupi peta di HP */
        .kec-legend {
            font-size: 8.5px !important; line-height: 1.25 !important;
            padding: 4px 6px !important; max-width: 44vw;
            max-height: 150px; overflow-y: auto;
            opacity: 0.92;
        }
        .kec-legend .legend-kec-item { padding: 1px 3px !important; gap: 3px !important; }
        .kec-legend .legend-kec-item span:last-child { white-space: normal !important; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
<div class="statistik-wrapper">

    @include('statistik.partials.sidebar')

    {{-- KONTEN --}}
    <div class="statistik-content">

        {{-- Header --}}
        <div class="stat-header-wrap">
            <div class="stat-header">GEOGRAFIS JAKARTA BARAT {{ $geo->tahun }}</div>
            <div class="dropdown-tahun">
                <div class="dropdown-tahun-btn" id="dropdownTahunBtn">
                    <i class="fa fa-calendar"></i>
                    {{ $geo->tahun }}
                    <span class="arrow">&#9660;</span>
                </div>
                <div class="dropdown-tahun-menu" id="dropdownTahunMenu">
                    @foreach($availableTahun as $t)
                    <a href="{{ route('statistik.geografis', ['tahun' => $t]) }}"
                       class="{{ (int) $t === (int) $tahun ? 'active' : '' }}">{{ $t }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Summary Cards --}}
        @php
            $jumlahKecamatan = $luas->count();
            $totalKelurahan  = $kecStats->sum('kelurahan') ?: 56;
            $totalPenduduk   = $kecStats->sum('penduduk');
            $totalKepadatan  = ($totalPenduduk && $geo->luas_kota_km2)
                ? round($totalPenduduk / $geo->luas_kota_km2) : 19243;
        @endphp
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-summary-card" id="card-luas">
                    <div class="card-text">
                        <div class="label" id="lbl-luas">LUAS WILAYAH</div>
                        <div class="value"><span id="val-luas">{{ number_format($geo->luas_kota_km2, 2) }}</span><small>km²</small></div>
                    </div>
                    <div class="card-icon" style="background:#2a78d6; margin-left:auto;">
                        <i class="fa fa-map" style="color:#fff;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-summary-card" id="card-kec">
                    <div class="card-text">
                        <div class="label" id="lbl-kec">JUMLAH KECAMATAN</div>
                        <div class="value"><span id="val-kec">{{ $jumlahKecamatan }}</span><small id="unit-kec"></small></div>
                    </div>
                    <div class="card-icon" style="background:#008300; margin-left:auto;">
                        <i class="fa fa-map-marker-alt" style="color:#fff;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-summary-card" id="card-kel">
                    <div class="card-text">
                        <div class="label" id="lbl-kel">JUMLAH KELURAHAN</div>
                        <div class="value"><span id="val-kel">{{ $totalKelurahan }}</span></div>
                    </div>
                    <div class="card-icon" style="background:#eb6834; margin-left:auto;">
                        <i class="fa fa-building" style="color:#fff;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-summary-card" id="card-padat">
                    <div class="card-text">
                        <div class="label" id="lbl-padat">KEPADATAN WILAYAH</div>
                        <div class="value"><span id="val-padat">{{ number_format($totalKepadatan, 0, ',', '.') }}</span><small>/km²</small></div>
                    </div>
                    <div class="card-icon" style="background:#4a3aa7; margin-left:auto;">
                        <i class="fa fa-users" style="color:#fff;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Middle: Charts (left) + Map (right) --}}
        <div class="geo-mid-grid">

            {{-- LEFT: Bar + Donut --}}
            <div class="chart-card-left">
                <div class="chart-card">
                    <div class="chart-title">LUAS WILAYAH PER KECAMATAN</div>
                    <div id="chart-bar-luas"></div>
                </div>
                <div class="chart-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="chart-title" style="margin-bottom:0;">PERSENTASE LUAS WILAYAH</div>
                        <div style="font-size:11px;color:#aaa;">Total {{ number_format($geo->luas_kota_km2, 1) }} km²</div>
                    </div>
                    <div id="chart-donut-persen"></div>
                </div>
            </div>

            {{-- RIGHT: Map --}}
            <div class="chart-card" style="margin-bottom:0; display:flex; flex-direction:column; gap:10px;">
                <div class="chart-title" style="margin-bottom:0;">PETA WILAYAH JAKARTA BARAT</div>
                <div id="geo-map"></div>
            </div>
        </div>

        {{-- Comparison Chart --}}
        <div class="chart-card">
            <div class="chart-title-row">
                <div>
                    <div class="chart-title" style="margin-bottom:2px;">PERBANDINGAN STATISTIK LANJUTAN</div>
                    <div class="chart-sub">Komparasi antara luas wilayah dan kepadatan penduduk per kecamatan</div>
                </div>
            </div>
            <div id="chart-compare"></div>
        </div>

        {{-- Highlight Cards --}}
        @php
            $sortedLuas = $luas->sortByDesc('luas_km2');
            $terluas    = $sortedLuas->first();
            $terkecil   = $sortedLuas->last();
        @endphp
        <div class="geo-highlight-grid">
            <div class="geo-hl-card">
                <div class="hl-icon" style="background:#E5ECF5;"><i class="fa fa-expand-arrows-alt" style="color:#34527A;"></i></div>
                <div>
                    <div class="hl-tag" style="color:#34527A;">TERLUAS</div>
                    <div class="hl-name">Kecamatan {{ $terluas->kecamatan->nama_kecamatan }}</div>
                    <div class="hl-sub">{{ number_format($terluas->luas_km2, 2) }} km² ({{ number_format($terluas->persentase, 1) }}% dari total)</div>
                </div>
            </div>
            <div class="geo-hl-card">
                <div class="hl-icon" style="background:#EDF1F8;"><i class="fa fa-compress-arrows-alt" style="color:#7B97C2;"></i></div>
                <div>
                    <div class="hl-tag" style="color:#5B7BB0;">TERKECIL</div>
                    <div class="hl-name">Kecamatan {{ $terkecil->kecamatan->nama_kecamatan }}</div>
                    <div class="hl-sub">{{ number_format($terkecil->luas_km2, 2) }} km² ({{ number_format($terkecil->persentase, 1) }}% dari total)</div>
                </div>
            </div>
            <div class="geo-hl-card">
                <div class="hl-icon" style="background:#E5ECF5;"><i class="fa fa-users" style="color:#4A6FA5;"></i></div>
                <div>
                    <div class="hl-tag" style="color:#4A6FA5;">TERPADAT</div>
                    <div class="hl-name">Kecamatan Tambora</div>
                    <div class="hl-sub">48.243 jiwa/km²</div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="geo-table-wrap">
            <div class="geo-table-header">
                <div class="tbl-title">Tabel Geografis Rinci</div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <input class="geo-search-input" type="text" id="geo-search" placeholder="Cari kecamatan..." oninput="filterTable()">
                    <button type="button" class="btn-export-csv" onclick="exportTableCSV()">
                        <i class="fa fa-file-csv"></i> Export CSV
                    </button>
                </div>
            </div>
            <table class="geo-table" id="geo-table">
                <thead>
                    <tr>
                        <th>Kecamatan</th>
                        <th>Luas (km²)</th>
                        <th>Kelurahan</th>
                        <th>RW</th>
                        <th>RT</th>
                        <th>Populasi</th>
                        <th>Kepadatan (/km²)</th>
                    </tr>
                </thead>
                <tbody id="geo-table-body">
                    @foreach($luas->sortByDesc('luas_km2') as $row)
                    @php $s = $kecStats[strtoupper($row->kecamatan->nama_kecamatan)] ?? null; @endphp
                    <tr data-name="{{ strtolower($row->kecamatan->nama_kecamatan) }}">
                        <td>{{ $row->kecamatan->nama_kecamatan }}</td>
                        <td>{{ number_format($row->luas_km2, 2) }}</td>
                        <td>{{ $s && $s['kelurahan'] ? $s['kelurahan'] : '—' }}</td>
                        <td>{{ $s && $s['rw'] ? number_format($s['rw'], 0, ',', '.') : '—' }}</td>
                        <td>{{ $s && $s['rt'] ? number_format($s['rt'], 0, ',', '.') : '—' }}</td>
                        <td>{{ $s && $s['penduduk'] ? number_format($s['penduduk'], 0, ',', '.') : '—' }}</td>
                        <td>{{ $s && $s['kepadatan'] ? number_format($s['kepadatan'], 0, ',', '.') : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="geo-pagination">
                <div id="pager-info"></div>
                <div class="geo-pager" id="geo-pager"></div>
            </div>
        </div>

        <div class="sumber">Sumber: {{ $geo->sumber }}</div>

    </div>{{-- end statistik-content --}}
</div>{{-- end statistik-wrapper --}}
</div>
@endsection

@push('scripts')
@include('statistik.partials.warna-kecamatan')
<script>
(function () {
    var btn  = document.getElementById('dropdownTahunBtn');
    var menu = document.getElementById('dropdownTahunMenu');
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
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ── Data dari Laravel ─────────────────────────────────────────
var namaKec  = {!! json_encode($luas->sortByDesc('luas_km2')->pluck('kecamatan.nama_kecamatan')) !!};
var luasData = {!! json_encode($luas->sortByDesc('luas_km2')->pluck('luas_km2')->map(fn($v) => (float)$v)) !!};
var persen   = {!! json_encode($luas->sortByDesc('luas_km2')->pluck('persentase')->map(fn($v) => (float)$v)) !!};

// Statistik per kecamatan (key = NAMA UPPERCASE) untuk card dinamis
var kecStatsData = {!! json_encode($kecStats) !!};

// ── Card ringkasan dinamis ────────────────────────────────────
var idID = 'id-ID';
function fmtNum(v, dec) { return Number(v).toLocaleString(idID, { minimumFractionDigits: dec || 0, maximumFractionDigits: dec || 0 }); }
function setText(id, txt) { var el = document.getElementById(id); if (el) el.textContent = txt; }

// Animasi halus (fade + naik) pada isi card saat nilainya berubah
function animateCards() {
    document.querySelectorAll('.stat-summary-card .card-text').forEach(function(el) {
        el.classList.remove('card-anim');
        void el.offsetWidth;   // retrigger animasi
        el.classList.add('card-anim');
    });
}

// Simpan nilai default (tampilan total kota)
var cardDefaults = {
    luas:  { label: 'LUAS WILAYAH',     val: '{{ number_format($geo->luas_kota_km2, 2, ',', '.') }}' },
    kec:   { label: 'JUMLAH KECAMATAN', val: '{{ $jumlahKecamatan }}', unit: '' },
    kel:   { label: 'JUMLAH KELURAHAN', val: '{{ number_format($totalKelurahan, 0, ',', '.') }}' },
    padat: { label: 'KEPADATAN WILAYAH',val: '{{ number_format($totalKepadatan, 0, ',', '.') }}' },
};

function updateCards(namaUp) {
    var s = kecStatsData[namaUp];
    if (!s) return;
    setText('lbl-luas', 'LUAS KEC. ' + s.nama.toUpperCase());
    setText('val-luas', fmtNum(s.luas, 2));
    setText('lbl-kec', '% LUAS WILAYAH');
    setText('val-kec', fmtNum(s.persentase, 2));
    setText('unit-kec', '%');
    setText('lbl-kel', 'JUMLAH KELURAHAN');
    setText('val-kel', s.kelurahan ? fmtNum(s.kelurahan) : '-');
    setText('lbl-padat', 'KEPADATAN KEC.');
    setText('val-padat', s.kepadatan ? fmtNum(s.kepadatan) : '-');
    animateCards();
}

function resetCards() {
    setText('lbl-luas', cardDefaults.luas.label);   setText('val-luas', cardDefaults.luas.val);
    setText('lbl-kec',  cardDefaults.kec.label);    setText('val-kec',  cardDefaults.kec.val);   setText('unit-kec', '');
    setText('lbl-kel',  cardDefaults.kel.label);    setText('val-kel',  cardDefaults.kel.val);
    setText('lbl-padat',cardDefaults.padat.label);  setText('val-padat',cardDefaults.padat.val);
    animateCards();
}

// Skala warna choropleth (base kuning) berdasarkan luas wilayah — konsisten map & chart
var luasMin = Math.min.apply(null, luasData);
var luasMax = Math.max.apply(null, luasData);
var luasLookup = {};
namaKec.forEach(function(n, i) { luasLookup[n.toUpperCase()] = luasData[i]; });

// ── Warna per kecamatan: dari sumber tunggal window.warnaKecamatan (konsisten antar modul) ──
function lerpColor(a, b, t) {
    var ah = parseInt(a.slice(1), 16), bh = parseInt(b.slice(1), 16);
    var ar = ah >> 16, ag = (ah >> 8) & 0xff, ab = ah & 0xff;
    var br = bh >> 16, bg = (bh >> 8) & 0xff, bb = bh & 0xff;
    var rr = Math.round(ar + (br - ar) * t);
    var rg = Math.round(ag + (bg - ag) * t);
    var rb = Math.round(ab + (bb - ab) * t);
    return '#' + ((1 << 24) + (rr << 16) + (rg << 8) + rb).toString(16).slice(1);
}
function getWarna(n) {
    return window.warnaKecamatan(n);
}
var warnaArr = namaKec.map(function(n){ return getWarna(n); });

/* ── WARNA LAMA (gradasi biru monokrom berdasarkan luas) — disimpan untuk referensi ──
var YEL_LIGHT = '#E2ECFA';   // luas terkecil → biru sangat muda
var YEL_DARK  = '#5B82C0';   // luas terbesar → biru slate cerah
var WARNA_STEPS = 5;   // jumlah tingkatan warna (choropleth bertingkat)
function getWarnaLama(n) {
    var v = luasLookup[(n || '').toUpperCase()];
    if (v == null) return '#e0e0e0';
    var t = luasMax > luasMin ? (v - luasMin) / (luasMax - luasMin) : 0.5;
    // Snap ke salah satu dari WARNA_STEPS tingkatan agar mudah dibedakan
    var step = Math.round(t * (WARNA_STEPS - 1)) / (WARNA_STEPS - 1);
    return lerpColor(YEL_LIGHT, YEL_DARK, step);
}
*/

// Klik elemen chart → fokuskan kecamatan (berelasi dengan peta & card)
function chartClickFocus(index) {
    if (index == null || index < 0) return;
    var namaUp = (namaKec[index] || '').toUpperCase();
    if (window.focusKecamatan) window.focusKecamatan(namaUp);
}

// ── Chart Bar Luas ────────────────────────────────────────────
new ApexCharts(document.querySelector('#chart-bar-luas'), {
    chart: { type: 'bar', height: 240, toolbar: { show: false },
        events: { dataPointSelection: function(e, ctx, cfg) { chartClickFocus(cfg.dataPointIndex); } } },
    series: [{ name: 'Luas (km²)', data: luasData }],
    xaxis: { categories: namaKec, labels: { style: { fontSize: '10px' } } },
    colors: warnaArr,
    plotOptions: { bar: { borderRadius: 3, distributed: true, horizontal: true } },
    dataLabels: { enabled: true, style: { fontSize: '9px' } },
    legend: { show: false },
    grid: { borderColor: '#f5f5f5' },
    states: { active: { filter: { type: 'darken', value: 0.6 } } },
}).render();

// ── Chart Donut ───────────────────────────────────────────────
new ApexCharts(document.querySelector('#chart-donut-persen'), {
    chart: { type: 'donut', height: 260,
        events: { dataPointSelection: function(e, ctx, cfg) { chartClickFocus(cfg.dataPointIndex); } } },
    series: persen,
    labels: namaKec,
    colors: warnaArr,
    dataLabels: { enabled: false },   // angka disembunyikan, muncul lewat tooltip saat hover
    tooltip: { enabled: true, y: { formatter: function(v){ return v + '%'; } } },
    legend: { position: 'bottom', fontSize: '11px' },
    plotOptions: { pie: { donut: { labels: {
        show: true,
        total: { show: true, label: 'Total', fontSize: '12px',
                 formatter: function() { return '{!! number_format($geo->luas_kota_km2, 1) !!} km²'; } }
    }}}},
}).render();

// ── Chart Comparison ──────────────────────────────────────────
// Kepadatan asli dari DB (penduduk ÷ luas), urut sesuai namaKec
var kepadatanData = namaKec.map(function(n){
    var s = kecStatsData[n.toUpperCase()];
    return s && s.kepadatan ? s.kepadatan : 0;
});
new ApexCharts(document.querySelector('#chart-compare'), {
    chart: { type: 'bar', height: 300, toolbar: { show: false } },
    series: [
        { name: 'Luas Wilayah (km²)', data: luasData },
        { name: 'Kepadatan (/km²)',   data: kepadatanData },
    ],
    xaxis: {
        categories: namaKec,
        labels: { rotate: -30, rotateAlways: true, style: { fontSize: '10px' }, trim: false }
    },
    // Dua sumbu terpisah → skala luas & kepadatan mandiri, bar luas tak lagi kekecilan
    yaxis: [
        { seriesName: 'Luas Wilayah (km²)',
          title: { text: 'Luas (km²)', style: { fontSize: '9px', color: '#4A6FA5' } },
          labels: { style: { fontSize: '9px', colors: '#4A6FA5' }, formatter: function(v){ return v.toFixed(0); } } },
        { seriesName: 'Kepadatan (/km²)', opposite: true,
          title: { text: 'Kepadatan', style: { fontSize: '9px', color: '#F5A623' } },
          labels: { style: { fontSize: '9px', colors: '#F5A623' }, formatter: function(v){ return (v/1000).toFixed(0) + 'rb'; } } },
    ],
    colors: ['#4A6FA5', '#F5A623'],
    dataLabels: { enabled: false },
    plotOptions: { bar: { borderRadius: 3, columnWidth: '60%' } },
    legend: { position: 'bottom', fontSize: '11px' },
    grid: { borderColor: '#f5f5f5' },
    // Hover pada bar menampilkan kedua nilai sekaligus
    tooltip: {
        shared: true, intersect: false,
        y: [
            { formatter: function(v){ return v.toFixed(2) + ' km²'; } },
            { formatter: function(v){ return Number(v).toLocaleString('id-ID') + ' jiwa/km²'; } },
        ],
    },
}).render();

function setView(v) {
    document.getElementById('btn-chart-view').classList.toggle('active', v === 'chart');
    document.getElementById('btn-table-view').classList.toggle('active', v === 'table');
}

// ── Export tabel ke CSV (kompatibel Excel) ────────────────────
function exportTableCSV() {
    var table = document.getElementById('geo-table');
    var rows = [];

    // Header
    var head = [];
    table.querySelectorAll('thead th').forEach(function(th) { head.push(th.textContent.trim()); });
    rows.push(head);

    // Semua baris (abaikan pagination display:none), buang pemisah ribuan agar angka bersih
    table.querySelectorAll('tbody tr').forEach(function(tr) {
        var row = [];
        tr.querySelectorAll('td').forEach(function(td, i) {
            var txt = td.textContent.trim();
            if (i > 0) txt = txt.replace(/\./g, '').replace(',', '.');  // "1.234,5" → "1234.5"
            row.push(txt);
        });
        rows.push(row);
    });

    var csv = rows.map(function(r) {
        return r.map(function(c) { return '"' + String(c).replace(/"/g, '""') + '"'; }).join(',');
    }).join('\r\n');

    // BOM agar UTF-8 terbaca benar di Excel
    var blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8;' });
    var url  = URL.createObjectURL(blob);
    var a    = document.createElement('a');
    a.href = url;
    a.download = 'tabel-geografis-jakarta-barat.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>

<script>
// ── Leaflet Map ───────────────────────────────────────────────
var map = L.map('geo-map').setView([-6.15, 106.76], 12);

// Basemap satelit (default)
var satelit = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    attribution: 'Tiles © Esri', maxZoom: 19
}).addTo(map);

// Opsi lain
var positron = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '© OpenStreetMap, © CARTO', subdomains: 'abcd', maxZoom: 19
});
var jalan = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
});

L.control.layers(
    { 'Satelit': satelit, 'Peta Terang': positron, 'Peta Jalan': jalan },
    {},
    { position: 'topright' }
).addTo(map);

var luasByNama = {};
namaKec.forEach(function(n, i) { luasByNama[n.toUpperCase()] = luasData[i]; });

var kecJakbar = namaKec.map(function(n){ return n.toUpperCase(); });

// ── GeoJSON Polygon + Legend Kecamatan ───────────────────────
fetch('{{ asset("assets/geojson/kecamatan.geojson") }}')
    .then(function(r) { return r.json(); })
    .then(function(data) {
        data.features = data.features.filter(function(f) {
            return kecJakbar.includes((f.properties.name || '').toUpperCase());
        });
        var activeLayer = null;
        var layerMap   = {};   // nama_kecamatan.toUpperCase() → layer

        var geoLayer = L.geoJSON(data, {
            style: function(feature) {
                return {
                    color: '#fff', weight: 2,
                    fillColor: getWarna(feature.properties.name || ''),
                    fillOpacity: 0.62,
                };
            },
            onEachFeature: function(feature, layer) {
                var nama   = feature.properties.name || '';
                var namaUp = nama.toUpperCase();
                var luas   = luasByNama[namaUp] ? luasByNama[namaUp].toFixed(2) + ' km²' : '-';

                // Simpan referensi layer
                layerMap[namaUp] = layer;

                layer.on('mouseover', function() {
                    if (layer !== activeLayer) layer.setStyle({ fillOpacity: 0.8 });
                });
                layer.on('mouseout', function() {
                    if (layer !== activeLayer) layer.setStyle({ fillOpacity: 0.62, weight: 1.5, color: '#fff' });
                });
                layer.on('click', function() {
                    focusKecamatan(namaUp);
                });
            }
        }).addTo(map);

        // Tampilkan kembali semua kecamatan (reset)
        function resetKecamatan() {
            Object.keys(layerMap).forEach(function(key) {
                var l = layerMap[key];
                if (!map.hasLayer(l)) l.addTo(map);
                l.setStyle({ fillOpacity: 0.62, weight: 1.5, color: '#fff' });
                l.closePopup();
            });
            activeLayer = null;
            map.fitBounds(geoLayer.getBounds(), { padding: [30, 30] });
            resetCards();

            document.querySelectorAll('.legend-kec-item').forEach(function(el) {
                el.style.fontWeight = '400';
                el.style.background = 'transparent';
                el.style.transform = 'none';
                el.style.borderRadius = '4px';
            });
        }

        // Fungsi highlight + zoom — bisa dipanggil dari layer maupun legend
        function focusKecamatan(namaUp) {
            var layer = layerMap[namaUp];
            if (!layer) return;

            // Klik kecamatan yang sama → kembalikan tampilan semua kecamatan
            if (activeLayer === layer) {
                resetKecamatan();
                return;
            }

            var luas = luasByNama[namaUp] ? luasByNama[namaUp].toFixed(2) + ' km²' : '-';

            // Sembunyikan semua kecamatan lain, tampilkan hanya yang diklik
            Object.keys(layerMap).forEach(function(key) {
                var l = layerMap[key];
                if (l === layer) {
                    if (!map.hasLayer(l)) l.addTo(map);
                } else if (map.hasLayer(l)) {
                    map.removeLayer(l);
                }
            });

            layer.setStyle({ fillOpacity: 0.82, weight: 2.5, color: '#fff' });
            layer.bringToFront();
            activeLayer = layer;

            // Zoom ke kecamatan; pastikan minimal zoom 13 agar kecamatan
            // besar seperti Kalideres tetap terlihat ter-zoom (bukan diam di 12)
            var bounds = layer.getBounds();
            var fitZoom = map.getBoundsZoom(bounds, false, L.point(30, 30));
            var targetZoom = Math.max(13, Math.min(14, fitZoom));
            map.flyTo(bounds.getCenter(), targetZoom);
            layer.bindPopup('<b>Kec. ' + namaUp + '</b><br>📐 ' + luas).openPopup();

            // Highlight baris legend aktif
            document.querySelectorAll('.legend-kec-item').forEach(function(el) {
                el.style.fontWeight = el.dataset.nama === namaUp ? '700' : '400';
                el.style.background = el.dataset.nama === namaUp ? '#fffbf0' : 'transparent';
                el.style.borderRadius = '4px';
            });

            // Update card ringkasan agar berelasi dengan kecamatan terpilih
            updateCards(namaUp);
        }

        // Ekspos agar bisa dipanggil dari klik chart (bar / donut)
        window.focusKecamatan = focusKecamatan;

        // Legend kecamatan — kompak, tiap baris bisa diklik & ber-hover dinamis
        var kecLegend = L.control({ position: 'bottomright' });
        kecLegend.onAdd = function() {
            var div = L.DomUtil.create('div', 'kec-legend');
            div.style.cssText = 'background:rgba(255,255,255,0.95);padding:6px 8px;border-radius:6px;font-size:10px;line-height:1.4;box-shadow:0 1px 4px rgba(0,0,0,0.18);backdrop-filter:blur(2px);';
            div.innerHTML = '<b style="font-size:10px;letter-spacing:.3px;color:#555;">KECAMATAN</b>';
            // Cegah peta ikut zoom/geser saat berinteraksi dengan legend
            L.DomEvent.disableClickPropagation(div);
            L.DomEvent.disableScrollPropagation(div);

            kecJakbar.forEach(function(nama) {
                var luas = luasByNama[nama] ? luasByNama[nama].toFixed(2) + ' km²' : '-';
                var row  = L.DomUtil.create('div', 'legend-kec-item', div);
                row.dataset.nama  = nama;
                row.style.cssText = 'display:flex;align-items:center;gap:5px;padding:2px 5px;margin-top:2px;cursor:pointer;border-radius:4px;transition:background .15s,transform .15s;transform-origin:left center;';
                row.innerHTML = '<span style="display:inline-block;width:9px;height:9px;border-radius:2px;flex-shrink:0;background:' + getWarna(nama) + ';"></span>'
                    + '<span style="white-space:nowrap;">' + nama + ' <b style="color:#777;font-weight:600;">' + luas + '</b></span>';

                function isActive() { return row.style.fontWeight === '700'; }
                row.addEventListener('mouseover', function() {
                    if (!isActive()) { row.style.background = '#f0f4ff'; row.style.transform = 'translateX(2px)'; }
                    var l = layerMap[nama];
                    if (l && l !== activeLayer && map.hasLayer(l)) l.setStyle({ fillOpacity: 0.8 });
                });
                row.addEventListener('mouseout', function() {
                    if (!isActive()) { row.style.background = 'transparent'; row.style.transform = 'none'; }
                    var l = layerMap[nama];
                    if (l && l !== activeLayer && map.hasLayer(l)) l.setStyle({ fillOpacity: 0.62, weight: 1.5, color: '#fff' });
                });
                row.addEventListener('click', function() { focusKecamatan(nama); });
            });
            return div;
        };
        kecLegend.addTo(map);
    });

// ── Table Pagination & Search ─────────────────────────────────
var PAGE_SIZE = 4, currentPage = 1, filteredRows = [];

function getAllRows() { return Array.from(document.querySelectorAll('#geo-table-body tr')); }

function filterTable() {
    var q = document.getElementById('geo-search').value.toLowerCase();
    filteredRows = getAllRows().filter(function(r) { return r.dataset.name.includes(q); });
    currentPage = 1;
    renderTable();
}

function renderTable() {
    var rows  = filteredRows.length ? filteredRows : getAllRows();
    var total = rows.length;
    var start = (currentPage - 1) * PAGE_SIZE;
    var end   = Math.min(start + PAGE_SIZE, total);

    getAllRows().forEach(function(r) { r.style.display = 'none'; });
    rows.slice(start, end).forEach(function(r) { r.style.display = ''; });

    document.getElementById('pager-info').textContent = 'Showing ' + (start+1) + '–' + end + ' of ' + total + ' Kecamatan';

    var pages = Math.ceil(total / PAGE_SIZE);
    var pager = document.getElementById('geo-pager');
    pager.innerHTML = '';

    var prev = document.createElement('button');
    prev.innerHTML = '&lsaquo;'; prev.disabled = currentPage === 1;
    prev.onclick = function() { currentPage--; renderTable(); };
    pager.appendChild(prev);

    for (var p = 1; p <= pages; p++) {
        (function(pg) {
            var btn = document.createElement('button');
            btn.textContent = pg;
            if (pg === currentPage) btn.classList.add('active');
            btn.onclick = function() { currentPage = pg; renderTable(); };
            pager.appendChild(btn);
        })(p);
    }

    var next = document.createElement('button');
    next.innerHTML = '&rsaquo;'; next.disabled = currentPage === pages;
    next.onclick = function() { currentPage++; renderTable(); };
    pager.appendChild(next);
}

document.addEventListener('DOMContentLoaded', function() { renderTable(); });
</script>
@endpush
