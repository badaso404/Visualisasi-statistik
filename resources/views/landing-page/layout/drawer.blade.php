<section class="hidden-sidebar style-two close-sidebar">
    <div class="wrapper-box">
        <div class="hidden-sidebar-close"><span class="flaticon-cross"></span></div>
        <div class="logo"><a href="{{ route('home') }}"><img src="{{ asset('assets/landing-page/images/logo-small.png') }}" alt="Foto Logo"></a></div>
        <div class="content">
            <div class="about-widget-four sidebar-widget">
                <h3>Kantor Walikota Administrasi Jakarta Barat</h3>
                <div class="text">Jl. Kembangan Raya No.2, RT.5/RW.2, Kembangan Selatan, Kecamatan Kembangan, Kota Jakarta Barat, Daerah Khusus Ibukota Jakarta 11610.</div>
            </div>
            <div class="news-widget-two sidebar-widget">
                <div class="widget-title">Berita Terbaru</div>
                @foreach ($landingPageData['beritaTerbaru']->take(2) as $b)
                <div class="post-wrapper">
                    <div class="image w-100"><a href="{{ route('berita-detail', $b->slug) }}"><img src="{{ asset('storage/images/berita/thumbnail/'.$b->thumbnail) }}" alt="Foto Berita"></a></div>
                    <div class="category">{{ $b->kategori->text() }}</div>
                    <h4><a href="{{ route('berita-detail', $b->slug) }}">{{ Str::limit($b->title, 62) }}</a></h4>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>