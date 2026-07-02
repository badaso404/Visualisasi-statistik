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
        <a href="{{ route('admin.bencana.index') }}" class="{{ request()->routeIs('admin.bencana.*') ? 'active' : '' }}"><i class="bi bi-exclamation-triangle"></i> Kebencanaan</a>
        <a href="{{ route('admin.titik-bencana.index') }}" class="{{ request()->routeIs('admin.titik-bencana.*') ? 'active' : '' }}"><i class="bi bi-geo-fill"></i> Titik Peta Bencana</a>
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
</body>
</html>
