{{--
    Ditampilkan saat modul statistik belum punya data untuk ditampilkan.

    Sebelumnya kondisi ini membuat halaman error 500 karena view langsung
    membaca properti dari record yang ternyata null.

    Variabel: $modul (nama modul), $tahun (opsional), $availableTahun (opsional)
--}}
@extends('landing-page.layout.app')

@section('content')
<div class="container">
    <div class="statistik-wrapper" style="display:flex; gap:24px; padding:40px 0;">
        @include('statistik.partials.sidebar')

        <div class="flex-grow-1">
            <h4 class="mb-4">{{ $modul }}</h4>

            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fa fa-database fa-2x text-muted mb-3"></i>
                    <h6 class="mb-2">Data belum tersedia</h6>
                    <p class="text-muted mb-0">
                        @if (!empty($availableTahun) && count((array) $availableTahun))
                            Belum ada data {{ strtolower($modul) }} untuk tahun {{ $tahun ?? '' }}.
                            Coba pilih tahun lain:
                            <span class="d-inline-flex flex-wrap gap-1 ms-1">
                                @foreach ((array) $availableTahun as $t)
                                    <a href="{{ request()->url() }}?tahun={{ $t }}" class="badge bg-secondary text-decoration-none">{{ $t }}</a>
                                @endforeach
                            </span>
                        @else
                            Data {{ strtolower($modul) }} belum diisi. Silakan hubungi pengelola.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
