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
    .ng-main-desc  { font-size: 13px; color: #666; line-height: 1.7; margin: 0 0 8px; white-space: pre-line; overflow-wrap: anywhere; }
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
    .ng-pl-item.active:not(.ng-materi-item) .ng-pl-thumb::after {
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
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; overflow-wrap: anywhere;
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
    .ng-artikel p { font-size: 14.5px; color: #444; line-height: 1.85; margin-bottom: 1em; overflow-wrap: anywhere; }
    .ng-artikel-cover { width: 100%; max-height: 320px; object-fit: cover; border-radius: 10px; margin-bottom: 18px; }
    .ng-pdf-frame { display: block; width: 100%; height: 70vh; min-height: 420px; border: 1px solid #ddd; border-radius: 8px; }

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
                    @continue($slug === 'infografis')
                    <a href="{{ route('statistik.ngobrol-statistik', ['jenis' => $slug]) }}" class="{{ $jenis === $slug ? 'active' : '' }}">
                        <i class="fa {{ \App\Models\NgobrolStatistik::IKON[$slug] }}"></i>{{ __('ngobrol.kategori.' . $slug) }}
                    </a>
                @endforeach
            </nav>

            @if (!$adaKonten)
                @php
                    $ikonKosong = $jenis && isset(\App\Models\NgobrolStatistik::IKON[$jenis]) 
                                  ? \App\Models\NgobrolStatistik::IKON[$jenis] 
                                  : 'fa-comments';
                @endphp
                <div class="chart-card ng-kosong">
                    <i class="fa {{ $ikonKosong }}"></i>
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

                @else {{-- materi --}}
                        @php
                            $materi = $s['isi'];
                            $utama_m = $materi->first();
                        @endphp
                        <div class="ng-video {{ $materi->count() > 1 ? '' : 'tunggal' }}">
                            <div>
                                <div class="ng-materi-hero ng-artikel" id="ngMateriHero">
                                    @if ($utama_m)
                                        @if ($utama_m->gambarUrl())
                                            <img class="ng-artikel-cover" src="{{ $utama_m->gambarUrl() }}" alt="">
                                        @endif
                                        <div style="word-wrap: break-word;">
                                            <h4 class="fw-bold mb-3" style="word-break: break-word;">{{ $utama_m->judul }}</h4>
                                            @foreach (preg_split('/\R\s*\R/', trim((string) $utama_m->isi)) as $paragraf)
                                                <p style="word-break: break-word;">{!! nl2br(e(trim($paragraf))) !!}</p>
                                            @endforeach
                                            @if ($utama_m->presentasiUrl())
                                                @php($ekstensi = strtolower(pathinfo($utama_m->presentasi, PATHINFO_EXTENSION)))
                                                @php($urlFull = url($utama_m->presentasiUrl()))
                                                @php($isPdf = $ekstensi === 'pdf')
                                                @php($embedUrl = $isPdf ? $urlFull : 'https://view.officeapps.live.com/op/embed.aspx?src=' . urlencode($urlFull))
                                                
                                                <button type="button" class="btn btn-warning align-self-start mt-3" data-bs-toggle="modal" data-bs-target="#ngDocModal" data-src="{{ $embedUrl }}" data-title="{{ $utama_m->judul }}">
                                                    <i class="fa fa-eye"></i> Lihat Materi
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if ($materi->count() > 1)
                                <div class="ng-playlist">
                                    <div class="ng-playlist-inner">
                                        <div class="ng-playlist-head">
                                            <i class="fa fa-list"></i> Daftar Materi
                                        </div>
                                        <div class="ng-playlist-list">
                                            @foreach ($materi as $k)
                                                <button type="button" class="ng-pl-item ng-materi-item {{ $loop->first ? 'active' : '' }}"
                                                        data-id="{{ $k->id }}">
                                                    <span class="ng-pl-thumb">
                                                        @if ($k->gambarUrl())
                                                            <img src="{{ $k->gambarUrl() }}" alt="" loading="lazy">
                                                        @else
                                                            <span class="ng-card-placeholder" style="width:100%; height:100%; font-size:20px;">
                                                                <i class="fa fa-book-open"></i>
                                                            </span>
                                                        @endif
                                                    </span>
                                                    <span class="ng-pl-teks">
                                                        <span class="ng-pl-judul">{{ $k->judul }}</span>
                                                        <span class="ng-pl-desc" style="word-break: break-word;">{{ $k->deskripsi ?: \Illuminate\Support\Str::limit($k->isi, 80) }}</span>
                                                    </span>
                                                </button>

                                                <template id="ng-materi-template-{{ $k->id }}">
                                                    @if ($k->gambarUrl())
                                                        <img class="ng-artikel-cover" src="{{ $k->gambarUrl() }}" alt="">
                                                    @endif
                                                    <div style="word-wrap: break-word;">
                                                        <h4 class="fw-bold mb-3" style="word-break: break-word;">{{ $k->judul }}</h4>
                                                        @foreach (preg_split('/\R\s*\R/', trim((string) $k->isi)) as $paragraf)
                                                            <p style="word-break: break-word;">{!! nl2br(e(trim($paragraf))) !!}</p>
                                                        @endforeach
                                                        @if ($k->presentasiUrl())
                                                            @php($ekstensi = strtolower(pathinfo($k->presentasi, PATHINFO_EXTENSION)))
                                                            @php($urlFull = url($k->presentasiUrl()))
                                                            @php($isPdf = $ekstensi === 'pdf')
                                                            @php($embedUrl = $isPdf ? $urlFull : 'https://view.officeapps.live.com/op/embed.aspx?src=' . urlencode($urlFull))
                                                            
                                                            <button type="button" class="btn btn-warning align-self-start mt-3" data-bs-toggle="modal" data-bs-target="#ngDocModal" data-src="{{ $embedUrl }}" data-title="{{ $k->judul }}">
                                                                <i class="fa fa-eye"></i> Lihat Materi
                                                            </button>
                                                        @endif
                                                    </div>
                                                </template>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                @endif
                    </section>
                @endforeach
            @endif

        </div>
    </div>
</div>

{{-- Modal untuk Viewer Dokumen --}}
<div class="modal fade ng-modal" id="ngDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <span class="badge text-bg-warning text-white" id="ngDocModalTitle"><i class="fa fa-book-open"></i> Isi Materi</span>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-0 pb-4 px-4 pt-3">
                <iframe id="ngDocIframe" src="" style="width: 100%; height: 85vh; border: 1px solid #ddd; border-radius: 8px;"></iframe>
            </div>
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

    // Ganti hero section untuk materi ketika item daftar diklik.
    document.addEventListener('click', function (e) {
        const itemMateri = e.target.closest('.ng-materi-item');
        if (!itemMateri) return;

        document.querySelectorAll('.ng-materi-item.active').forEach(el => el.classList.remove('active'));
        itemMateri.classList.add('active');

        const hero = document.getElementById('ngMateriHero');
        const tpl = document.getElementById('ng-materi-template-' + itemMateri.dataset.id);
        if (hero && tpl) {
            hero.replaceChildren(tpl.content.cloneNode(true));
            
            if (window.matchMedia('(max-width: 992px)').matches) {
                hero.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });

    const docModalEl = document.getElementById('ngDocModal');
    if (docModalEl) {
        docModalEl.addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            document.getElementById('ngDocModalTitle').innerHTML = '<i class="fa fa-book-open"></i> ' + btn.dataset.title;
            document.getElementById('ngDocIframe').src = btn.dataset.src;
        });
        docModalEl.addEventListener('hidden.bs.modal', function () {
            document.getElementById('ngDocIframe').src = '';
        });
    }
})();
</script>
@endpush
