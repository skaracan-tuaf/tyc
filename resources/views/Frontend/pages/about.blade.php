@extends('Frontend.index')

@section('title', '| Hakkımızda')

@section('content')
    <!-- Title page -->
    <section class="bg-img1 txt-center p-lr-15 p-tb-92"
        style="background-image: url('{{ asset('frontend_assets/images/bg-03.jpg') }}');">
        <h2 class="ltext-105 cl0 txt-center">
            Hakkımızda
        </h2>
    </section>


    <!-- Content page -->
    <section class="bg0 p-t-75 p-b-120">
        <div class="container">
            <div class="row p-b-148">
                <div class="col-md-7 col-lg-8">
                    <div class="p-t-7 p-r-85 p-r-15-lg p-r-0-md">
                        <h3 class="mtext-111 cl2 p-b-16">
                            Hikayemiz
                        </h3>

                        <p class="stext-113 cl6 p-b-26">
                            Hava ve Uzay Gücü Komutanlığı, Türk havacılık tarihinin en köklü kurumlarından biridir. 1911
                            yılında kurulan Hava Kuvvetleri Komutanlığı, 2021 yılında Hava ve Uzay Gücü Komutanlığı'na
                            dönüştürülmüştür. Komutanlık, Türkiye'nin hava ve uzay hakimiyetini korumak ve geliştirmek için
                            görev yapmaktadır.
                        </p>

                        <p class="stext-113 cl6 p-b-26">
                            Hava ve Uzay Gücü Komutanlığı, Türkiye'nin en modern ve güçlü havacılık güçlerinden biridir.
                            Komutanlık, geniş bir yelpazede hava savunma, havadan havaya, havadan yere ve elektronik harp
                            sistemlerine sahiptir. Komutanlık ayrıca uzay araştırmaları ve uzay güvenliği alanlarında da
                            çalışmalar yürütmektedir.
                        </p>

                        <p class="stext-113 cl6 p-b-26">
                            Hava ve Uzay Gücü Komutanlığı, ulusal ve uluslararası barışa katkıda bulunmak için de aktif rol
                            oynamaktadır. Komutanlık, NATO ve diğer uluslararası organizasyonlarla iş birliği içinde
                            faaliyet göstermektedir.
                        </p>
                        <p class="stext-113 cl6 p-b-26">
                            Hava ve Uzay Gücü Komutanlığı, Türk havacılık tarihinin en önemli başarılarına imza atmıştır.
                            Komutanlık, Çanakkale Savaşı'ndan Kurtuluş Savaşı'na, Kıbrıs Barış Harekatı'ndan günümüze kadar
                            birçok önemli operasyonda görev almıştır.
                        </p>
                        <p class="stext-113 cl6 p-b-26">
                            Hava ve Uzay Gücü Komutanlığı, Türkiye'nin havacılık ve uzay alanında lider bir güç olmasını
                            sağlamak için çalışmalarını sürdürmektedir. Komutanlık, geleceğe güvenle bakmakta ve Türkiye'nin
                            hava ve uzay hakimiyetini korumak için her zaman hazır olmaktadır.
                        </p>
                    </div>
                </div>

                <div class="col-11 col-md-5 col-lg-4 m-lr-auto">
                    <div class="how-bor1 ">
                        <div class="hov-img0">
                            <img src="{{ asset('frontend_assets/images/icons/logo-04.png') }}" alt="IMG">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="order-md-2 col-md-7 col-lg-8 p-b-30">
                    <div class="p-t-7 p-l-85 p-l-15-lg p-l-0-md">
                        <h3 class="mtext-111 cl2 p-b-16">
                            Görevimiz
                        </h3>

                        <p class="stext-113 cl6 p-b-26">
                            Hava ve Uzay Gücü Komutanlığı'nın temel görevi, Türkiye'nin hava ve uzay hakimiyetini korumak ve
                            geliştirmektir. Bu görevi yerine getirmek için komutanlık aşağıdaki faaliyetleri yürütmektedir:
                        </p>
                        <p class="stext-113 cl6 p-b-26">
                            Hava Savunması: Türkiye'nin hava sahasını korumak ve savunmak.
                            Havadan Havaya Muharebe: Düşman hava araçlarını etkisiz hale getirmek.
                            Havadan Yere Muharebe: Yer hedeflerini vurmak.
                            Elektronik Harp: Düşman haberleşme ve radar sistemlerini etkisiz hale getirmek.
                            Uzay Araştırmaları ve Uzay Güvenliği: Uzay araştırmaları yapmak ve uzaydaki varlıklarımızı
                            korumak.
                            Ulusal ve Uluslararası Barışa Katkı: Ulusal ve uluslararası barışa katkıda bulunmak.
                        </p>
                        <p class="stext-113 cl6 p-b-26">
                            Hava ve Uzay Gücü Komutanlığı, bu görevleri yerine getirirken en son teknolojiyi kullanmakta ve
                            personele en iyi eğitimi vermektedir. Komutanlık, Türkiye'nin havacılık ve uzay alanında lider
                            bir güç olmasını sağlamak için çalışmalarını sürdürmektedir.
                        </p>

                        <div class="bor16 p-l-29 p-b-9 m-t-22">
                            <p class="stext-114 cl6 p-r-40 p-b-11">
                                Bir ulusun asker ordusu ne kadar güçlü olursa olsun, kazandığı zafer ne kadar yüce olursa
                                olsun, bir ulus ilim ordusuna sahip değilse, savaş meydanlarında kazanılmış zaferlerin sonu
                                olacaktır. Bu nedenle bir an önce büyük, mükemmel bir ilim ordusuna sahip olma zorunluluğu
                                vardır.
                            </p>

                            <span class="stext-111 cl8">
                                - Mustafa Kemal ATATÜRK
                            </span>
                        </div>
                    </div>
                </div>

                <div class="order-md-1 col-11 col-md-5 col-lg-4 m-lr-auto p-b-30">
                    <div class="how-bor2">
                        <div class="hov-img0">
                            <img src="{{ asset('frontend_assets/images/HVKK Logo_PNG.png') }}" alt="IMG">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
