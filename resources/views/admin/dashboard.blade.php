@extends('admin.layout.app')
@section('title', 'Dashboard')

@section('content')
<div class="row g-3">
    @php
        $cards = [
            ['kecamatan',    'Kecamatan',    'bi-geo-alt',                'admin.kecamatan.index',    'primary'],
            ['geografis',    'Data Geografis','bi-globe-asia-australia',  'admin.geografis.index',    'success'],
            ['iklim',        'Data Iklim',   'bi-cloud-sun',              'admin.iklim.index',        'info'],
            ['kependudukan', 'Kependudukan', 'bi-people',                 'admin.kependudukan.index', 'warning'],
            ['pendidikan',   'Pendidikan',   'bi-mortarboard',            'admin.pendidikan.index',   'danger'],
            ['kesehatan',    'Kesehatan',    'bi-heart-pulse',            'admin.kesehatan.index',    'secondary'],
            ['bencana',      'Kebencanaan','bi-exclamation-triangle', 'admin.bencana.index',      'danger'],
            ['kemiskinan',   'Kemiskinan',   'bi-hand-thumbs-down',       'admin.kemiskinan.index',   'warning'],
            ['perekonomian', 'Perekonomian', 'bi-graph-up-arrow',         'admin.perekonomian.index', 'success'],
            ['infrastruktur digital', 'Infrastruktur Digital', 'bi-wifi', 'admin.infrastruktur-digital.index', 'info'],
        ];
    @endphp
    @foreach ($cards as [$key, $label, $icon, $route, $color])
        <div class="col-md-4">
            <a href="{{ route($route) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="rounded-circle bg-{{ $color }} bg-opacity-10 text-{{ $color }} d-flex align-items-center justify-content-center me-3" style="width:54px;height:54px;">
                            <i class="bi {{ $icon }} fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">{{ $label }}</div>
                            <div class="fs-4 fw-semibold">{{ $stats[$key] }} <span class="fs-6 fw-normal text-muted">data</span></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>
@endsection
