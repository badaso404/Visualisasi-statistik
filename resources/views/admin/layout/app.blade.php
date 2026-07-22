<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin') &middot; Statistik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background:#f5f6fa; }
        .sidebar { width:240px; min-height:100vh; background:#1e293b; }
        .sidebar a { color:#cbd5e1; text-decoration:none; padding:.6rem 1rem; display:block; border-radius:.4rem; }
        .sidebar a:hover, .sidebar a.active { background:#334155; color:#fff; }
        .sidebar .brand { color:#fff; font-weight:600; }
        .content { flex:1; }
    </style>
</head>
<body>
<div class="d-flex">
    <nav class="sidebar p-3">
        <div class="brand mb-4 fs-5"><i class="bi bi-bar-chart-fill"></i> Admin Statistik</div>
        @php $r = request()->routeIs(...); @endphp
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="{{ route('admin.kecamatan.index') }}" class="{{ request()->routeIs('admin.kecamatan.*') ? 'active' : '' }}"><i class="bi bi-geo-alt"></i> Kecamatan</a>
        <a href="{{ route('admin.geografis.index') }}" class="{{ request()->routeIs('admin.geografis.*') ? 'active' : '' }}"><i class="bi bi-globe-asia-australia"></i> Geografis</a>
        <a href="{{ route('admin.iklim.index') }}" class="{{ request()->routeIs('admin.iklim.*') ? 'active' : '' }}"><i class="bi bi-cloud-sun"></i> Iklim</a>
        <a href="{{ route('admin.kependudukan.index') }}" class="{{ request()->routeIs('admin.kependudukan.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Kependudukan</a>
        <a href="{{ route('admin.pendidikan.index') }}" class="{{ request()->routeIs('admin.pendidikan.*') ? 'active' : '' }}"><i class="bi bi-mortarboard"></i> Pendidikan</a>
        <a href="{{ route('admin.kesehatan.index') }}" class="{{ request()->routeIs('admin.kesehatan.*') ? 'active' : '' }}"><i class="bi bi-heart-pulse"></i> Kesehatan</a>
        <a href="{{ route('admin.bencana.index') }}" class="{{ request()->routeIs('admin.bencana.*') || request()->routeIs('admin.titik-bencana.*') ? 'active' : '' }}"><i class="bi bi-exclamation-triangle"></i> Kebencanaan</a>
        <a href="{{ route('admin.kemiskinan.index') }}" class="{{ request()->routeIs('admin.kemiskinan.*') ? 'active' : '' }}"><i class="bi bi-hand-thumbs-down"></i> Kemiskinan</a>
        <a href="{{ route('admin.perekonomian.index') }}" class="{{ request()->routeIs('admin.perekonomian.*') || request()->routeIs('admin.pdrb-sektor.*') ? 'active' : '' }}"><i class="bi bi-graph-up-arrow"></i> Perekonomian</a>
        <a href="{{ route('admin.infrastruktur-digital.index') }}" class="{{ request()->routeIs('admin.infrastruktur-digital.*') || request()->routeIs('admin.jak-wifi.*') || request()->routeIs('admin.cctv.*') ? 'active' : '' }}"><i class="bi bi-wifi"></i> Infrastruktur Digital</a>
    </nav>

    <div class="content">
        <header class="bg-white border-bottom px-4 py-2 d-flex justify-content-between align-items-center">
            <span class="fw-semibold">@yield('title', 'Panel Admin')</span>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ url('/') }}" target="_blank" class="text-decoration-none small"><i class="bi bi-box-arrow-up-right"></i> Lihat situs</a>
                <span class="text-muted small">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Keluar</button>
                </form>
            </div>
        </header>

        <main class="p-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-warning alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Pemicu modal form: dipakai bersama oleh semua halaman admin.
// Tombol Tambah  : data-modal-form="#id" data-action="{url store}"
// Tombol Edit    : data-modal-form="#id" data-action="{url update}" data-method="PUT"
//                  data-fields='{"tahun":2024,"jumlah_penduduk":1000}'
document.addEventListener('click', function (e) {
    const trigger = e.target.closest('[data-modal-form]');
    if (!trigger) return;

    const modalEl = document.querySelector(trigger.dataset.modalForm);
    if (!modalEl) return;

    const form = modalEl.querySelector('form');
    form.reset();
    form.action = trigger.dataset.action;

    // Laravel butuh _method=PUT saat edit; dihapus lagi saat tambah.
    let spoof = form.querySelector('input[name="_method"]');
    if (trigger.dataset.method) {
        if (!spoof) {
            spoof = document.createElement('input');
            spoof.type = 'hidden';
            spoof.name = '_method';
            form.appendChild(spoof);
        }
        spoof.value = trigger.dataset.method;
    } else if (spoof) {
        spoof.remove();
    }

    const fields = JSON.parse(trigger.dataset.fields || '{}');
    form.querySelectorAll('[name]').forEach(function (input) {
        if (input.name === '_token' || input.name === '_method') return;
        if (input.name === '_form_action') { input.value = form.action; return; }
        if (input.name === '_form_method') { input.value = trigger.dataset.method || ''; return; }
        const value = fields[input.name];
        input.value = value === null || value === undefined ? '' : value;
    });

    const title = modalEl.querySelector('[data-modal-title]');
    if (title && trigger.dataset.title) title.textContent = trigger.dataset.title;

    bootstrap.Modal.getOrCreateInstance(modalEl).show();
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
});

// Konfirmasi hapus. Label diambil dari atribut, bukan ditulis langsung ke dalam
// confirm('...'), supaya nama berisi tanda kutip (mis. "Jl. Anggrek 'Blok A'")
// tidak merusak skripnya.
document.addEventListener('submit', function (e) {
    const form = e.target.closest('[data-konfirmasi-hapus]');
    if (!form) return;

    const label = form.dataset.konfirmasiHapus;
    if (!window.confirm('Hapus ' + label + '?\n\nTindakan ini tidak bisa dibatalkan.')) {
        e.preventDefault();
    }
});

// Validasi gagal -> Laravel redirect balik; buka lagi modal yang bersangkutan.
@if ($errors->any())
    document.addEventListener('DOMContentLoaded', function () {
        const target = document.querySelector('[data-modal-autoopen]');
        if (target) bootstrap.Modal.getOrCreateInstance(target).show();
    });
@endif
</script>
@stack('scripts')
</body>
</html>
