{{-- Judul seksi di halaman Ngobrol Statistik (video / infografis / materi).
     $lihatSemua = true di halaman utama: tautan ke halaman jenis tersebut. --}}
<div class="ng-seksi-head">
    <div class="ng-seksi-ikon"><i class="fa {{ \App\Models\NgobrolStatistik::IKON[$slug] }}"></i></div>
    <div>
        <h2 class="ng-seksi-judul">{{ __('ngobrol.kategori.' . $slug) }}</h2>
        <p class="ng-seksi-desc">{{ __('ngobrol.desc.' . $slug) }}</p>
    </div>
    @if ($lihatSemua ?? false)
        <a class="ng-lihat-semua" href="{{ route('statistik.ngobrol-statistik', ['jenis' => $slug]) }}">
            {{ __('ngobrol.lihat_semua') }} <i class="fa fa-arrow-right"></i>
        </a>
    @endif
</div>
