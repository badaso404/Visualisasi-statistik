@extends('landing-page.layout.app')
@section('page_title', __('geografis.page_title') . ' - Jakarta Barat')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="{{ asset('assets/statistik/css/geografis.css') }}">

@endpush

@section('content')
<div class="container-fluid px-4">
<div class="statistik-wrapper">

    @include('statistik.partials.sidebar')

    {{-- KONTEN --}}
    <div class="statistik-content">

        {{-- Header --}}
        <div class="stat-header-wrap">
            <div class="stat-header">{{ __('geografis.header', ['tahun' => $geo->tahun]) }}</div>
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
                        <div class="label" id="lbl-luas">{{ __('geografis.card_luas') }}</div>
                        <div class="value"><span id="val-luas">{{ nf($geo->luas_kota_km2, 2) }}</span><small>km²</small></div>
                    </div>
                    <div class="card-icon" style="background:#2a78d6; margin-left:auto;">
                        <i class="fa fa-map" style="color:#fff;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-summary-card" id="card-kec">
                    <div class="card-text">
                        <div class="label" id="lbl-kec">{{ __('geografis.card_kec') }}</div>
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
                        <div class="label" id="lbl-kel">{{ __('geografis.card_kel') }}</div>
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
                        <div class="label" id="lbl-padat">{{ __('geografis.card_padat') }}</div>
                        <div class="value"><span id="val-padat">{{ nf($totalKepadatan, 0) }}</span><small>/km²</small></div>
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
                    <div class="chart-title">{{ __('geografis.chart_bar_title') }}</div>
                    <div id="chart-bar-luas"></div>
                </div>
                <div class="chart-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="chart-title" style="margin-bottom:0;">{{ __('geografis.chart_donut_title') }}</div>
                        <div style="font-size:11px;color:#aaa;">{{ __('geografis.chart_donut_total', ['luas' => nf($geo->luas_kota_km2, 1)]) }}</div>
                    </div>
                    <div id="chart-donut-persen"></div>
                </div>
            </div>

            {{-- RIGHT: Map --}}
            <div class="chart-card" style="margin-bottom:0; display:flex; flex-direction:column; gap:10px;">
                <div class="chart-title" style="margin-bottom:0;">{{ __('geografis.map_title') }}</div>
                <div id="geo-map"></div>
            </div>
        </div>

        {{-- Comparison Chart --}}
        <div class="chart-card">
            <div class="chart-title-row">
                <div>
                    <div class="chart-title" style="margin-bottom:2px;">{{ __('geografis.chart_compare_title') }}</div>
                    <div class="chart-sub">{{ __('geografis.chart_compare_sub') }}</div>
                </div>
            </div>
            <div id="chart-compare"></div>
        </div>

        {{-- Highlight Cards --}}
        @php
            // Hanya baris yang kecamatannya sudah terisi — hindari null saat data tahun tertentu belum lengkap
            $sortedLuas = $luas->filter(fn($r) => $r->kecamatan !== null)->sortByDesc('luas_km2');
            $terluas    = $sortedLuas->first();
            $terkecil   = $sortedLuas->last();
        @endphp
        <div class="geo-highlight-grid">
            <div class="geo-hl-card">
                <div class="hl-icon" style="background:#E5ECF5;"><i class="fa fa-expand-arrows-alt" style="color:#34527A;"></i></div>
                <div>
                    <div class="hl-tag" style="color:#34527A;">{{ __('geografis.hl_terluas') }}</div>
                    <div class="hl-name">{{ $terluas ? __('geografis.hl_nama', ['nama' => $terluas->kecamatan->nama_kecamatan]) : __('geografis.hl_kosong') }}</div>
                    <div class="hl-sub">{{ $terluas ? __('geografis.hl_sub', ['luas' => nf($terluas->luas_km2, 2), 'persen' => nf($terluas->persentase, 1)]) : '—' }}</div>
                </div>
            </div>
            <div class="geo-hl-card">
                <div class="hl-icon" style="background:#EDF1F8;"><i class="fa fa-compress-arrows-alt" style="color:#7B97C2;"></i></div>
                <div>
                    <div class="hl-tag" style="color:#5B7BB0;">{{ __('geografis.hl_terkecil') }}</div>
                    <div class="hl-name">{{ $terkecil ? __('geografis.hl_nama', ['nama' => $terkecil->kecamatan->nama_kecamatan]) : __('geografis.hl_kosong') }}</div>
                    <div class="hl-sub">{{ $terkecil ? __('geografis.hl_sub', ['luas' => nf($terkecil->luas_km2, 2), 'persen' => nf($terkecil->persentase, 1)]) : '—' }}</div>
                </div>
            </div>
            <div class="geo-hl-card">
                <div class="hl-icon" style="background:#E5ECF5;"><i class="fa fa-users" style="color:#4A6FA5;"></i></div>
                <div>
                    <div class="hl-tag" style="color:#4A6FA5;">{{ __('geografis.hl_terpadat') }}</div>
                    <div class="hl-name">{{ __('geografis.hl_nama', ['nama' => 'Tambora']) }}</div>
                    <div class="hl-sub">{{ __('geografis.hl_padat_val', ['nilai' => nf(48243)]) }}</div>
                </div>
            </div>
        </div>

        {{-- ── TABEL GEOGRAFIS RINCI ───────────────────────────── --}}
        <x-statistik.tabel
            id="geo-table"
            :judul="__('geografis.table_title')"
            subjudul="Data wilayah kecamatan di Jakarta Barat"
            :kolom="[
                __('geografis.col_kecamatan'),
                __('geografis.col_luas'),
                __('geografis.col_kelurahan'),
                __('geografis.col_rw'),
                __('geografis.col_rt'),
                __('geografis.col_populasi'),
                __('geografis.col_kepadatan'),
            ]"
            :per-halaman="5"
            :sumber="$geo->sumber"
            :berkas="__('geografis.table_file', ['tahun' => $tahun])"
        >
            @foreach($luas->sortByDesc('luas_km2') as $row)
                @continue($row->kecamatan === null)

                @php
                    $s = $kecStats[strtoupper($row->kecamatan->nama_kecamatan)] ?? null;
                @endphp

                <tr data-cari="{{ strtolower($row->kecamatan->nama_kecamatan) }}">
                    <td>
                        <div class="stat-sel-label">
                            <span class="stat-rank" data-unduh-abaikan>{{ $loop->iteration }}</span>
                            <span class="stat-nama">{{ $row->kecamatan->nama_kecamatan }}</span>
                        </div>
                    </td>
                    <td><span class="stat-nilai">{{ nf($row->luas_km2, 2) }}</span></td>
                    @foreach (['kelurahan', 'rw', 'rt', 'penduduk', 'kepadatan'] as $kunci)
                        <td>
                            <span class="stat-nilai {{ $s && $s[$kunci] ? '' : 'stat-nilai-kosong' }}">
                                {{ $s && $s[$kunci] ? nf($s[$kunci], 0) : '—' }}
                            </span>
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </x-statistik.tabel>

