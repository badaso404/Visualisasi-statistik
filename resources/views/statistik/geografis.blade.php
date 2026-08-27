@extends('landing-page.layout.app')
@section('page_title', __('geografis.page_title') . ' - Jakarta Barat')

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
    .geo-table-wrap { background: #ffffff !important; border: 1px solid #e6ebf2 !important; border-radius: 20px !important; padding: 0 !important; margin-top: 24px !important; overflow: hidden !important; box-shadow: 0 8px 28px rgba(15, 23, 42, 0.07) !important; }
    .geo-table-top { padding: 28px 30px 16px !important }

   .geo-table-header { display: flex !important; align-items: flex-start !important; justify-content: space-between !important; gap: 28px !important; margin-bottom: 0 !important; }
   .geo-table-heading { flex: 1; }
   .geo-table-header .tbl-title { margin: 0 0 6px 0 !important; font-size: 16px !important; line-height: 1.3 !important; font-weight: 700 !important; color: #344054 !important; text-transform: uppercase !important; letter-spacing: 0.12em !important; }
   .geo-table-header .tbl-subtitle { font-size: 13px !important; font-weight: 400 !important; color: #98a2b3 !important; line-height: 1.5 !important; }
   .geo-title-accent { display: block !important; width: 52px !important; height: 4px !important; margin-top: 19px !important; border-radius: 999px !important; background: #ffb000 !important; }

    /* Tools */
    .geo-table-tools { display: flex !important; align-items: center !important; gap: 14px !important; }

    /* Search */
    .geo-search-box { position: relative !important; width: 330px !important; }
    .geo-search-icon { position: absolute !important; left: 18px !important; top: 50% !important; transform: translateY(-50%) !important; color: #536b92 !important; z-index: 2; }
    .geo-search-input { width: 100% !important; height: 54px !important; padding: 0 18px 0 52px !important; background: #ffffff !important; border: 1px solid #d5deea !important; border-radius: 11px !important; color: #25395d !important; font-size: 15px !important; font-weight: 500 !important; outline: none !important; box-shadow: none !important; }
    .geo-search-input::placeholder { color: #667a9a !important; }
    .geo-search-input:focus { border-color: #7994c2 !important; box-shadow: 0 0 0 3px rgba(73, 110, 173, 0.09) !important; }  
   
    .geo-table-tools .btn-export-csv {
    height: 54px !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    padding: 0 22px !important;

    border: 1px solid #d5deea !important;
    border-radius: 11px !important;

    background: #ffffff !important;
    color: #11264b !important;

    font-size: 14px !important;
    font-weight: 700 !important;

    box-shadow: none !important;
}

    .geo-table-tools .btn-export-csv:hover {
    background: #f8fafc !important;
    color: #11264b !important;
}

    .geo-table-content {
    padding: 0 25px !important;
}

   /* RESPONSIVE TABLE */
    .geo-table-responsive {
    width: 100% !important;

    overflow-x: auto !important;
    overflow-y: hidden !important;

    padding: 0 !important;
}

    /* TABLE */
    .geo-table {
    width: 100% !important;

    min-width: 1220px !important;

    table-layout: fixed !important;

    border-collapse: separate !important;

    /* JARAK ANTAR KOLOM */
    border-spacing: 15px 0 !important;

    color: #152441 !important;

    font-size: 14px !important;
}
    
    /* TABLE HEADER */

    .geo-table th:nth-child(1),
.geo-table td:nth-child(1) {
    width: 255px !important;
}

.geo-table th:nth-child(2),
.geo-table td:nth-child(2) {
    width: 140px !important;
}

.geo-table th:nth-child(3),
.geo-table td:nth-child(3) {
    width: 145px !important;
}

.geo-table th:nth-child(4),
.geo-table td:nth-child(4) {
    width: 128px !important;
}

.geo-table th:nth-child(5),
.geo-table td:nth-child(5) {
    width: 128px !important;
}

.geo-table th:nth-child(6),
.geo-table td:nth-child(6) {
    width: 145px !important;
}

.geo-table th:nth-child(7),
.geo-table td:nth-child(7) {
    width: 170px !important;
}

    .geo-table thead th {
    height: 60px !important;

    padding: 0 16px !important;

    vertical-align: middle !important;
    text-align: center !important;

    border: none !important;

    color: #ffffff !important;

    font-size: 13px !important;
    font-weight: 800 !important;

    text-transform: uppercase !important;

    letter-spacing: 0 !important;

    text-shadow:
        0 1px 2px rgba(0, 0, 0, 0.14) !important;

    overflow: hidden !important;
}   
    
    /* KECAMATAN */
.geo-table tbody td:nth-child(1) {
    background:
        linear-gradient(
            90deg,
            #fbfcfe 0%,
            #f7f9fc 100%
        ) !important;

    border-color: #d9e1eb !important;

    text-align: left !important;
}


/* LUAS */
.geo-table tbody td:nth-child(2) {
    background:
        linear-gradient(
            90deg,
            #fdfaff 0%,
            #fbf7ff 100%
        ) !important;

    border-color: #eadcf7 !important;
}


/* KELURAHAN */
.geo-table tbody td:nth-child(3) {
    background:
        linear-gradient(
            90deg,
            #fdfaff 0%,
            #faf6ff 100%
        ) !important;

    border-color: #e9dcf7 !important;
}


/* RW */
.geo-table tbody td:nth-child(4) {
    background:
        linear-gradient(
            90deg,
            #f9fbff 0%,
            #f4f7ff 100%
        ) !important;

    border-color: #d9e3ff !important;
}


/* RT */
.geo-table tbody td:nth-child(5) {
    background:
        linear-gradient(
            90deg,
            #f9fcff 0%,
            #f3f9ff 100%
        ) !important;

    border-color: #d9ebfc !important;
}


/* POPULASI */
.geo-table tbody td:nth-child(6) {
    background:
        linear-gradient(
            90deg,
            #f9fdff 0%,
            #f2fbfd 100%
        ) !important;

    border-color: #d7eef4 !important;
}


/* KEPADATAN */
.geo-table tbody td:nth-child(7) {
    background:
        linear-gradient(
            90deg,
            #f9fefd 0%,
            #f1fbfa 100%
        ) !important;

    border-color: #d3eeeb !important;
}

/* ============================================================
   BOTTOM CORNERS PER COLUMN
============================================================ */

.geo-table tbody tr:last-child td:nth-child(1),
.geo-table tbody tr:last-child td:nth-child(2),
.geo-table tbody tr:last-child td:nth-child(3),
.geo-table tbody tr:last-child td:nth-child(4),
.geo-table tbody tr:last-child td:nth-child(5),
.geo-table tbody tr:last-child td:nth-child(6),
.geo-table tbody tr:last-child td:nth-child(7) {
    border-radius: 0 0 17px 17px !important;
}

/* ============================================================
   HOVER
============================================================ */

.geo-table tbody tr:hover td:nth-child(1) {
    background: #f1f5fa !important;
}

.geo-table tbody tr:hover td:nth-child(2),
.geo-table tbody tr:hover td:nth-child(3) {
    background: #f7f0ff !important;
}

.geo-table tbody tr:hover td:nth-child(4) {
    background: #edf2ff !important;
}

.geo-table tbody tr:hover td:nth-child(5) {
    background: #edf7ff !important;
}

.geo-table tbody tr:hover td:nth-child(6) {
    background: #ebf9fc !important;
}

.geo-table tbody tr:hover td:nth-child(7) {
    background: #eaf9f7 !important;
}

/* ============================================================
   CELL KECAMATAN
============================================================ */

.kecamatan-cell {
    display: flex !important;

    align-items: center !important;

    gap: 16px !important;
}

.geo-rank {
    width: 33px !important;
    height: 33px !important;

    min-width: 33px !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    border: 1px solid #d7e0ec !important;

    border-radius: 50% !important;

    background: #ffffff !important;

    color: #172d54 !important;

    font-size: 13px !important;
    font-weight: 700 !important;

    box-shadow:
        0 1px 4px rgba(15, 23, 42, 0.04) !important;
}

.kecamatan-name {
    color: #10234b !important;

    font-size: 14px !important;

    font-weight: 700 !important;

    white-space: nowrap !important;
}


/* HILANGKAN DOT LAMA */
.geo-dot,
.kecamatan-icon {
    display: none !important;
}

    /* ============================================================
   VALUE
============================================================ */

.number-value,
.population-value {
    color: #172238 !important;

    font-size: 14px !important;

    font-weight: 500 !important;
}


    /* BADGE */
    .geo-badge {
    display: inline !important;

    min-width: auto !important;

    padding: 0 !important;

    border-radius: 0 !important;

    background: transparent !important;

    color: #172238 !important;

    font-size: 14px !important;

    font-weight: 500 !important;
}
    
    /* ============================================================
   PAGINATION
============================================================ */

.geo-pagination {
    display: flex !important;

    align-items: center !important;

    justify-content: space-between !important;

    gap: 20px !important;

    padding: 16px 30px 18px !important;

    margin: 0 !important;

    border-top: 0 !important;
}

#pager-info {
    color: #627597 !important;

    font-size: 13px !important;
}

.geo-pager {
    display: flex !important;

    align-items: center !important;

    gap: 10px !important;
}

.geo-pager button {
    width: 44px !important;

    height: 44px !important;

    min-width: 44px !important;

    padding: 0 !important;

    display: flex !important;

    align-items: center !important;
    justify-content: center !important;

    border: 1px solid #d7e0ec !important;

    border-radius: 9px !important;

    background: #ffffff !important;

    color: #17335e !important;

    font-size: 16px !important;

    font-weight: 600 !important;

    box-shadow: none !important;
}

.geo-pager button:hover:not(:disabled):not(.active) {
    background: #f5f8fc !important;
}

.geo-pager button.active {
    background:
        linear-gradient(
            180deg,
            #3970f4 0%,
            #2759e8 100%
        ) !important;

    border-color: #2d61eb !important;

    color: #ffffff !important;

    font-weight: 700 !important;

    box-shadow:
        0 5px 14px rgba(47, 99, 234, .22) !important;
}

/* ============================================================
   SOURCE
============================================================ */

.geo-source-footer {
    min-height: 60px !important;

    display: flex !important;

    align-items: center !important;

    gap: 10px !important;

    padding: 13px 30px !important;

    border-top: 1px solid #e9eef5 !important;

    background: #ffffff !important;

    color: #667895 !important;

    font-size: 12px !important;
}

.geo-source-icon {
    width: 31px !important;

    height: 31px !important;

    flex-shrink: 0 !important;

    display: inline-flex !important;

    align-items: center !important;

    justify-content: center !important;

    border-radius: 50% !important;

    background: #edf4ff !important;

    color: #326af0 !important;
}

.geo-source-footer strong {
    color: #354b70 !important;

    font-weight: 700 !important;
}


    /* Tombol export CSV */
    .btn-export-csv {
        display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;
        border: 1px solid #1e8e3e; background: #eaf6ec; color: #1e8e3e;
        font-size: 13px; font-weight: 600; padding: 6px 12px; border-radius: 6px;
        cursor: pointer; transition: background .15s, color .15s;
    }
    .btn-export-csv:hover { background: #1e8e3e; color: #fff; }

    /* ANIMASI CARD TETAP DIPAKAI */
@keyframes cardValueIn {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}

.stat-summary-card .card-anim {
    animation: cardValueIn .35s ease both;
}

    /* ============================================================
   RESPONSIVE - TABLET & MOBILE
============================================================ */

/* ============================================================
   TABLET BESAR
============================================================ */
@media (max-width: 1100px) {

    /* Header tabel jadi bertumpuk */
    .geo-table-header {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 20px !important;
    }

    .geo-table-heading {
        width: 100% !important;
    }

    .geo-table-tools {
        width: 100% !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
    }

    .geo-search-box {
        flex: 1 !important;
        width: auto !important;
    }

    /* Kolom tabel tetap lebar,
       sehingga nanti bisa horizontal scroll */
    .geo-table {
        min-width: 1180px !important;
    }
}


/* ============================================================
   TABLET
============================================================ */
@media (max-width: 992px) {

    /* Bagian chart/map lama tetap dipertahankan */
    .geo-mid-grid {
        grid-template-columns: 1fr !important;
    }

    .geo-highlight-grid {
        grid-template-columns: 1fr !important;
    }

    /* Header tabel */
    .geo-table-top {
        padding: 24px 22px 14px !important;
    }

    .geo-table-header .tbl-title {
        font-size: 26px !important;
    }

    .geo-table-header .tbl-subtitle {
        font-size: 14px !important;
    }

    .geo-title-accent {
        margin-top: 16px !important;
    }

    /* Tabel tetap mempertahankan card column */
    .geo-table-content {
        padding: 0 12px !important;
    }

    .geo-table {
        min-width: 1160px !important;
        border-spacing: 12px 0 !important;
    }

    .geo-pagination {
        padding-left: 22px !important;
        padding-right: 22px !important;
    }

    .geo-source-footer {
        padding-left: 22px !important;
        padding-right: 22px !important;
    }
}


/* ============================================================
   MOBILE
============================================================ */
@media (max-width: 768px) {

    /* ========================================================
       WRAPPER / SIDEBAR LAMA
    ======================================================== */

    .statistik-wrapper {
        flex-direction: column !important;
        padding: 20px 0 !important;
        gap: 16px !important;
    }

    .statistik-sidebar {
        width: 100% !important;
    }

    .statistik-sidebar .nav {
        flex-direction: row !important;
        flex-wrap: nowrap !important;

        overflow-x: auto !important;

        gap: 6px !important;

        padding-bottom: 4px !important;

        -webkit-overflow-scrolling: touch;
    }

    .statistik-sidebar .nav-link {
        white-space: nowrap !important;
        margin-bottom: 0 !important;
    }

    .stat-header {
        font-size: 15px !important;
        padding: 12px !important;
    }


    /* ========================================================
       MAP
    ======================================================== */

    #geo-map {
        height: 360px !important;
    }


    /* ========================================================
       CARD TABLE UTAMA
    ======================================================== */

    .geo-table-wrap {
        border-radius: 14px !important;

        /* jangan scroll wrapper utama */
        overflow: hidden !important;
    }


    /* ========================================================
       HEADER TABLE
    ======================================================== */

    .geo-table-top {
        padding: 20px 18px 14px !important;
    }

    .geo-table-header {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 18px !important;
    }

    .geo-table-header .tbl-title {
        font-size: 23px !important;
        line-height: 1.2 !important;
    }

    .geo-table-header .tbl-subtitle {
        font-size: 13px !important;
    }

    .geo-title-accent {
        width: 46px !important;
        height: 4px !important;
        margin-top: 14px !important;
    }


    /* ========================================================
       SEARCH + CSV
    ======================================================== */

    .geo-table-tools {
        width: 100% !important;

        flex-direction: column !important;

        align-items: stretch !important;

        gap: 10px !important;
    }

    .geo-search-box {
        width: 100% !important;
    }

    .geo-search-input {
        width: 100% !important;
        height: 48px !important;

        font-size: 14px !important;
    }

    .geo-table-tools .btn-export-csv {
        width: 100% !important;
        height: 48px !important;
    }


    /* ========================================================
       TABLE SCROLL
    ======================================================== */

    .geo-table-content {
        padding: 0 4px !important;
    }

    .geo-table-responsive {
        width: 100% !important;

        overflow-x: auto !important;
        overflow-y: hidden !important;

        -webkit-overflow-scrolling: touch;

        scrollbar-width: thin;
    }

    /*
       tetap lebar agar bentuk 7 kolom tidak rusak.
       Pengguna tinggal scroll horizontal.
    */
    .geo-table {
        min-width: 1120px !important;

        border-spacing: 10px 0 !important;
    }


    /* ========================================================
       TABLE HEADER MOBILE
    ======================================================== */

    .geo-table thead th {
        height: 55px !important;

        padding: 0 13px !important;

        font-size: 12px !important;
    }


    /* ========================================================
       TABLE BODY MOBILE
    ======================================================== */

    .geo-table tbody td {
        height: 54px !important;

        padding: 0 14px !important;

        font-size: 13px !important;
    }

    .geo-rank {
        width: 30px !important;
        height: 30px !important;
        min-width: 30px !important;

        font-size: 12px !important;
    }

    .kecamatan-cell {
        gap: 12px !important;
    }

    .kecamatan-name {
        font-size: 13px !important;
    }

    .number-value,
    .population-value,
    .geo-badge {
        font-size: 13px !important;
    }


    /* ========================================================
       PAGINATION
    ======================================================== */

    .geo-pagination {
        padding: 15px 18px 18px !important;

        flex-direction: column !important;

        align-items: flex-start !important;

        gap: 12px !important;
    }

    #pager-info {
        font-size: 12px !important;
    }

    .geo-pager {
        align-self: flex-end !important;
    }

    .geo-pager button {
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;

        font-size: 14px !important;
    }


    /* ========================================================
       SOURCE
    ======================================================== */

    .geo-source-footer {
        padding: 12px 18px !important;

        min-height: 56px !important;

        font-size: 11px !important;
    }

    .geo-source-icon {
        width: 28px !important;
        height: 28px !important;
    }


    /* ========================================================
       LEGEND PETA
       bagian lama tetap dipertahankan
    ======================================================== */

    .kec-legend {
        font-size: 8.5px !important;

        line-height: 1.25 !important;

        padding: 4px 6px !important;

        max-width: 44vw !important;

        max-height: 150px !important;

        overflow-y: auto !important;

        opacity: 0.92 !important;
    }

    .kec-legend .legend-kec-item {
        padding: 1px 3px !important;

        gap: 3px !important;
    }

    .kec-legend .legend-kec-item span:last-child {
        white-space: normal !important;
    }
}


/* ============================================================
   MOBILE KECIL
============================================================ */
@media (max-width: 480px) {

    .geo-table-top {
        padding: 18px 14px 12px !important;
    }

    .geo-table-header .tbl-title {
        font-size: 21px !important;
    }

    .geo-table-header .tbl-subtitle {
        font-size: 12px !important;
    }

    .geo-search-input {
        height: 46px !important;
    }

    .geo-table-tools .btn-export-csv {
        height: 46px !important;
    }

    .geo-table {
        min-width: 1080px !important;

        border-spacing: 8px 0 !important;
    }

    .geo-pagination {
        padding-left: 14px !important;
        padding-right: 14px !important;
    }

    .geo-source-footer {
        padding-left: 14px !important;
        padding-right: 14px !important;
    }
}

/* ============================================================
   FORCE COLOR HEADER TABLE GEOGRAFIS
   WAJIB TARUH PALING BAWAH CSS
============================================================ */

#geo-table.geo-table thead tr th {
    color: #ffffff !important;
    border: none !important;
    text-align: center !important;
    vertical-align: middle !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    height: 60px !important;
    padding: 0 16px !important;
    background-clip: padding-box !important;
}


/* 1. KECAMATAN */
#geo-table.geo-table thead tr th:nth-child(1) {
    background-color: #5f7898 !important;
    background-image: linear-gradient(
        135deg,
        #7d95b1 0%,
        #536d8d 100%
    ) !important;

    border-radius: 16px 16px 0 0 !important;
}


/* 2. LUAS */
#geo-table.geo-table thead tr th:nth-child(2) {
    background-color: #a66fd8 !important;
    background-image: linear-gradient(
        135deg,
        #c48be8 0%,
        #9866d6 100%
    ) !important;

    border-radius: 16px 16px 0 0 !important;
}


/* 3. KELURAHAN */
#geo-table.geo-table thead tr th:nth-child(3) {
    background-color: #a977db !important;
    background-image: linear-gradient(
        135deg,
        #c191e8 0%,
        #9b6ed8 100%
    ) !important;

    border-radius: 16px 16px 0 0 !important;
}


/* 4. RW */
#geo-table.geo-table thead tr th:nth-child(4) {
    background-color: #6689ef !important;
    background-image: linear-gradient(
        135deg,
        #84a6fa 0%,
        #5579ed 100%
    ) !important;

    border-radius: 16px 16px 0 0 !important;
}


/* 5. RT */
#geo-table.geo-table thead tr th:nth-child(5) {
    background-color: #75afe9 !important;
    background-image: linear-gradient(
        135deg,
        #90c8fa 0%,
        #68a6e6 100%
    ) !important;

    border-radius: 16px 16px 0 0 !important;
}


