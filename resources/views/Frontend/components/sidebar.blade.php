<aside class="wrap-sidebar js-sidebar">
    <div class="s-full js-hide-sidebar"></div>

    <div class="sidebar flex-col-l p-t-22 p-b-25">
        <div class="flex-r w-full p-b-30 p-r-27">
            <div class="fs-35 lh-10 cl2 p-lr-5 pointer hov-cl1 trans-04 js-hide-sidebar">
                <i class="zmdi zmdi-close"></i>
            </div>
        </div>

        <div class="sidebar-content flex-w w-full p-lr-65 js-pscroll">
            <ul class="sidebar-link w-full">
                <li class="p-b-13">
                    <a href="{{ route('AnaSayfa') }}" class="stext-102 cl2 hov-cl1 trans-04">
                        Ana Sayfa
                    </a>
                </li>

                <li class="p-b-13">
                    <a href="{{ route('Blog') }}" class="stext-102 cl2 hov-cl1 trans-04">
                        Blog
                    </a>
                </li>

                <li class="p-b-13">
                    <a href="{{ route('Hakkimizda') }}" class="stext-102 cl2 hov-cl1 trans-04">
                        Hakkımızda
                    </a>
                </li>

                <li class="p-b-13">
                    <a href="{{ route('Iletisim') }}" class="stext-102 cl2 hov-cl1 trans-04">
                        İletişim
                    </a>
                </li>
            </ul>

            <div class="sidebar-gallery w-full p-tb-30">
                <span class="mtext-101 cl5">
                    @ HUGEM K.lığı
                </span>

                <div class="flex-w flex-sb p-t-36 gallery-lb">
                    <!-- item gallery sidebar -->
                    <div class="wrap-item-gallery m-b-10">
                        <a class="item-gallery bg-img1" href="{{ asset('frontend_assets/images/gallery-01.jpg') }}"
                            data-lightbox="gallery"
                            style="background-image: url('{{ asset('frontend_assets/images/gallery-01.jpg') }}');"></a>
                    </div>

                    <!-- item gallery sidebar -->
                    <div class="wrap-item-gallery m-b-10">
                        <a class="item-gallery bg-img1" href="{{ asset('frontend_assets/images/gallery-02.jpg') }}"
                            data-lightbox="gallery"
                            style="background-image: url('{{ asset('frontend_assets/images/gallery-02.jpg') }}');"></a>
                    </div>

                    <!-- item gallery sidebar -->
                    <div class="wrap-item-gallery m-b-10">
                        <a class="item-gallery bg-img1" href="{{ asset('frontend_assets/images/gallery-03.jpg') }}"
                            data-lightbox="gallery"
                            style="background-image: url('{{ asset('frontend_assets/images/gallery-03.jpg') }}');"></a>
                    </div>

                    <!-- item gallery sidebar -->
                    <div class="wrap-item-gallery m-b-10">
                        <a class="item-gallery bg-img1" href="{{ asset('frontend_assets/images/gallery-04.jpg') }}"
                            data-lightbox="gallery"
                            style="background-image: url('{{ asset('frontend_assets/images/gallery-04.jpg') }}');"></a>
                    </div>

                    <!-- item gallery sidebar -->
                    <div class="wrap-item-gallery m-b-10">
                        <a class="item-gallery bg-img1" href="{{ asset('frontend_assets/images/gallery-05.jpg') }}"
                            data-lightbox="gallery"
                            style="background-image: url('{{ asset('frontend_assets/images/gallery-05.jpg') }}');"></a>
                    </div>

                    <!-- item gallery sidebar -->
                    <div class="wrap-item-gallery m-b-10">
                        <a class="item-gallery bg-img1" href="{{ asset('frontend_assets/images/gallery-06.jpg') }}"
                            data-lightbox="gallery"
                            style="background-image: url('{{ asset('frontend_assets/images/gallery-06.jpg') }}');"></a>
                    </div>

                    <!-- item gallery sidebar -->
                    <div class="wrap-item-gallery m-b-10">
                        <a class="item-gallery bg-img1" href="{{ asset('frontend_assets/images/gallery-07.jpg') }}"
                            data-lightbox="gallery"
                            style="background-image: url('{{ asset('frontend_assets/images/gallery-07.jpg') }}');"></a>
                    </div>

                    <!-- item gallery sidebar -->
                    <div class="wrap-item-gallery m-b-10">
                        <a class="item-gallery bg-img1" href="{{ asset('frontend_assets/images/gallery-08.jpg') }}"
                            data-lightbox="gallery"
                            style="background-image: url('{{ asset('frontend_assets/images/gallery-08.jpg') }}');"></a>
                    </div>

                    <!-- item gallery sidebar -->
                    <div class="wrap-item-gallery m-b-10">
                        <a class="item-gallery bg-img1" href="{{ asset('frontend_assets/images/gallery-09.jpg') }}"
                            data-lightbox="gallery"
                            style="background-image: url('{{ asset('frontend_assets/images/gallery-09.jpg') }}');"></a>
                    </div>
                </div>
            </div>

            <div class="sidebar-gallery w-full">
                <span class="mtext-101 cl5">
                    Hakkımızda
                </span>

                <p class="stext-108 cl6 p-t-27">
                    Hava ve Uzay Gücü Komutanlığı, gökleri vatan bilerek Türkiye'nin güvenliğini ve bekasını korumak
                    için var. Modern hava savunma sistemleri, güçlü muharip filoları ve uzay yetenekleriyle ülkemizin
                    hava ve uzay hakimiyetini kararlılıkla savunuyoruz. Barışın teminatı, havacılığın öncüsü olarak
                    geleceğe güvenle uçuyoruz.
                </p>
            </div>
        </div>
    </div>
</aside>
