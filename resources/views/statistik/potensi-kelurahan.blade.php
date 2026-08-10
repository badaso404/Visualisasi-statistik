@extends('landing-page.layout.app')
@section('page_title', __('podes.page_title') . ' - Jakarta Barat')

@push('styles')
<style>
    .statistik-wrapper { display: flex; gap: 24px; padding: 40px 0; }
    .statistik-content { flex: 1; min-width: 0; }

    /* Header — disamakan dengan modul lain, hanya tanpa dropdown tahun karena
       periode datanya ditentukan sumber (Podes), bukan oleh kita. */
    .stat-header-wrap { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
    .stat-header {
        flex: 1; background: #ffbf00; color: white; text-align: center;
        padding: 14px; border-radius: 8px; font-weight: 700;
        font-size: 18px; margin-bottom: 0; letter-spacing: 1px;
    }

    /* Gaya pemilih bahasa ikut pindah ke partial-nya sendiri. */

    .chart-card { background: #fff; border: 1px solid #eee; border-radius: 18px; padding: 22px; box-shadow: 0 10px 40px rgba(76, 78, 100, 0.05); }
    .chart-card .chart-title { font-size: 14px; font-weight: 700; color: #333; margin-bottom: 6px; letter-spacing: .5px; }
    .chart-hint { font-size: 12px; color: #999; margin-bottom: 16px; }
    .sumber { text-align: right; font-size: 12px; color: #999; margin-top: 14px; }

    /* Baris tombol blok — gaya sama dengan tab peta di modul kebencanaan. */
    .podes-toolbar {
        display: flex; align-items: flex-start; gap: 12px;
        margin-bottom: 16px; flex-wrap: wrap;
    }
    .podes-tabs { display: flex; gap: 6px; flex-wrap: wrap; flex: 1; min-width: 0; }
    .podes-tab {
        padding: 6px 12px; border: 1px solid #ddd; border-radius: 6px;
        background: #fff; color: #555; font-weight: 600; font-size: 12px;
        text-decoration: none; transition: all .2s; white-space: nowrap;
    }
    .podes-tab:hover  { border-color: #ffbf00; color: #b8860b; background: #fff8e1; }
    .podes-tab.active { background: #ffbf00; border-color: #ffbf00; color: #fff; }

    .podes-open {
        display: inline-flex; align-items: center; gap: 8px; flex-shrink: 0;
        padding: 7px 14px; border: 2px solid #ffbf00; border-radius: 6px;
        background: #fff; color: #b8860b; font-weight: 700; font-size: 13px;
        text-decoration: none; transition: all .15s; white-space: nowrap;
    }
    .podes-open:hover { background: #ffbf00; color: #fff; }

    /* Tinggi frame mengikuti layar supaya halaman kita tidak jadi sangat
       panjang; dashboard sumber tetap punya gulirannya sendiri di dalam. */
    .podes-frame-wrap {
        border: 1px solid #eee; border-radius: 12px; overflow: hidden; background: #f9f9f9;
    }
    .podes-frame { display: block; width: 100%; height: calc(100vh - 120px); min-height: 760px; border: 0; }

    .podes-note {
        display: flex; align-items: flex-start; gap: 10px;
        margin-top: 14px; padding: 10px 14px;
        background: #fff8e1; border: 1px solid #ffe28a; border-radius: 8px;
        font-size: 12px; color: #8a6d1a; line-height: 1.6;
    }
    .podes-note i { margin-top: 2px; }

    @media (max-width: 992px) {
        .statistik-wrapper { flex-direction: column; }
    }
    @media (max-width: 768px) {
        .stat-header  { font-size: 15px; padding: 12px; }
        .podes-frame  { height: 70vh; min-height: 520px; }
        .podes-open   { width: 100%; justify-content: center; }
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
                <div class="stat-header">{{ __('podes.header') }}</div>
            </div>

            <div class="chart-card">
                <div class="chart-title">{{ __('podes.blok_label') }}</div>
                <div class="chart-hint">{{ __('podes.desc') }}</div>

                <div class="podes-toolbar">
                    <div class="podes-tabs">
                        @foreach ($bloks as $slug)
                            <a class="podes-tab {{ $slug === $blok ? 'active' : '' }}"
                               href="{{ route('statistik.potensi-kelurahan', ['blok' => $slug]) }}">
                                {{ __('podes.blok.' . $slug) }}
                            </a>
                        @endforeach
                    </div>

                    {{-- Selalu tampil, bukan hanya saat frame gagal: penolakan
                         penyematan lintas-domain tidak terdeteksi dari sini. --}}
                    <a class="podes-open" href="{{ $embedUrl }}" target="_blank" rel="noopener noreferrer">
                        <i class="fa fa-arrow-up-right-from-square"></i> {{ __('podes.open') }}
                    </a>
                </div>

                <div class="podes-frame-wrap">
                    <iframe
                        class="podes-frame"
                        src="{{ $embedUrl }}"
                        title="{{ __('podes.header') }}"
                        loading="lazy"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen></iframe>
                </div>

                <div class="podes-note">
                    <i class="fa fa-circle-info"></i>
                    <div>{!! __('podes.fallback') !!}</div>
                </div>

                <div class="sumber">{{ __('podes.source') }}</div>
            </div>

        </div>
    </div>
</div>
@endsection
