@extends('landing-page.layout.app')
@section('page_title', __('ngobrol.page_title') . ($jenis ? ' - ' . __('ngobrol.kategori.' . $jenis) : '') . ' - Jakarta Barat')

@php
    $video = $seksi['video']['isi'] ?? collect();
    $utama = $video->first();
    $adaKonten = collect($seksi)->sum('total') > 0;
@endphp

@push('styles')
<style>
    .statistik-wrapper { display: flex; gap: 24px; padding: 40px 0; }
    .statistik-content { flex: 1; min-width: 0; }

    /* Header — disamakan dengan modul lain, tanpa dropdown tahun karena
       kontennya kurasi, bukan data per periode. */
    .stat-header-wrap { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .stat-header {
        flex: 1; background: #ffbf00; color: white; text-align: center;
        padding: 14px; border-radius: 8px; font-weight: 700;
        font-size: 18px; margin-bottom: 0; letter-spacing: 1px;
    }

    .chart-card { background: #fff; border: 1px solid #eee; border-radius: 18px; padding: 22px; box-shadow: 0 10px 40px rgba(76, 78, 100, 0.05); margin-bottom: 24px; }

    /* Tombol pindah halaman (Semua / Video / Infografis / Materi) — gaya sama
       dengan tab blok di Potensi Kelurahan. */
    .ng-nav { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 20px; }
    .ng-nav a {
        padding: 7px 14px; border: 1px solid #ddd; border-radius: 6px;
        background: #fff; color: #555; font-weight: 600; font-size: 13px;
        text-decoration: none; transition: all .2s; white-space: nowrap;
    }
    .ng-nav a i { margin-right: 4px; }
    .ng-nav a:hover  { border-color: #ffbf00; color: #b8860b; background: #fff8e1; }
    .ng-nav a.active { background: #ffbf00; border-color: #ffbf00; color: #fff; }

    .ng-seksi-head { display: flex; align-items: center; gap: 12px; margin-bottom: 18px; }
    .ng-seksi-head .ng-lihat-semua {
        margin-left: auto; flex-shrink: 0; font-size: 12px; font-weight: 700;
        color: #b8860b; text-decoration: none; white-space: nowrap;
    }
    .ng-seksi-head .ng-lihat-semua:hover { color: #8a6500; }
    .ng-seksi-ikon {
        width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
        background: #fff8e1; color: #b8860b; display: flex; align-items: center; justify-content: center; font-size: 18px;
    }
    .ng-seksi-judul { font-size: 16px; font-weight: 700; color: #333; margin: 0; letter-spacing: .3px; }
    .ng-seksi-desc  { font-size: 12px; color: #999; margin: 0; }

    /* ── Video: satu besar + daftar di samping ─────────────────────── */
    .ng-video { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 20px; }
    .ng-video.tunggal { grid-template-columns: minmax(0, 1fr); }

    .ng-player { position: relative; aspect-ratio: 16 / 9; background: #000; border-radius: 12px; overflow: hidden; }
    .ng-player iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; }
    .ng-thumb {
        position: absolute; inset: 0; width: 100%; height: 100%;
        border: 0; padding: 0; background: none; cursor: pointer; display: block;
    }
    .ng-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .ng-play {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        width: 72px; height: 50px; border-radius: 14px; background: rgba(0,0,0,.7);
        color: #fff; display: flex; align-items: center; justify-content: center;
        font-size: 22px; transition: background .2s;
    }
    .ng-thumb:hover .ng-play, .ng-thumb:focus-visible .ng-play { background: #ff0000; }

    .ng-main-info { padding-top: 14px; }
    .ng-main-judul { font-size: 18px; font-weight: 700; color: #333; margin: 0 0 6px; line-height: 1.4; }
    .ng-main-desc  { font-size: 13px; color: #666; line-height: 1.7; margin: 0 0 8px; white-space: pre-line; }
    .ng-yt { font-size: 12px; font-weight: 600; color: #b8860b; text-decoration: none; }
    .ng-yt:hover { color: #ff0000; }

    /* Tinggi daftar mengikuti kolom video besar (isi absolut di dalam
       pembungkus relatif), sisanya bisa digulir. */
    .ng-playlist { position: relative; min-height: 240px; }
    .ng-playlist-inner {
        position: absolute; inset: 0; display: flex; flex-direction: column;
        border: 1px solid #eee; border-radius: 12px; overflow: hidden;
    }
    .ng-playlist-head {
        padding: 10px 14px; font-size: 12px; font-weight: 700; color: #555;
        background: #fafafa; border-bottom: 1px solid #eee; letter-spacing: .3px;
    }
    .ng-playlist-list { overflow-y: auto; flex: 1; padding: 6px; }
    .ng-pl-item {
        display: flex; gap: 10px; width: 100%; text-align: left;
        padding: 8px; border: 0; border-radius: 8px; background: none; cursor: pointer;
        transition: background .15s;
    }
    .ng-pl-item:hover  { background: #f5f5f5; }
    .ng-pl-item.active { background: #fff8e1; box-shadow: inset 3px 0 0 #ffbf00; }
    .ng-pl-thumb { position: relative; width: 128px; flex-shrink: 0; aspect-ratio: 16 / 9; border-radius: 6px; overflow: hidden; background: #000; }
    .ng-pl-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .ng-pl-item.active .ng-pl-thumb::after {
        content: "\f04b"; font-family: "Font Awesome 6 Free"; font-weight: 900;
        position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
        background: rgba(0,0,0,.45); color: #fff; font-size: 14px;
    }
    .ng-pl-teks { min-width: 0; }
    .ng-pl-judul {
        font-size: 13px; font-weight: 700; color: #333; line-height: 1.35; margin-bottom: 4px;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .ng-pl-desc {
        font-size: 11.5px; color: #888; line-height: 1.45;
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
    }

    /* ── Kartu infografis & materi ──────────────────────────────── */
    .ng-grid { display: grid; gap: 20px; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); }
    .ng-card {
        border: 1px solid #eee; border-radius: 12px; overflow: hidden; background: #fff;
        display: flex; flex-direction: column; text-align: left; padding: 0; width: 100%;
        transition: box-shadow .2s, transform .2s; cursor: pointer;
    }
    .ng-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,.08); transform: translateY(-2px); }
    .ng-card-img { width: 100%; display: block; object-fit: cover; background: #f5f5f5; }
    .ng-card-img.lanskap { aspect-ratio: 16 / 9; }

    /* Kartu infografis: gambar saja, judul & tombol muncul sebagai overlay
       saat disorot (atau difokus — lihat catatan tabindex di markup). */
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
    .ng-info-judul {
        color: #fff; font-size: 17px; font-weight: 600; text-align: center; line-height: 1.35;
        text-shadow: 0 1px 4px rgba(0,0,0,.4);
    }
    .ng-info-aksi { display: flex; gap: 16px; }
    .ng-bulat {
        width: 56px; height: 56px; border-radius: 50%; border: 0; background: #fff;
        display: flex; align-items: center; justify-content: center; font-size: 24px;
        text-decoration: none; box-shadow: 0 4px 14px rgba(0,0,0,.18);
        transform: translateY(8px); transition: transform .25s, box-shadow .2s;
    }
    .ng-info:hover .ng-bulat, .ng-info:focus-within .ng-bulat { transform: translateY(0); }
    .ng-bulat:hover { box-shadow: 0 6px 20px rgba(0,0,0,.28); }
    .ng-bulat.ig   { color: #e1306c; }
    .ng-bulat.zoom { color: #26a69a; }

    .ng-card-placeholder {
        aspect-ratio: 16 / 9; display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, #fff8e1, #ffe8a3); color: #d4a017; font-size: 34px;
    }
    .ng-card-body { padding: 14px 16px 16px; display: flex; flex-direction: column; gap: 6px; flex: 1; }
    .ng-card-judul { font-size: 14px; font-weight: 700; color: #333; line-height: 1.4; margin: 0; }
    .ng-card-desc {
        font-size: 12.5px; color: #777; line-height: 1.6; margin: 0;
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
    }
    .ng-card-more { margin-top: auto; padding-top: 4px; font-size: 12px; font-weight: 700; color: #b8860b; }

    /* ── Modal ─────────────────────────────────────────────────────── */
    .ng-modal .modal-content { border: 0; border-radius: 16px; }
    .ng-modal-img { width: 100%; border-radius: 10px; display: block; }
    .ng-artikel p { font-size: 14.5px; color: #444; line-height: 1.85; margin-bottom: 1em; }
    .ng-artikel-cover { width: 100%; max-height: 320px; object-fit: cover; border-radius: 10px; margin-bottom: 18px; }

    .ng-kosong { text-align: center; color: #999; padding: 56px 16px; }
    .ng-kosong i { font-size: 40px; color: #ddd; margin-bottom: 12px; display: block; }

    @media (max-width: 992px) {
        .statistik-wrapper { flex-direction: column; }
        .ng-video { grid-template-columns: minmax(0, 1fr); }
        .ng-playlist { min-height: 0; }
        .ng-playlist-inner { position: static; max-height: 420px; }
    }
    @media (max-width: 768px) {
        .stat-header { font-size: 15px; padding: 12px; }
        .ng-pl-thumb { width: 112px; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="statistik-wrapper">

        @include('statistik.partials.sidebar')

        {{-- KONTEN --}}
        <div class="statistik-content">

            <div class="stat-header-wrap">
                <div class="stat-header">{{ __('ngobrol.header') }}</div>
            </div>
            {{-- Pindah halaman: utama (semua jenis) atau satu jenis saja. --}}
            <nav class="ng-nav">
                <a href="{{ route('statistik.ngobrol-statistik') }}" class="{{ $jenis === null ? 'active' : '' }}">
                    <i class="fa fa-table-cells-large"></i>{{ __('ngobrol.semua') }}
                </a>
                @foreach (\App\Models\NgobrolStatistik::KATEGORI as $slug => $label)
                    <a href="{{ route('statistik.ngobrol-statistik', ['jenis' => $slug]) }}" class="{{ $jenis === $slug ? 'active' : '' }}">
                        <i class="fa {{ \App\Models\NgobrolStatistik::IKON[$slug] }}"></i>{{ __('ngobrol.kategori.' . $slug) }}
                    </a>
                @endforeach
            </nav>

            @if (!$adaKonten)
                <div class="chart-card ng-kosong">
                    <i class="fa {{ $jenis ? \App\Models\NgobrolStatistik::IKON[$jenis] : 'fa-comments' }}"></i>
                    {{ $jenis
                        ? __('ngobrol.kosong_jenis', ['jenis' => \Illuminate\Support\Str::lower(__('ngobrol.kategori.' . $jenis))])
                        : __('ngobrol.kosong') }}
                </div>
            @else
                @foreach ($seksi as $slug => $s)
                    {{-- Di halaman utama, jenis yang belum punya konten dilewati. --}}
                    @continue($s['total'] === 0)

                    <section class="chart-card" id="{{ $slug }}">
                        @include('statistik.partials.ngobrol-seksi-head', [
                            'slug'       => $slug,
                            'lihatSemua' => $jenis === null,
                        ])

                @if ($slug === 'video')
                        <div class="ng-video {{ $video->count() > 1 ? '' : 'tunggal' }}">
                            <div>
                                <div class="ng-player" id="ngPlayer">
                                    {{-- Iframe baru dipasang saat diklik supaya halaman
                                         tidak memuat pemutar YouTube sebelum dibutuhkan. --}}
                                    <button type="button" class="ng-thumb"
                                            data-embed="{{ $utama->embedUrl() }}"
                                            data-judul="{{ $utama->judul }}"
                                            aria-label="{{ __('ngobrol.putar') }}: {{ $utama->judul }}">
                                        <img src="{{ $utama->thumbnail() }}" alt="">
                                        <span class="ng-play"><i class="fa fa-play"></i></span>
                                    </button>
                                </div>
                                <div class="ng-main-info">
                                    <h3 class="ng-main-judul" id="ngJudul">{{ $utama->judul }}</h3>
                                    <p class="ng-main-desc" id="ngDesc" @if (!$utama->deskripsi) hidden @endif>{{ $utama->deskripsi }}</p>
                                    <a class="ng-yt" id="ngLink" href="{{ $utama->watchUrl() }}" target="_blank" rel="noopener noreferrer">
                                        <i class="fab fa-youtube"></i> {{ __('ngobrol.buka') }}
                                    </a>
                                </div>
                            </div>

                            @if ($video->count() > 1)
                                <div class="ng-playlist">
                                    <div class="ng-playlist-inner">
                                        <div class="ng-playlist-head">
                                            <i class="fa fa-list"></i> {{ __('ngobrol.daftar') }}
                                        </div>
                                        <div class="ng-playlist-list">
                                            @foreach ($video as $v)
                                                <button type="button" class="ng-pl-item {{ $loop->first ? 'active' : '' }}"
                                                        data-embed="{{ $v->embedUrl() }}"
                                                        data-judul="{{ $v->judul }}"
                                                        data-desc="{{ $v->deskripsi }}"
                                                        data-watch="{{ $v->watchUrl() }}">
                                                    <span class="ng-pl-thumb"><img src="{{ $v->thumbnail() }}" alt="" loading="lazy"></span>
                                                    <span class="ng-pl-teks">
                                                        <span class="ng-pl-judul">{{ $v->judul }}</span>
                                                        @if ($v->deskripsi)
                                                            <span class="ng-pl-desc">{{ $v->deskripsi }}</span>
                                                        @endif
                                                    </span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                @elseif ($slug === 'infografis')
                        <div class="ng-grid">
                            @foreach ($s['isi'] as $i)
                                {{-- tabindex: di layar sentuh tidak ada hover, jadi
                                     ketukan pertama memfokuskan kartu dan memunculkan
                                     overlay; ketukan kedua mengenai tombolnya. --}}
                                <div class="ng-info" tabindex="0">
                                    <img src="{{ $i->gambarUrl() }}" alt="{{ $i->judul }}" loading="lazy">
                                    <div class="ng-info-overlay">
                                        <div class="ng-info-judul">{{ $i->judul }}</div>
                                        <div class="ng-info-aksi">
                                            @if ($i->instagram_url)
                                                <a class="ng-bulat ig" href="{{ $i->instagram_url }}" target="_blank" rel="noopener noreferrer"
                                                   aria-label="{{ __('ngobrol.instagram') }}: {{ $i->judul }}" title="{{ __('ngobrol.instagram') }}">
                                                    <i class="fab fa-instagram"></i>
                                                </a>
                                            @endif
                                            <button type="button" class="ng-bulat zoom" data-bs-toggle="modal" data-bs-target="#ngInfoModal"
                                                    data-gambar="{{ $i->gambarUrl() }}"
                                                    data-judul="{{ $i->judul }}"
                                                    data-desc="{{ $i->deskripsi }}"
                                                    data-instagram="{{ $i->instagram_url }}"
                                                    aria-label="{{ __('ngobrol.perbesar') }}: {{ $i->judul }}" title="{{ __('ngobrol.perbesar') }}">
                                                <i class="fa fa-magnifying-glass-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                @else {{-- materi --}}
                        <div class="ng-grid">
                            @foreach ($s['isi'] as $k)
                                <button type="button" class="ng-card" data-bs-toggle="modal" data-bs-target="#ngMateriModal"
                                        data-isi="ng-isi-{{ $k->id }}">
                                    @if ($k->gambarUrl())
                                        <img class="ng-card-img lanskap" src="{{ $k->gambarUrl() }}" alt="" loading="lazy">
                                    @else
                                        <span class="ng-card-placeholder"><i class="fa fa-book-open"></i></span>
                                    @endif
                                    <span class="ng-card-body">
                                        <span class="ng-card-judul">{{ $k->judul }}</span>
                                        <span class="ng-card-desc">{{ $k->deskripsi ?: \Illuminate\Support\Str::limit($k->isi, 160) }}</span>
                                        <span class="ng-card-more">{{ __('ngobrol.baca') }} <i class="fa fa-arrow-right"></i></span>
                                    </span>
                                </button>

                                {{-- Isi artikel disimpan di template supaya modal bersama
                                     cukup menyalinnya; teks sudah di-escape di sini. --}}
                                <template id="ng-isi-{{ $k->id }}">
                                    @if ($k->gambarUrl())
                                        <img class="ng-artikel-cover" src="{{ $k->gambarUrl() }}" alt="">
                                    @endif
                                    <h4 class="fw-bold mb-3">{{ $k->judul }}</h4>
                                    @foreach (preg_split('/\R\s*\R/', trim((string) $k->isi)) as $paragraf)
                                        <p>{!! nl2br(e(trim($paragraf))) !!}</p>
                                    @endforeach
                                </template>
                            @endforeach
                        </div>
                @endif
                    </section>
                @endforeach
            @endif

        </div>
    </div>
</div>

{{-- Modal infografis (dipakai bersama semua kartu) --}}
<div class="modal fade ng-modal" id="ngInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" data-ng="judul"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('ngobrol.tutup') }}"></button>
            </div>
            <div class="modal-body">
                <img class="ng-modal-img" data-ng="gambar" alt="">
                <p class="mt-3 mb-0 text-muted small" style="white-space:pre-line" data-ng="desc"></p>
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

{{-- Modal artikel materi --}}
<div class="modal fade ng-modal" id="ngMateriModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <span class="badge text-bg-warning text-white"><i class="fa fa-book-open"></i> {{ __('ngobrol.kategori.materi') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('ngobrol.tutup') }}"></button>
            </div>
            <div class="modal-body ng-artikel px-4 pb-4" data-ng="isi"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const player = document.getElementById('ngPlayer');

    function putar(embed, judul) {
        const frame = document.createElement('iframe');
        frame.src = embed;
        frame.title = judul;
        frame.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
        frame.referrerPolicy = 'strict-origin-when-cross-origin';
        frame.allowFullscreen = true;
        player.replaceChildren(frame);
    }

    document.addEventListener('click', function (e) {
        // Thumbnail video besar → putar.
        const thumb = e.target.closest('.ng-thumb');
        if (thumb) { putar(thumb.dataset.embed, thumb.dataset.judul); return; }

        // Video di daftar → pindahkan ke pemutar besar dan langsung putar.
        const item = e.target.closest('.ng-pl-item');
        if (!item) return;

        document.querySelectorAll('.ng-pl-item.active').forEach(el => el.classList.remove('active'));
        item.classList.add('active');

        putar(item.dataset.embed, item.dataset.judul);
        document.getElementById('ngJudul').textContent = item.dataset.judul;
        const desc = document.getElementById('ngDesc');
        desc.textContent = item.dataset.desc;
        desc.hidden = !item.dataset.desc;
        document.getElementById('ngLink').href = item.dataset.watch;

        // Di layar sempit daftar ada di bawah pemutar; gulir balik ke atas.
        if (window.matchMedia('(max-width: 992px)').matches) {
            player.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    // Isi modal infografis dari kartu yang diklik.
    document.getElementById('ngInfoModal')?.addEventListener('show.bs.modal', function (e) {
        const d = e.relatedTarget.dataset;
        this.querySelector('[data-ng="judul"]').textContent = d.judul;
        this.querySelector('[data-ng="gambar"]').src = d.gambar;
        this.querySelector('[data-ng="gambar"]').alt = d.judul;
        this.querySelector('[data-ng="link"]').href = d.gambar;
        const ig = this.querySelector('[data-ng="instagram"]');
        ig.hidden = !d.instagram;
        if (d.instagram) ig.href = d.instagram;
        const desc = this.querySelector('[data-ng="desc"]');
        desc.textContent = d.desc || '';
        desc.hidden = !d.desc;
    });

    // Isi modal materi dari template artikelnya.
    document.getElementById('ngMateriModal')?.addEventListener('show.bs.modal', function (e) {
        const tpl = document.getElementById(e.relatedTarget.dataset.isi);
        this.querySelector('[data-ng="isi"]').replaceChildren(tpl.content.cloneNode(true));
    });
})();
</script>
@endpush
