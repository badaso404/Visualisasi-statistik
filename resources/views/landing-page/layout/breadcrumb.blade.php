<section class="page-title" style="background-image: url({{ asset('assets/landing-page/images/background/overlay.jpg') }})">
    <div class="auto-container">
        <div class="content-box">
            @isset($mainMenu)
            <ul class="bread-crumb">
                <li><a class="home" href="{{ route('home') }}"><span class="fa fa-home"></span></a></li>
                <li>{{ $mainMenu ?? '' }}</li>

                @if(isset($subMenu) && $mainMenu != "Publikasi")
                <li>{{ $subMenu }}</li>
                @endif

                @isset($detailMenu)
                <li>{{ $detailMenu }}</li>
                @endisset
            </ul>
            @endisset
        </div>
    </div>
</section>  