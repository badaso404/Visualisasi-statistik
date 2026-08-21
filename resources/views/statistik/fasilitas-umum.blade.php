@extends('landing-page.layout.app')
@section('page_title', __('fasilitas.page_title') . ' - Jakarta Barat')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css"/>
<style>
    /* ── Layout ─────────────────────────────────────────────── */
    .kes-wrapper  { display:flex; gap:24px; padding:40px 0; }
    .kes-content  { flex:1; min-width:0; }

    /* ── Page header ────────────────────────────────────────── */
    .stat-header-wrap { display:flex; align-items:center; gap:12px; margin-bottom:24px; }
    .stat-header {
        flex:1; background:#ffbf00; color:#fff; text-align:center;
        padding:14px; border-radius:8px; font-weight:700;
        font-size:18px; letter-spacing:1px;
    }

    /* ── Stat cards ─────────────────────────────────────────── */
    .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:16px; }
    .stat-card {
        background:#f9f9f9; border:1px solid #eee; border-radius:8px;
        padding:16px 24px; position:relative; overflow:hidden;
    }
    .sc-card-body  { display:flex; justify-content:space-between; align-items:flex-start; margin-top:8px; }
    .sc-card-left  { flex:1; min-width:0; }
    .sc-icon {
        width:48px; height:48px; border-radius:12px;
        display:flex; align-items:center; justify-content:center;
        font-size:22px; flex-shrink:0; margin-left:12px;
        background:#2a78d6; color:#fff;
    }
    .sc-icon.ic-blue   { background:#2a78d6; }
    .sc-icon.ic-orange { background:#eb6834; }
    .sc-icon.ic-green  { background:#008300; }
    .sc-icon.ic-violet { background:#4a3aa7; }
    .sc-label { font-size:12px; font-weight:600; color:#888; letter-spacing:1px; text-transform:uppercase; margin-bottom:4px; }
    .sc-value { font-size:28px; font-weight:700; color:#333; line-height:1.15; margin-bottom:6px; }
    .sc-value.sm { font-size:19px; }
    .sc-desc  { font-size:11px; color:#aaa; }

    /* ── Panel card ─────────────────────────────────────────── */
    .panel-card { background:#fff; border:1px solid #ebebeb; border-radius:12px; padding:22px; }
    .pc-header  { display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; }
    .pc-title   { font-size:15px; font-weight:700; color:#1a1a1a; margin:0; display:flex; align-items:center; gap:8px; }
    .pc-title i { color:#ffbf00; }

    .fas-grid { display:grid; grid-template-columns:2fr 1fr; gap:16px; margin-bottom:16px; }

    /* ── Legenda kategori ───────────────────────────────────── */
    .kat-legend { display:flex; flex-wrap:wrap; gap:10px 16px; font-size:11px; color:#666; font-weight:600; margin-top:14px; }
    .kat-legend .dot { width:9px; height:9px; border-radius:50%; display:inline-block; margin-right:5px; }

    /* ── Peta ───────────────────────────────────────────────── */
    .map-card  { background:#fff; border:1px solid #ebebeb; border-radius:12px; padding:22px; margin-bottom:16px; }
    .fas-map   { width:100%; height:420px; border-radius:8px; border:1px solid #eee; z-index:0; }
    .map-note  { font-size:11px; color:#aaa; margin-top:10px; }
    .map-empty {
        display:flex; align-items:center; justify-content:center; text-align:center;
        height:180px; border:1px dashed #e0e0e0; border-radius:8px;
        color:#aaa; font-size:13px; padding:20px;
    }

    /* ── Table card ─────────────────────────────────────────── */
    .table-card   { background:#fff; border:1px solid #ebebeb; border-radius:12px; padding:22px; margin-bottom:16px; }
    .table-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:12px; }
    .table-title  { font-size:15px; font-weight:700; color:#1a1a1a; margin:0; }
    .table-sub    { font-size:11px; color:#aaa; margin:2px 0 0; }
    .tbl-tools    { display:flex; gap:8px; flex-wrap:wrap; }
    .tbl-btn, .tbl-input, .tbl-select {
        border:1px solid #e0e0e0; background:#fff; color:#555;
        font-size:13px; font-weight:600; padding:7px 12px; border-radius:8px;
    }
    .tbl-btn { display:inline-flex; align-items:center; gap:6px; cursor:pointer; transition:all .15s; }
    .tbl-btn:hover { border-color:#ffbf00; color:#b8860b; }
    .tbl-input { font-weight:400; min-width:220px; }
    .tbl-input:focus, .tbl-select:focus { outline:none; border-color:#ffbf00; }

    .kes-table { width:100%; border-collapse:collapse; }
    .kes-table th {
        font-size:11px; font-weight:700; color:#9e9e9e;
        text-transform:uppercase; letter-spacing:.5px;
        padding:10px 14px; border-bottom:1px solid #f0f0f0; text-align:left;
    }
    .kes-table td { padding:12px 14px; font-size:13px; color:#333; border-bottom:1px solid #f9f9f9; vertical-align:top; }
    .kes-table tr:last-child td { border-bottom:none; }
    .kes-table tr:hover td { background:#fafafa; }
    .td-alamat { color:#888; font-size:12px; max-width:340px; }
    .td-nama   { font-weight:600; }

    .badge-kat {
        display:inline-flex; align-items:center; gap:5px; white-space:nowrap;
        font-size:11px; font-weight:700; padding:4px 10px; border-radius:20px;
        color:#fff;
    }
    /* Warna lencana lewat kelas, bukan style sebaris: dengan 776 baris,
       mengulang atribut style di tiap baris menambah puluhan kilobyte
       untuk enam nilai warna yang itu-itu saja. */
@foreach (\App\Models\FasilitasUmum::WARNA as $slug => $warna)
    .badge-kat.kat-{{ $slug }} { background: {{ $warna }}; }
@endforeach
    .badge-kosong { background:#f0f0f0; color:#999; font-size:11px; padding:3px 8px; border-radius:20px; }

    /* ── Pagination ─────────────────────────────────────────── */
    .pager { display:flex; align-items:center; justify-content:space-between; margin-top:16px; flex-wrap:wrap; gap:12px; }
    .pager-info { font-size:12px; color:#999; }
    .pager-btns { display:flex; gap:6px; flex-wrap:wrap; }
    .page-btn {
        min-width:34px; height:34px; padding:0 10px; border:1px solid #e0e0e0;
        border-radius:8px; background:#fff; color:#555; font-weight:600; font-size:13px; cursor:pointer; transition:all .15s;
    }
    .page-btn:hover:not(:disabled) { border-color:#ffbf00; color:#b8860b; }
    .page-btn.active { background:#ffbf00; border-color:#ffbf00; color:#fff; }
    .page-btn:disabled { opacity:.45; cursor:not-allowed; }

    /* ── Footer ─────────────────────────────────────────────── */
    .kes-footer { font-size:11px; color:#bbb; text-align:right; margin-top:8px; }

    /* ── Responsive ─────────────────────────────────────────── */
    @media (max-width: 992px) {
        .stat-grid { grid-template-columns: repeat(2,1fr); }
        .fas-grid  { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .kes-wrapper { flex-direction: column; padding: 20px 0; gap: 16px; }
        .stat-header { font-size: 15px; padding: 12px; }
        .table-card  { overflow-x: auto; }
        .kes-table   { min-width: 720px; }
        .tbl-input   { min-width: 0; width: 100%; }
    }
    @media (max-width: 520px) {
        .stat-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
@php
    // Nama kelas ditulis lengkap sekali di sini lalu dipakai lewat variabel,
    // supaya sisa view tidak berulang-ulang menyebut namespace-nya.
    $warnaKategori  = \App\Models\FasilitasUmum::WARNA;
    $ikonKategori   = \App\Models\FasilitasUmum::IKON;
    $daftarKategori = \App\Models\FasilitasUmum::KATEGORI;
    $labelKategori  = fn ($slug) => \App\Models\FasilitasUmum::label($slug);
    $terakhir       = $semua->max('updated_at');
@endphp

<div class="container-fluid px-4">
    <div class="kes-wrapper">

        @include('statistik.partials.sidebar')

        {{-- ── KONTEN ───────────────────────────────────── --}}
        <div class="kes-content">

            {{-- Header. Tidak ada dropdown tahun di modul ini: data sumbernya
                 berupa inventaris tanpa penanda periode. --}}
            <div class="stat-header-wrap">
                <div class="stat-header">{{ __('fasilitas.header') }}</div>
            </div>

            {{-- ── 4 Stat Cards ────────────────────────── --}}
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">{{ __('fasilitas.card_total') }}</div>
                            <div class="sc-value">{{ nf($ringkasan['total']) }}</div>
                            <div class="sc-desc">{{ __('fasilitas.card_total_desc', ['jumlah' => nf($ringkasan['kecamatan_terisi'])]) }}</div>
                        </div>
                        <div class="sc-icon ic-blue"><i class="fa fa-building-columns"></i></div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">{{ __('fasilitas.card_kategori') }}</div>
                            <div class="sc-value sm">
                                {{ $ringkasan['kategori_top'] ? $labelKategori($ringkasan['kategori_top']) : '—' }}
                            </div>
                            <div class="sc-desc">{{ __('fasilitas.card_kategori_desc', ['jumlah' => nf($ringkasan['kategori_top_n'])]) }}</div>
                        </div>
                        <div class="sc-icon ic-violet">
                            <i class="fa {{ $ikonKategori[$ringkasan['kategori_top']] ?? 'fa-layer-group' }}"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">{{ __('fasilitas.card_kecamatan') }}</div>
                            <div class="sc-value sm">{{ $ringkasan['kecamatan_top'] ?? '—' }}</div>
                            <div class="sc-desc">{{ __('fasilitas.card_kecamatan_desc', ['jumlah' => nf($ringkasan['kecamatan_top_n'])]) }}</div>
                        </div>
                        <div class="sc-icon ic-orange"><i class="fa fa-map-location-dot"></i></div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="sc-card-body">
                        <div class="sc-card-left">
                            <div class="sc-label">{{ __('fasilitas.card_rasio') }}</div>
                            <div class="sc-value">{{ $ringkasan['rasio'] !== null ? nf($ringkasan['rasio'], 2) : '—' }}</div>
                            <div class="sc-desc">
                                {{ $ringkasan['rasio'] !== null
                                    ? __('fasilitas.card_rasio_desc', ['tahun' => $ringkasan['tahun_penduduk']])
                                    : __('fasilitas.card_rasio_kosong') }}
                            </div>
                        </div>
                        <div class="sc-icon ic-green"><i class="fa fa-users-between-lines"></i></div>
                    </div>
                </div>
            </div>

            {{-- ── Grafik ──────────────────────────────── --}}
            <div class="fas-grid">
                <div class="panel-card">
                    <div class="pc-header">
                        <h3 class="pc-title"><i class="fa fa-chart-column"></i> {{ __('fasilitas.panel_sebaran') }}</h3>
                    </div>
                    <div id="chart-sebaran"></div>
                    <div class="kat-legend">
                        @foreach ($daftarKategori as $slug => $label)
                            <span><span class="dot" style="background: {{ $warnaKategori[$slug] }}"></span>{{ $labelKategori($slug) }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="panel-card">
                    <div class="pc-header">
                        <h3 class="pc-title"><i class="fa fa-chart-pie"></i> {{ __('fasilitas.panel_komposisi') }}</h3>
                    </div>
                    <div id="chart-komposisi"></div>
                </div>
            </div>

            {{-- ── Peta ────────────────────────────────── --}}
            <div class="map-card">
                <div class="pc-header">
                    <h3 class="pc-title"><i class="fa fa-map-location-dot"></i> {{ __('fasilitas.map_title') }}</h3>
                </div>

                @if ($titik->isEmpty())
                    {{-- Peta kosong tanpa keterangan mudah disalahpahami sebagai
                         "tidak ada fasilitas", padahal datanya ada — yang belum
                         ada koordinatnya. --}}
                    <div class="map-empty">{{ __('fasilitas.map_kosong') }}</div>
                @else
                    <div id="map-fasilitas" class="fas-map"></div>
                    <div class="map-note">
                        {{ __('fasilitas.map_catatan', [
                            'tanpa' => nf($ringkasan['total'] - $titik->count()),
                            'total' => nf($ringkasan['total']),
                        ]) }}
                    </div>
                @endif
            </div>

            {{-- ── Tabel ───────────────────────────────── --}}
            <div class="table-card">
                <div class="table-header">
                    <div>
                        <h3 class="table-title">{{ __('fasilitas.table_title') }}</h3>
                        <p class="table-sub">{{ __('fasilitas.table_sub', ['total' => nf($ringkasan['total'])]) }}</p>
                    </div>
                    <div class="tbl-tools">
                        <input type="search" id="cariFasilitas" class="tbl-input" placeholder="{{ __('fasilitas.cari') }}">
                        <select id="filterKategori" class="tbl-select">
                            <option value="ALL">{{ __('fasilitas.filter_semua') }}</option>
                            @foreach ($daftarKategori as $slug => $label)
                                <option value="{{ $slug }}">{{ $labelKategori($slug) }} ({{ $perKategori[$slug] ?? 0 }})</option>
                            @endforeach
                        </select>
                        <button class="tbl-btn" id="exportCsv"><i class="fa fa-download"></i> {{ __('common.unduh_csv') }}</button>
                    </div>
                </div>

                <table class="kes-table">
                    <thead>
                        <tr>
                            <th>{{ __('fasilitas.col_nama') }}</th>
                            <th>{{ __('fasilitas.col_kategori') }}</th>
                            <th>{{ __('fasilitas.col_kecamatan') }}</th>
                            <th>{{ __('fasilitas.col_kelurahan') }}</th>
                            <th>{{ __('fasilitas.col_alamat') }}</th>
                        </tr>
                    </thead>
                    {{-- Ratusan baris dicetak sekaligus supaya pencarian dan
                         penyaringan berjalan tanpa memuat ulang halaman. Karena
                         itu markup per barisnya ditulis rapat: indentasi Blade
                         yang wajar untuk 8 baris menjadi ratusan kilobyte
                         spasi kosong pada 776 baris. Kunci pencarian juga tidak
                         ditulis sebagai atribut data — JS menyusunnya sendiri
                         dari teks baris saat halaman siap. --}}
                    <tbody id="fasilitas-tbody">
                        @forelse ($semua as $f)
                        <tr class="fasilitas-row" data-kategori="{{ $f->kategori }}"><td class="td-nama">{{ $f->nama }}</td><td><span class="badge-kat kat-{{ $f->kategori }}"><i class="fa {{ $f->ikon() }}"></i> {{ $f->labelKategori() }}</span></td><td>@if ($f->kecamatan){{ $f->kecamatan->nama_kecamatan }}@else<span class="badge-kosong">{{ __('fasilitas.belum_diisi') }}</span>@endif</td><td>{{ $f->kelurahan ?: '-' }}</td><td class="td-alamat">{{ $f->alamat ?: '-' }}</td></tr>
                        @empty
                        <tr><td colspan="5" class="text-center" style="color:#bbb;padding:20px;">{{ __('fasilitas.empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div id="tidakAdaHasil" style="display:none;color:#bbb;text-align:center;padding:20px;font-size:13px;">
                    {{ __('fasilitas.empty_cari') }}
                </div>

                <div class="pager">
                    <div class="pager-info" id="pagerInfo"></div>
                    <div class="pager-btns" id="pagerBtns"></div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="kes-footer">
                {!! __('fasilitas.source', [
                    'tanggal' => $terakhir ? \Carbon\Carbon::parse($terakhir)->translatedFormat('d F Y') : '-',
                ]) !!}
            </div>

        </div>{{-- /.kes-content --}}
    </div>{{-- /.kes-wrapper --}}
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
(function () {
    // ── Peta titik fasilitas ──────────────────────────────────────
    // Berbeda dari peta infrastruktur digital yang posisinya digenerate acak
    // di dalam polygon kecamatan: di sini tiap titik adalah koordinat asli
    // sebuah fasilitas, jadi tidak ada yang perlu disebar-sebar sendiri.
    var titik = {!! json_encode($titik) !!};
    var el = document.getElementById('map-fasilitas');
    if (typeof L === 'undefined' || !el || !titik.length) return;

    var map = L.map('map-fasilitas', { scrollWheelZoom: false }).setView([-6.168, 106.785], 12);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        maxZoom: 19, attribution: '&copy; OpenStreetMap &copy; CARTO'
    }).addTo(map);

    var bounds = [];
    titik.forEach(function (t) {
        L.circleMarker([t.lat, t.lng], {
            radius: 6, color: '#fff', weight: 1.5,
            fillColor: t.warna, fillOpacity: 0.9
        }).addTo(map).bindPopup(
            '<b>' + t.nama + '</b><br>' + t.kategori + '<br><span style="color:#888">' + t.kecamatan + '</span>'
        );
        bounds.push([t.lat, t.lng]);
    });

    if (bounds.length) map.fitBounds(bounds, { padding: [30, 30], maxZoom: 15 });
})();
</script>
<script>
(function () {
    // Pemisah ribuan/desimal ikut bahasa aktif (lihat helper nf()).
    var fmt = function (v) { return Number(v).toLocaleString('{{ locale_angka_js() }}'); };

    // ── Chart sebaran per kecamatan (batang bertumpuk per kategori) ──
    var kecNama = {!! json_encode($perKecamatan->pluck('nama')->values()) !!};
    var seri    = {!! json_encode(
        collect(App\Models\FasilitasUmum::KATEGORI)->keys()->map(fn ($slug) => [
            'name' => App\Models\FasilitasUmum::label($slug),
            'data' => $perKecamatan->map(fn ($r) => (int) ($r['kategori'][$slug] ?? 0))->values(),
        ])->values()
    ) !!};
    var warna = {!! json_encode(array_values(App\Models\FasilitasUmum::WARNA)) !!};

    if (document.querySelector('#chart-sebaran') && kecNama.length) {
        new ApexCharts(document.querySelector('#chart-sebaran'), {
            chart: {
                type: 'bar', height: 360, stacked: true, toolbar: { show: false },
                fontFamily: 'inherit', animations: { enabled: true, speed: 500 },
            },
            series: seri,
            colors: warna,
            plotOptions: { bar: { horizontal: true, borderRadius: 3, barHeight: '70%' } },
            dataLabels: { enabled: false },
            // Sekat setipis latar di antara segmen: tanpa ini dua kategori yang
            // bersebelahan pada batang bertumpuk terbaca seperti satu blok.
            stroke: { width: 2, colors: ['#fff'] },
            xaxis: {
                categories: kecNama,
                labels: { style: { fontSize: '11px', colors: '#888' }, formatter: fmt },
                axisBorder: { show: false }, axisTicks: { show: false },
            },
            yaxis: { labels: { style: { fontSize: '11px', colors: '#666' } } },
            // Legenda sudah digambar sendiri di bawah grafik (.kat-legend)
            // supaya warnanya konsisten dengan lencana pada tabel.
            legend: { show: false },
            grid: { borderColor: '#f0f0f0', strokeDashArray: 3, xaxis: { lines: { show: true } }, yaxis: { lines: { show: false } } },
            tooltip: { theme: 'light', y: { formatter: fmt } },
        }).render();
    }

    // ── Chart komposisi kategori (donat) ──────────────────────────
    var kompLabel = {!! json_encode(
        collect(App\Models\FasilitasUmum::KATEGORI)->keys()
            ->map(fn ($slug) => App\Models\FasilitasUmum::label($slug))->values()
    ) !!};
    var kompData = {!! json_encode(
        collect(App\Models\FasilitasUmum::KATEGORI)->keys()
            ->map(fn ($slug) => (int) ($perKategori[$slug] ?? 0))->values()
    ) !!};

    if (document.querySelector('#chart-komposisi') && kompData.some(function (v) { return v > 0; })) {
        new ApexCharts(document.querySelector('#chart-komposisi'), {
            chart: { type: 'donut', height: 360, fontFamily: 'inherit' },
            series: kompData,
            labels: kompLabel,
            colors: warna,
            legend: { position: 'bottom', fontSize: '11px', markers: { radius: 4 } },
            dataLabels: { enabled: true, style: { fontSize: '10px' }, dropShadow: { enabled: false } },
            plotOptions: { pie: { donut: { size: '58%' } } },
            stroke: { width: 2, colors: ['#fff'] },
            tooltip: { theme: 'light', y: { formatter: fmt } },
        }).render();
    }

    // ── Tabel: pencarian, filter kategori, pagination, export CSV ──
    var pageSize     = 15;
    var currentPage  = 1;
    var filterKat    = 'ALL';
    var kataCari     = '';
    var rows = Array.prototype.slice.call(document.querySelectorAll('#fasilitas-tbody tr.fasilitas-row'));

    // Kunci pencarian disusun sekali di sini, bukan dikirim sebagai atribut
    // data dari server: isinya cuma salinan teks yang sudah ada di baris, dan
    // 776 salinan itu memberatkan halaman tanpa menambah informasi apa pun.
    var kunci = rows.map(function (r) { return r.textContent.toLowerCase().replace(/\s+/g, ' '); });

    function filtered() {
        return rows.filter(function (r, i) {
            var cocokKat  = filterKat === 'ALL' || r.dataset.kategori === filterKat;
            var cocokCari = kataCari === '' || kunci[i].indexOf(kataCari) !== -1;
            return cocokKat && cocokCari;
        });
    }

    function render() {
        var fr = filtered();
        var totalPages = Math.max(1, Math.ceil(fr.length / pageSize));
        if (currentPage > totalPages) currentPage = totalPages;

        rows.forEach(function (r) { r.style.display = 'none'; });
        var start = (currentPage - 1) * pageSize;
        fr.slice(start, start + pageSize).forEach(function (r) { r.style.display = ''; });

        document.getElementById('tidakAdaHasil').style.display = fr.length ? 'none' : '';

        document.getElementById('pagerInfo').textContent = fr.length
            ? @json(__('fasilitas.pager_info'))
                .replace(':from', fmt(start + 1))
                .replace(':to', fmt(Math.min(start + pageSize, fr.length)))
                .replace(':total', fmt(fr.length))
            : @json(__('fasilitas.pager_empty'));

        buildPager(totalPages);
    }

    function buildPager(totalPages) {
        var box = document.getElementById('pagerBtns');
        box.innerHTML = '';
        var mk = function (label, page, opts) {
            opts = opts || {};
            var b = document.createElement('button');
            b.className = 'page-btn' + (opts.active ? ' active' : '');
            b.innerHTML = label;
            if (opts.disabled) b.disabled = true;
            else b.addEventListener('click', function () { currentPage = page; render(); });
            box.appendChild(b);
        };

        mk('&laquo;', currentPage - 1, { disabled: currentPage === 1 });

        // Daftarnya bisa puluhan halaman (600+ tempat ibadah), jadi hanya
        // jendela di sekitar halaman aktif yang ditampilkan — kalau semua
        // nomor dicetak, barisan tombolnya lebih panjang dari tabelnya.
        var dari = Math.max(1, currentPage - 2);
        var ke   = Math.min(totalPages, dari + 4);
        dari = Math.max(1, ke - 4);

        if (dari > 1) mk('1', 1, {});
        if (dari > 2) mk('…', 0, { disabled: true });
        for (var p = dari; p <= ke; p++) mk(p, p, { active: p === currentPage });
        if (ke < totalPages - 1) mk('…', 0, { disabled: true });
        if (ke < totalPages) mk(totalPages, totalPages, {});

        mk('&raquo;', currentPage + 1, { disabled: currentPage === totalPages });
    }

    document.getElementById('filterKategori').addEventListener('change', function () {
        filterKat = this.value; currentPage = 1; render();
    });

    // Pencarian ditunda sesaat: mengetik cepat di daftar 700+ baris akan
    // memicu render berulang kali per huruf tanpa jeda ini.
    var timer = null;
    document.getElementById('cariFasilitas').addEventListener('input', function () {
        var nilai = this.value.toLowerCase().trim();
        clearTimeout(timer);
        timer = setTimeout(function () {
            kataCari = nilai; currentPage = 1; render();
        }, 180);
    });

    document.getElementById('exportCsv').addEventListener('click', function () {
        var header = [
            @json(__('fasilitas.col_nama')),      @json(__('fasilitas.col_kategori')),
            @json(__('fasilitas.col_kecamatan')), @json(__('fasilitas.col_kelurahan')),
            @json(__('fasilitas.col_alamat')),
        ];
        // Mengikuti apa yang sedang terlihat (hasil filter + pencarian), bukan
        // seluruh tabel — sama seperti export pada modul infrastruktur digital.
        var lines = [header.join(',')];
        filtered().forEach(function (r) {
            var c = r.querySelectorAll('td');
            var sel = function (i) { return '"' + c[i].textContent.trim().replace(/"/g, '""').replace(/\s+/g, ' ') + '"'; };
            lines.push([sel(0), sel(1), sel(2), sel(3), sel(4)].join(','));
        });

        // BOM agar Excel membaca berkas sebagai UTF-8, sama seperti modul lain.
        var blob = new Blob(['﻿' + lines.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'fasilitas-umum-jakarta-barat.csv';
        a.click();
        URL.revokeObjectURL(a.href);
    });

    render();
})();
</script>
@endpush
