<footer class="main-footer style-two">
    
    <div class="auto-container">
        <div class="widget-wrapper">
            <div class="row">
                
                <div class="col-lg-12 footer-widget text-center">
                    <h4 class="widget-title-pengunjung">Pengunjung Website</h4>
                    <div class="row text-light">
                        @foreach($landingPageData['statistikPengunjung'] as $sp)
                        <div class="col-lg col-md-6">
                            <div class="pengunjung-box">
                                <div class="d-flex flex-row align-items-center">
                                    <div class="p-2">
                                        <h1 class="pengunjung-icon {{ $sp->color ?? '' }}"><span class="{{ $sp->icon ?? '' }}"></span></h1>
                                    </div>
                                    <div class="text-left">
                                        <p class="pengunjung-kategori-waktu">{{ $sp->label ?? '-' }}</p>
                                        <h1 class="pengunjung-counter"> {{ $sp->value ?? '-' }} </h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-4 col-md-12 footer-widget">
                    <h4 class="widget-title-two">Alamat</h4>
                    <div class="text about-widget-two mb-4">
                        <h4 class="mt-3">Kantor Walikota Administrasi Jakarta Barat</h4>
                    </div>
                    <div class="row">
                        <div class="col-5">
                            <div class="wrapper-box mb-4">
                                <img src="{{ asset('assets/landing-page/images/logo-lambang.png') }}" alt="Logo Lambang">
                            </div> 
                        </div>
                        <div class="col-7">
                            <p class="mb-0 alamat-p"><span class="flaticon-point alamat-span"></span> Jl. Kembangan Raya No.2, RT.5/RW.2, Kembangan Selatan, Kecamatan Kembangan, Kota Jakarta Barat, Daerah Khusus Ibukota Jakarta 11610</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 about-widget-two footer-widget">
                    <h4 class="widget-title-two">Peta Lokasi</h4>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.586676369066!2d106.7337963147689!3d-6.186036995521404!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f77f9801454d%3A0x6295551390460481!2sKantor%20Walikota%20Jakarta%20Barat!5e0!3m2!1sen!2sid!4v1679999999999!5m2!1sen!2sid" width="100%" height="250px" style="border-radius :10px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>

                <div class="col-lg-4 col-md-6 post-widget footer-widget">
                    <h4 class="widget-title-two">Berita Populer</h4>
                    @foreach ($landingPageData['beritaPopuler']->take(3) as $b)
                    <div class="row footer-berita-populer">
                        <div class="col-lg-3 col-4">
                            <a href="{{ route('berita-detail', $b->slug) }}">
                                <div class="image"><img src="{{ asset('storage/images/berita/'.$b->img) }}" alt="Foto Berita"></div>
                            </a>
                        </div>
                        <div class="col-lg-9 col-8">
                            <div class="d-flex">
                                <div class="views mr-auto"><span class="fa fa-calendar-days text-custom-1"></span> {{ \Carbon\Carbon::parse($b->published_at)->isoFormat('D MMMM Y') }}</div>
                                <div class="views"><span class="fa fa-eye text-custom-1"></span> {{ $b->viewed }}</div>
                            </div>
                            <h6><a href="{{ route('berita-detail', $b->slug) }}">{{ $b->title }}</a></h6>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div> </div>     </div>         <div class="footer-bottom-three">
        <div class="auto-container">
            <ul class="social-icon-four">
                <li><a href="https://www.facebook.com/kotaadmjakartabarat" class="facebook" target="_blank"><span class="fa-brands fa-facebook-f"></span></a></li>
                <li><a href="https://twitter.com/kotajakbar" class="twitter" target="_blank"><span class="fa-brands fa-x-twitter"></span></a></li>
                <li><a href="https://www.youtube.com/channel/UChXtiMFK84Q1od1R_SvEbuQ" class="youtube" target="_blank"><span class="fa-brands fa-youtube"></span></a></li>
                <li><a href="https://www.instagram.com/kotajakartabarat/" class="instagram" target="_blank"><span class="fa-brands fa-instagram"></span></a></li>
                <li><a href="https://www.tiktok.com/discover/kota-jakarta-barat" class="tiktok" target="_blank"><span class="fa-brands fa-tiktok"></span></a></li>
            </ul>
        </div>
    </div>


    <div class="footer-bottom-two">
        <div class="auto-container"> <div class="copy-right-text">© 2025 Copyright <a class="theme-color-two" href="#">Sudin Kominfotik Jakarta Barat</a></div>
            <div class="copy-right-subtext">Mengalami masalah teknis? Hubungi <a class="theme-color-two" href="https://wa.me/+6281211255934"> CS BATIK</a></div>
        </div>
    </div>

</footer>

<div class="scroll-to-top scroll-to-target style-two" data-target="html"><span class="icon flaticon-next"></span></div>