/* 6. POPULASI */
#geo-table.geo-table thead tr th:nth-child(6) {
    background-color: #59bfd7 !important;
    background-image: linear-gradient(
        135deg,
        #75d1e6 0%,
        #4fb6ce 100%
    ) !important;

    border-radius: 16px 16px 0 0 !important;
}


/* 7. KEPADATAN */
#geo-table.geo-table thead tr th:nth-child(7) {
    background-color: #46bdb4 !important;
    background-image: linear-gradient(
        135deg,
        #66d2cb 0%,
        #39b5ac 100%
    ) !important;

    border-radius: 16px 16px 0 0 !important;
}

#geo-table thead,
#geo-table thead tr {
    background: transparent !important;
}

/* ============================================================
   WARNA BODY KOLOM - DIPERTEGAS
============================================================ */

#geo-table tbody td:nth-child(1) {
    background: #f6f8fb !important;
    border-color: #dce3ec !important;
}

#geo-table tbody td:nth-child(2) {
    background: #fbf7ff !important;
    border-color: #eadcf8 !important;
}

#geo-table tbody td:nth-child(3) {
    background: #faf6ff !important;
    border-color: #e8daf7 !important;
}

#geo-table tbody td:nth-child(4) {
    background: #f3f7ff !important;
    border-color: #d8e3ff !important;
}

