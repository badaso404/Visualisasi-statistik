<header class="main-header style-two">
    <div class="top-bar style-two">
        <div class="auto-container">
            <div class="wrapper-box">
                <div class="left-content">
                    <div class="text"><span class="flaticon-point"></span> Jalan Raya Kembangan No.2 Kota Jakarta Barat Provinsi DKI Jakarta</div>
                </div>
                <div class="right-content">
                    <ul class="contact-info">
                        <li><a href="mailto:sekkojakbar@jakarta.go.id"><span class="flaticon-mail"></span>sekkojakbar@jakarta.go.id</a></li>
                        <li><a href="#"><span class="flaticon-phone"></span>021-58217409</a></li>
                        <li class="header-sosmed-li"> 
                            <a href="https://www.facebook.com/kotaadmjakartabarat"><span class="fa-brands fa-facebook"></span></a>
                            <a href="https://twitter.com/kotajakbar"><span class="fa-brands fa-x-twitter"></span></a>
                            <a href="https://www.youtube.com/channel/UChXtiMFK84Q1od1R_SvEbuQ/"><span class="fa-brands fa-youtube"></span></a>
                            <a href="https://www.instagram.com/kotajakartabarat"><span class="fa-brands fa-instagram"></span></a>
                            <a href="https://www.tiktok.com/discover/kota-jakarta-barat"><span class="fa-brands fa-tiktok"></span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="header-upper style-two">
        <div class="auto-container">
            <div class="wrapper-box">
                <div class="logo-column">
                    <div class="logo-box">
                        <div class="logo">
                            <a href="{{ route('home') }}"><img src="{{ asset('assets/landing-page/images/logo.png') }}" alt="Logo Jakarta Barat" class="header-logo-a"></a>
                        </div>
                    </div>
                </div>
                <div class="right-column">
                    <div class="option-wrapper">
                        <div class="nav-outer">
                            <nav class="main-menu navbar-expand-xl navbar-dark">
                                <div class="collapse navbar-collapse">
                                    <ul class="navigation">
                                        @foreach($landingPageData['menu'] as $m)
                                        <li class="dropdown">
                                            <a href="{{ $m['url'] != null && $m['route'] == \App\Enums\MenuRoute::Internal ? route($m['url']) : ($m['url'] != null ? url($m['url']) : '#') }}">{{ $m['name'] }}</a>
                                            @if(!empty($m['children']))
                                            <ul>
                                                @foreach($m['children'] as $children)
                                                <li class="dropdown">
                                                    <a href="{{ $children['url'] != null && $children['route'] == \App\Enums\MenuRoute::Internal ? route($children['url']) : ($children['url'] != null ? url($children['url']) : '#') }}">{{ $children['name'] }}</a>
                                                    @if(!empty($children['children']))

                                                    @php
                                                    $chunks = array_chunk($children['children'], 16);
                                                    @endphp

                                                    @foreach($chunks as $key => $chunk)
                                                    <ul class="{{ $key == 1 ? ' ond' : 'first' }}">
                                                        @foreach($chunk as $subChildren)
                                                        <li><a href="{{ $subChildren['url'] != null && $subChildren['route'] == \App\Enums\MenuRoute::Internal ? route($subChildren['url']) : ($subChildren['url'] != null ? url($subChildren['url']) : '#') }}">{{ $subChildren['name'] }}</a></li>
                                                        @endforeach
                                                    </ul>
                                                    @endforeach

                                                    @endif
                                                </li>
                                                @endforeach
                                            </ul>
                                            @endif
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </nav>
                        </div>

                        <div class="search-box-outer">
                            <div class="dropdown">
                                <button class="search-box-btn dropdown-toggle" type="button" id="dropdownMenu3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fa fa-search"></span></button>
                                <ul class="dropdown-menu pull-right search-panel" aria-labelledby="dropdownMenu3">
                                    <li class="panel-outer">
                                        <div class="form-container">
                                            <form method="get" action="{{ route('pencarian') }}">
                                                <div class="form-group">
                                                    <input type="search" name="keyword" value="" placeholder="Cari menu, berita, lainnya" required="">
                                                    <button type="submit" class="search-btn"><span class="fa fa-search"></span></button>
                                                </div>
                                            </form>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="navbar-btn-wrap">
                            <button class="anim-menu-btn">
                                <i class="flaticon-menu"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="sticky-header style-two">
        <div class="auto-container">
            <div class="wrapper-box">
                <div class="logo-column">
                    <div class="logo-box">
                        <div class="logo"><a href="{{ route('home') }}"><img class="logo-header" src="{{ asset('assets/landing-page/images/logo.png') }}" alt="Foto Logo Jakarta" title=""></a></div>
                    </div>
                </div>
                <div class="menu-column">
                    <div class="nav-outer">
                        <div class="nav-inner">
                            <nav class="main-menu navbar-expand-xl navbar-dark">
                                <div class="collapse navbar-collapse">
                                    <ul class="navigation">
                                    </ul>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mobile-menu style-two">
        <div class="menu-box">
            <div class="logo"><a href="#"><img src="{{ asset('assets/landing-page/images/logo-small.png') }}" alt="Foto Logo Jakarta"></a></div>
            <nav class="main-menu navbar-expand-xl navbar-dark">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="flaticon-menu"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navigation">

                    </ul>
                </div>
            </nav>
            <div class="search-box-outer">
                <div class="dropdown">
                    <button class="search-box-btn dropdown-toggle" type="button" id="dropdownMenu4" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fa fa-search"></span></button>
                    <ul class="dropdown-menu pull-right search-panel" aria-labelledby="dropdownMenu4">
                        <li class="panel-outer">
                            <div class="form-container">
                                <form method="get" action="{{ route('pencarian') }}">
                                    <div class="form-group">
                                        <input type="search" name="keyword" value="" placeholder="Cari menu, berita, lainnya" required="">
                                        <button type="submit" class="search-btn"><span class="fa fa-search"></span></button>
                                    </div>
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="nav-overlay">
        <div class="cursor"></div>
        <div class="cursor-follower"></div>
    </div>
</header>

@push('scripts')
<script>
    jQuery(function($) {
        $('.dropdown > a').click(function() {
            location.href = this.href;
        });
    });
</script>
@endpush