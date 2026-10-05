@extends('landing-page.layout.app')
@section('page_title', __('ngobrol.kategori.infografis') . ' - Jakarta Barat')

@push('styles')
<style>
    .statistik-wrapper { display: flex; gap: 24px; padding: 40px 0; }
    .statistik-content { flex: 1; min-width: 0; }
    .stat-header {
        background: #ffbf00; color: #fff; text-align: center; padding: 14px;
        border-radius: 8px; font-weight: 700; font-size: 18px; margin-bottom: 20px;
    }
    .chart-card { background: #fff; border: 1px solid #eee; border-radius: 18px; padding: 22px; box-shadow: 0 10px 40px rgba(76, 78, 100, .05); }
    .ng-grid { display: grid; gap: 20px; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); }
    .ng-info {
        position: relative; border-radius: 12px; overflow: hidden; cursor: pointer;
        border: 1px solid #eee; background: #f5f5f5; outline: none;
    }
    .ng-info img { width: 100%; aspect-ratio: 3 / 4; object-fit: cover; object-position: top; display: block; transition: transform .35s; }
    .ng-info-overlay {
        position: absolute; inset: 0; padding: 20px;
        display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 18px;
        background: rgba(20, 20, 20, .45); backdrop-filter: blur(1.5px);
        opacity: 0; transition: opacity .25s;
    }
    .ng-info:hover .ng-info-overlay, .ng-info:focus-within .ng-info-overlay { opacity: 1; }
    .ng-info:hover img, .ng-info:focus-within img { transform: scale(1.04); }
    .ng-info:focus-visible { box-shadow: 0 0 0 3px #ffbf00; }
    .ng-info-judul { color: #fff; font-size: 17px; font-weight: 600; text-align: center; line-height: 1.35; text-shadow: 0 1px 4px rgba(0, 0, 0, .4); }
    .ng-info-aksi { display: flex; gap: 16px; }
    .ng-bulat {
        width: 56px; height: 56px; border-radius: 50%; border: 0; background: #fff;
        display: flex; align-items: center; justify-content: center; font-size: 24px;
        text-decoration: none; box-shadow: 0 4px 14px rgba(0, 0, 0, .18);
        transform: translateY(8px); transition: transform .25s, box-shadow .2s;
    }
    .ng-info:hover .ng-bulat, .ng-info:focus-within .ng-bulat { transform: translateY(0); }
    .ng-bulat:hover { box-shadow: 0 6px 20px rgba(0, 0, 0, .28); }
    .ng-bulat.ig { color: #e1306c; }
    .ng-bulat.zoom { color: #26a69a; }
    .ng-modal .modal-content { border: 0; border-radius: 16px; }
    .ng-modal-img { width: 100%; border-radius: 10px; display: block; }
    .ng-modal-desc { max-width: 100%; white-space: pre-line; overflow-wrap: anywhere; }
    .ng-kosong { text-align: center; color: #999; padding: 56px 16px; }
    .ng-kosong i { display: block; margin-bottom: 12px; color: #ddd; font-size: 40px; }
    @media (max-width: 992px) { .statistik-wrapper { flex-direction: column; } }
    @media (max-width: 768px) {
        .stat-header { font-size: 15px; padding: 12px; }
        .ng-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="statistik-wrapper">
        @include('statistik.partials.sidebar')

        <main class="statistik-content">
            <h1 class="stat-header">{{ __('ngobrol.kategori.infografis') }}</h1>

            @if ($infografis->isEmpty())
                <div class="chart-card ng-kosong">
                    <i class="fa fa-chart-pie"></i>
                    {{ __('ngobrol.kosong_jenis', ['jenis' => \Illuminate\Support\Str::lower(__('ngobrol.kategori.infografis'))]) }}
                </div>
            @else
                <div class="chart-card">
                    <div class="ng-grid">
                        @foreach ($infografis as $item)
                            <div class="ng-info" tabindex="0">
                                <img src="{{ $item->gambarUrl() }}" alt="{{ $item->judul }}" loading="lazy">
                                <div class="ng-info-overlay">
                                    <div class="ng-info-judul">{{ $item->judul }}</div>
                                    <div class="ng-info-aksi">
                                        @if ($item->instagram_url)
                                            <a class="ng-bulat ig" href="{{ $item->instagram_url }}" target="_blank" rel="noopener noreferrer"
                                               aria-label="{{ __('ngobrol.instagram') }}: {{ $item->judul }}" title="{{ __('ngobrol.instagram') }}">
                                                <i class="fab fa-instagram"></i>
                                            </a>
                                        @endif
                                        <button type="button" class="ng-bulat zoom" data-bs-toggle="modal" data-bs-target="#infografisModal"
                                                data-gambar="{{ $item->gambarUrl() }}"
                                                data-judul="{{ $item->judul }}"
                                                data-desc="{{ $item->deskripsi }}"
                                                data-instagram="{{ $item->instagram_url }}"
                                                aria-label="{{ __('ngobrol.perbesar') }}: {{ $item->judul }}" title="{{ __('ngobrol.perbesar') }}">
                                            <i class="fa fa-magnifying-glass-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </main>
    </div>
</div>

<div class="modal fade ng-modal" id="infografisModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h2 class="modal-title fs-5 fw-bold" data-ng="judul"></h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('ngobrol.tutup') }}"></button>
            </div>
            <div class="modal-body">
                <img class="ng-modal-img" data-ng="gambar" alt="">
                <p class="ng-modal-desc mt-3 mb-0 text-muted small" data-ng="desc"></p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <a class="btn btn-sm btn-outline-danger" data-ng="instagram" target="_blank" rel="noopener noreferrer" hidden>
                    <i class="fab fa-instagram"></i> {{ __('ngobrol.instagram') }}
                </a>
                <a class="btn btn-sm btn-outline-warning" data-ng="link" target="_blank" rel="noopener">
                    <i class="fa fa-up-right-from-square"></i> {{ __('ngobrol.lihat_penuh') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('infografisModal')?.addEventListener('show.bs.modal', function (event) {
    const data = event.relatedTarget.dataset;
    this.querySelector('[data-ng="judul"]').textContent = data.judul;
    const image = this.querySelector('[data-ng="gambar"]');
    image.src = data.gambar;
    image.alt = data.judul;
    this.querySelector('[data-ng="link"]').href = data.gambar;

    const instagram = this.querySelector('[data-ng="instagram"]');
    instagram.hidden = !data.instagram;
    if (data.instagram) instagram.href = data.instagram;

    const description = this.querySelector('[data-ng="desc"]');
    description.textContent = data.desc || '';
    description.hidden = !data.desc;
});
</script>
@endpush