#geo-table tbody td:nth-child(5) {
    background: #f3f9ff !important;
    border-color: #d7eafa !important;
}

#geo-table tbody td:nth-child(6) {
    background: #f1fbfd !important;
    border-color: #d5edf3 !important;
}

#geo-table tbody td:nth-child(7) {
    background: #f1fbfa !important;
    border-color: #d2edea !important;
}

#geo-table.geo-table {
    border-collapse: separate !important;
    border-spacing: 16px 0 !important;
}

/* ============================================================
   ALIGNMENT ISI DATA TABEL
   Kecamatan tetap kiri, kolom lainnya center
============================================================ */

/* Kecamatan tetap rata kiri */
#geo-table tbody td:nth-child(1) {
    text-align: left !important;
}

/* Luas, Kelurahan, RW, RT, Populasi, Kepadatan */
#geo-table tbody td:nth-child(2),
#geo-table tbody td:nth-child(3),
#geo-table tbody td:nth-child(4),
#geo-table tbody td:nth-child(5),
#geo-table tbody td:nth-child(6),
#geo-table tbody td:nth-child(7) {
    text-align: center !important;
    vertical-align: middle !important;
}

/* Pastikan elemen nilai di dalam cell ikut center */
#geo-table tbody td:nth-child(2) .number-value,
#geo-table tbody td:nth-child(3) .geo-badge,
#geo-table tbody td:nth-child(6) .population-value,
#geo-table tbody td:nth-child(7) .number-value {
    display: inline-block !important;
    width: 100% !important;
    text-align: center !important;
}