</div> {{-- END statistik-content --}}

</div> {{-- END statistik-wrapper --}}

</div> {{-- END container-fluid --}}

@endsection

@push('scripts')
@include('statistik.partials.warna-kecamatan')
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
// Pemisah ribuan/desimal ikut bahasa aktif (lihat helper nf()).
var idID = '{{ locale_angka_js() }}';
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
    luas:  { label: @json(__('geografis.card_luas')),  val: '{{ nf($geo->luas_kota_km2, 2) }}' },
    kec:   { label: @json(__('geografis.card_kec')),   val: '{{ $jumlahKecamatan }}', unit: '' },
    kel:   { label: @json(__('geografis.card_kel')),   val: '{{ nf($totalKelurahan, 0) }}' },
    padat: { label: @json(__('geografis.card_padat')), val: '{{ nf($totalKepadatan, 0) }}' },
};

// Label kartu saat satu kecamatan dipilih. :nama diganti di sisi JS karena
// nama kecamatannya baru diketahui setelah pengunjung mengklik.
var cardLabelLuasKec = @json(__('geografis.card_luas_kec'));

function updateCards(namaUp) {
    var s = kecStatsData[namaUp];
    if (!s) return;
    setText('lbl-luas', cardLabelLuasKec.replace(':nama', s.nama.toUpperCase()));
    setText('val-luas', fmtNum(s.luas, 2));
    setText('lbl-kec', @json(__('geografis.card_persen')));
    setText('val-kec', fmtNum(s.persentase, 2));
    setText('unit-kec', '%');
    setText('lbl-kel', @json(__('geografis.card_kel')));
    setText('val-kel', s.kelurahan ? fmtNum(s.kelurahan) : '-');
    setText('lbl-padat', @json(__('geografis.card_padat_kec')));
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
    series: [{ name: @json(__('geografis.series_luas')), data: luasData }],
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
        total: { show: true, label: @json(__('geografis.chart_donut_center')), fontSize: '12px',
                 formatter: function() { return '{!! nf($geo->luas_kota_km2, 1) !!} km²'; } }
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
        { name: @json(__('geografis.series_luas_full')), data: luasData },
        { name: @json(__('geografis.series_padat')),     data: kepadatanData },
    ],
    xaxis: {
        categories: namaKec,
        labels: { rotate: -30, rotateAlways: true, style: { fontSize: '10px' }, trim: false }
    },
    // Dua sumbu terpisah → skala luas & kepadatan mandiri, bar luas tak lagi kekecilan
    yaxis: [
        { seriesName: @json(__('geografis.series_luas_full')),
          title: { text: @json(__('geografis.series_luas')), style: { fontSize: '9px', color: '#4A6FA5' } },
          labels: { style: { fontSize: '9px', colors: '#4A6FA5' }, formatter: function(v){ return v.toFixed(0); } } },
        { seriesName: @json(__('geografis.series_padat')), opposite: true,
          title: { text: @json(__('geografis.axis_padat')), style: { fontSize: '9px', color: '#F5A623' } },
          labels: { style: { fontSize: '9px', colors: '#F5A623' }, formatter: function(v){ return (v/1000).toFixed(0) + @json(__('geografis.ribuan_singkat')); } } },
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
            { formatter: function(v){ return Number(v).toLocaleString(idID) + ' ' + @json(__('geografis.col_kepadatan')); } },
        ],
    },
}).render();

function setView(v) {
    document.getElementById('btn-chart-view').classList.toggle('active', v === 'chart');
    document.getElementById('btn-table-view').classList.toggle('active', v === 'table');
}

// Unduh CSV ditangani statistik.partials.unduh-tabel (dipakai semua modul).
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
            layer.bindPopup('<b>' + @json(__('geografis.map_popup')).replace(':nama', namaUp) + '</b><br>📐 ' + luas).openPopup();

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
            div.innerHTML = '<b style="font-size:10px;letter-spacing:.3px;color:#555;">' + @json(__('geografis.legend_title')) + '</b>';
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

// Pencarian & paginasi tabel ditangani komponen statistik.tabel.

</script>

@endpush