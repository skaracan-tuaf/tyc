@extends('Backend.index')

@section('title', 'Ana Sayfa')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/compiled/css/iconly.css') }}">
@endsection

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Yönetici Ana Sayfa')

@section('breadcrumb')

@endsection

@section('content')
    <section class="section">
        <div class="row">
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                <div class="stats-icon purple mb-2">
                                    <i class="iconly-boldShow"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Mühimmat</h6>
                                <h6 class="font-extrabold mb-0">{{ $totalMunitions }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                <div class="stats-icon blue mb-2">
                                    <i class="iconly-boldProfile"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Kategori</h6>
                                <h6 class="font-extrabold mb-0">{{ $totalCategories }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                <div class="stats-icon green mb-2">
                                    <i class="iconly-boldAdd-User"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Özellik</h6>
                                <h6 class="font-extrabold mb-0">{{ $totalAttributes }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                <div class="stats-icon red mb-2">
                                    <i class="iconly-boldBookmark"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Resim</h6>
                                <h6 class="font-extrabold mb-0">{{ $totalImages }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Mühimmat Kataloğu, Karşılaştırma ve Etki Analizi</h4>
            </div>
            <div class="card-body">
                Modern savaşta mühimmat analizi, operasyonel etkinliği ve taktiksel avantajı optimize etmek için kritik önem
                taşımaktadır. Bu platform, hava-hava, hava-yer ve yer-yer mühimmat türlerine odaklanarak kapsamlı bir
                mühimmat analizi sunar. Platformumuz, savunma ve havacılık sektöründeki profesyonellerin yanı sıra
                araştırmacılara ve akademisyenlere değerli bilgiler sunmayı amaçlamaktadır.<br>
                <br>
                <strong>Platformumuzun Özellikleri:</strong><br>
                <br>
                Geniş Mühimmat Kataloğu: Hava-hava, hava-yer ve yer-yer mühimmat türleri de dahil olmak üzere geniş bir
                mühimmat yelpazesini kapsar. Her mühimmat için detaylı teknik özellikler, performans verileri ve
                karşılaştırmalar sunar.<br>
                Gelişmiş Karşılaştırma Aracı: Farklı mühimmat türlerini yan yana karşılaştırmanıza ve ihtiyaçlarınıza en
                uygun olanı seçmenize yardımcı olur. Balistik veriler, menzil, savaş başlığı türü ve fiyat gibi faktörleri
                göz önünde bulundurabilirsiniz.<br>
                Etki Analizi: Farklı mühimmatların farklı hedeflerde nasıl performans göstereceğini görmenizi sağlar. Bu,
                hava savunması, tanksavar mücadelesi ve diğer savaş senaryoları için en uygun mühimmatı seçmenize yardımcı
                olabilir.<br>
                Uzman Makaleleri ve Rehberler: Mühimmat analizi ve seçimi ile ilgili güncel bilgiler ve rehberler sunar.
                Kullanıcılar, platformumuzda en son trendler ve gelişmeler hakkında bilgi edinebilirler.<br>
                Veri Görselleştirme: Karmaşık mühimmat verilerini anlamaya yardımcı olmak için grafikler, tablolar ve diğer
                görselleştirme araçları sunar.<br>
                <br>
                <strong>Platformumuzun Faydaları:</strong><br>
                <br>
                Geliştirilmiş Karar Verme: Doğru mühimmat seçimi, savaşta kritik önem taşır. Platformumuz, kullanıcıların
                ihtiyaçlarına en uygun mühimmatı seçmelerine yardımcı olarak daha bilinçli karar vermelerine imkan
                sağlar.<br>
                Artan Operasyonel Etkinlik: Doğru mühimmatı kullanmak, görev başarısını ve operasyonel etkinliği önemli
                ölçüde artırabilir.<br>
                Maliyet Optimizasyonu: Platformumuz, kullanıcıların ihtiyaç duydukları mühimmatı bulmalarına ve gereksiz
                harcamalardan kaçınmalarına yardımcı olarak maliyetleri optimize etmelerine yardımcı olur.<br>
                Bilgiye Erişimi: Platformumuz, kullanıcıların mühimmat analizi ve seçimi ile ilgili en son bilgilere
                erişmesini sağlayarak bilgi eksikliğini ortadan kaldırır.<br>
                <br>
                <strong>Hedef Kitlemiz:</strong><br>
                <br>
                Savunma ve havacılık sektöründeki profesyoneller<br>
                Araştırmacılar ve akademisyenler<br>
                Mühimmat seçimi ve analizi ile ilgilenen herkes<br>
                <br>
                Platformumuz, mühimmat analizi ve seçimi için kapsamlı ve güvenilir bir kaynak sunarak savunma ve havacılık
                sektörüne değer katmayı amaçlamaktadır.
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <link rel="stylesheet" href="{{ asset('backend_assets/compiled/css/iconly.css') }}">
@endsection