#geo-table tbody td {
    line-height: 1 !important;
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

        {{-- =========================================================
    TABLE GEOGRAFIS RINCI
========================================================= --}}
<div class="geo-table-wrap">

    {{-- Header --}}
    <div class="geo-table-top">

        <div class="geo-table-header">

            <div class="geo-table-heading">

                <div class="tbl-title">
                    {{ __('geografis.table_title') }}
                </div>

                <div class="tbl-subtitle">
                    Data wilayah kecamatan di Jakarta Barat
                </div>

                <div class="geo-title-accent"></div>

            </div>


            {{-- Tools --}}
            <div class="geo-table-tools">

                {{-- Search --}}
                <div class="geo-search-box">

                    <span class="geo-search-icon">

                        <svg
                            width="21"
                            height="21"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >

                            <circle
                                cx="11"
                                cy="11"
                                r="7"
                                stroke="currentColor"
                                stroke-width="2"
                            />

                            <path
                                d="M20 20L16.5 16.5"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />

                        </svg>

                    </span>

                    <input
                        class="geo-search-input"
                        type="text"
                        id="geo-search"
                        placeholder="{{ __('geografis.table_search') }}"
                        oninput="filterTable()"
                    >

                </div>


                {{-- CSV --}}
                @include('statistik.partials.unduh-tabel', [
                    'target' => '#geo-table',
                    'nama'   => __('geografis.table_file', ['tahun' => $tahun]),
                ])

            </div>

        </div>

    </div>


    {{-- Table Content --}}
    <div class="geo-table-content">

        <div class="geo-table-responsive">

            <table
                class="geo-table"
                id="geo-table"
                data-unduh-angka="{{ app()->getLocale() }}"
            >

                <thead>

                    <tr>

                        <th>
                            {{ __('geografis.col_kecamatan') }}
                        </th>

                        <th>
                            {{ __('geografis.col_luas') }}
                        </th>

                        <th>
                            {{ __('geografis.col_kelurahan') }}
                        </th>

                        <th>
                            {{ __('geografis.col_rw') }}
                        </th>

                        <th>
                            {{ __('geografis.col_rt') }}
                        </th>

                        <th>
                            {{ __('geografis.col_populasi') }}
                        </th>

                        <th>
                            {{ __('geografis.col_kepadatan') }}
                        </th>

                    </tr>

                </thead>


                <tbody id="geo-table-body">

                    @foreach($luas->sortByDesc('luas_km2') as $row)

                        @continue($row->kecamatan === null)

                        @php
                            $s = $kecStats[
                                strtoupper(
                                    $row->kecamatan->nama_kecamatan
                                )
                            ] ?? null;
                        @endphp


                        <tr
                            data-name="{{ strtolower($row->kecamatan->nama_kecamatan) }}"
                        >

                            {{-- Kecamatan --}}
                            <td>

                                <div class="kecamatan-cell">

                                    <span class="geo-rank">
                                        {{ $loop->iteration }}
                                    </span>

                                    <span class="kecamatan-name">
                                        {{ $row->kecamatan->nama_kecamatan }}
                                    </span>

                                </div>

                            </td>


                            {{-- Luas --}}
                            <td>

                                <span class="number-value">
                                    {{ nf($row->luas_km2, 2) }}
                                </span>

                            </td>


                            {{-- Kelurahan --}}
                            <td>

                                @if($s && $s['kelurahan'])

                                    <span class="geo-badge">
                                        {{ $s['kelurahan'] }}
                                    </span>

                                @else
                                    —
                                @endif

                            </td>


                            {{-- RW --}}
                            <td>

                                {{ $s && $s['rw']
                                    ? nf($s['rw'], 0)
                                    : '—'
                                }}

                            </td>


                            {{-- RT --}}
                            <td>

                                {{ $s && $s['rt']
                                    ? nf($s['rt'], 0)
                                    : '—'
                                }}

                            </td>


                            {{-- Populasi --}}
                            <td>

                                <span class="population-value">

                                    {{ $s && $s['penduduk']
                                        ? nf($s['penduduk'], 0)
                                        : '—'
                                    }}

                                </span>

                            </td>


                            {{-- Kepadatan --}}
                            <td>

                                <span class="number-value">

                                    {{ $s && $s['kepadatan']
                                        ? nf($s['kepadatan'], 0)
                                        : '—'
                                    }}

                                </span>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>


    {{-- Pagination --}}
    <div class="geo-pagination">

        <div id="pager-info"></div>

        <div
            class="geo-pager"
            id="geo-pager"
        ></div>

    </div>


        {{-- Source --}}
    <div class="geo-source-footer">
        <span class="geo-source-icon">
            <svg
                width="17"
                height="17"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M12 3L19 6V11C19 15.5 16.1 19.1 12 21C7.9 19.1 5 15.5 5 11V6L12 3Z"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linejoin="round"
                />
                <path
                    d="M9.5 11.5L11.2 13.2L14.8 9.5"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        </span>

        <span>
            <strong>Sumber:</strong>
            {{ $geo->sumber }}
        </span>
    </div>

</div> {{-- END geo-table-wrap --}}

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

// ── Table Pagination & Search ─────────────────────────────────

var PAGE_SIZE = 4;
var currentPage = 1;
var filteredRows = [];

function getAllRows() {
    return Array.from(
        document.querySelectorAll('#geo-table-body tr')
    );
}

function filterTable() {
    var searchInput = document.getElementById('geo-search');

    var q = searchInput
        ? searchInput.value.toLowerCase().trim()
        : '';

    searchActive = q.length > 0;

    if (searchActive) {
        filteredRows = getAllRows().filter(function(row) {
            return (row.dataset.name || '').includes(q);
        });
    } else {
        filteredRows = [];
    }

    currentPage = 1;
    renderTable();
}

function renderTable() {
    var allRows = getAllRows();

    var rows = searchActive
        ? filteredRows
        : allRows;

    var total = rows.length;

    var pages = Math.max(
        1,
        Math.ceil(total / PAGE_SIZE)
    );

    if (currentPage > pages) {
        currentPage = pages;
    }

    var start = (currentPage - 1) * PAGE_SIZE;
    var end = Math.min(start + PAGE_SIZE, total);

    allRows.forEach(function(row) {
        row.style.display = 'none';
    });

    rows.slice(start, end).forEach(function(row) {
        row.style.display = '';
    });

    var info = document.getElementById('pager-info');

    if (info) {
        if (total === 0) {
            info.textContent = 'Tidak ada kecamatan yang ditemukan';
        } else {
            info.textContent =
                'Menampilkan ' +
                (start + 1) +
                '–' +
                end +
                ' dari ' +
                total +
                ' kecamatan';
        }
    }

    var pager = document.getElementById('geo-pager');

    if (!pager) return;

    pager.innerHTML = '';

    var prev = document.createElement('button');
    prev.innerHTML = '&#8249;';
    prev.setAttribute('aria-label', 'Halaman sebelumnya');
    prev.disabled = currentPage === 1 || total === 0;

    prev.onclick = function() {
        if (currentPage > 1) {
            currentPage--;
            renderTable();
        }
    };

    pager.appendChild(prev);

    if (total > 0) {
        for (var p = 1; p <= pages; p++) {
            (function(pg) {
                var btn = document.createElement('button');

                btn.textContent = pg;

                if (pg === currentPage) {
                    btn.classList.add('active');
                }

                btn.onclick = function() {
                    currentPage = pg;
                    renderTable();
                };

                pager.appendChild(btn);
            })(p);
        }
    }

    var next = document.createElement('button');

    next.innerHTML = '&#8250;';
    next.setAttribute('aria-label', 'Halaman berikutnya');

    next.disabled =
        currentPage === pages ||
        total === 0;

    next.onclick = function() {
        if (currentPage < pages) {
            currentPage++;
            renderTable();
        }
    };

    pager.appendChild(next);
}

document.addEventListener('DOMContentLoaded', function() {
    renderTable();
});

</script>

@endpush