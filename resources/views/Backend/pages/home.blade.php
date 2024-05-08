@extends('Backend.index')

@section('title', '| Ana Sayfa')

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
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row gallery">
                            <div class="col-xl-2 col-lg-4 col-md-6 mt-2 mt-md-1 mb-md-1 mb-2">
                                <a href="{{ route('muhimmat.create') }}" class="btn btn-outline-dark w-100">Mühimmat
                                    Ekle</a>
                            </div>
                            <div class="col-xl-2 col-lg-4 col-md-6 mt-2 mt-md-1 mb-md-1 mb-2">
                                <a href="{{ route('makale.create') }}" class="btn btn-outline-dark w-100">Makale Yaz</a>
                            </div>
                            <div class="col-xl-2 col-lg-4 col-md-6 mt-2 mt-md-1 mb-md-1 mb-2">
                                <a href="{{ route('kategori.create') }}" class="btn btn-outline-dark w-100">Kategori
                                    Ekle</a>
                            </div>
                            <div class="col-xl-2 col-lg-4 col-md-6 mt-2 mt-md-1 mb-md-1 mb-2">
                                <a href="{{ route('ozellik.create') }}" class="btn btn-outline-dark w-100">Özellik
                                    Ekle</a>
                            </div>
                            <div class="col-xl-2 col-lg-4 col-md-6 mt-2 mt-md-1 mb-md-1 mb-2">
                                <a href="{{ route('varyant.create') }}" class="btn btn-outline-dark w-100">Varyant
                                    Ekle</a>
                            </div>
                            <div class="col-xl-2 col-lg-4 col-md-6 mt-2 mt-md-1 mb-md-1 mb-2">
                                <a href="{{ route('etiket.create') }}" class="btn btn-outline-dark w-100">Etiket
                                    Ekle</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                <div class="stats-icon red mb-2">
                                    <i class="iconly-boldDanger"></i>
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
                                <div class="stats-icon purple mb-2">
                                    <i class="iconly-boldEdit"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Makale</h6>
                                <h6 class="font-extrabold mb-0">{{ $totalPosts }}</h6>
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
                                    <i class="iconly-boldCategory"></i>
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
                                    <i class="iconly-boldProfile"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Kullanıcı</h6>
                                <h6 class="font-extrabold mb-0">{{ $totalUsers }}</h6>
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
                                    <i class="iconly-boldGraph"></i>
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
                                <div class="stats-icon green mb-2">
                                    <i class="iconly-boldChart"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Varyant</h6>
                                <h6 class="font-extrabold mb-0">{{ $totalVariants }}</h6>
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
                                <div class="stats-icon black mb-2">
                                    <i class="iconly-boldScan"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Etiket</h6>
                                <h6 class="font-extrabold mb-0">{{ $totalTags }}</h6>
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
                                    <i class="iconly-boldImage"></i>
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

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Mühimmat Kataloğu, Karşılaştırma ve Etki Analizi</h4>
                    </div>
                    <div class="card-body">
                        <div class="card-header" <h4 class="card-title"
                            style="margin-bottom: var(--bs-card-title-spacer-y);">
                            <font color="#607080" face="Times New Roman"><span
                                    style="font-size: 16px; font-weight: normal;">Modern savaşta mühimmat analizi,
                                    operasyonel etkinliği ve taktiksel avantajı optimize etmek için kritik önem
                                    taşımaktadır. Bu platform, hava-hava, hava-yer ve yer-yer mühimmat türlerine
                                    odaklanarak kapsamlı bir mühimmat analizi sunar. Platformumuz, savunma ve havacılık
                                    sektöründeki profesyonellerin yanı sıra araştırmacılara ve akademisyenlere değerli
                                    bilgiler sunmayı amaçlamaktadır.</span></font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span style="font-size: 16px;"><br></span>
                                </font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span
                                        style="font-size: 16px;">Platformumuzun
                                        Özellikleri:&nbsp;</span></font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span style="font-size: 16px;">Geniş
                                        Mühimmat
                                        Kataloğu: <span style="font-weight: normal;">Hava-hava, hava-yer ve yer-yer
                                            mühimmat
                                            türleri de dahil olmak üzere geniş bir mühimmat yelpazesini kapsar. Her mühimmat
                                            için detaylı teknik özellikler, performans verileri ve karşılaştırmalar
                                            sunar.</span></span></font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span style="font-size: 16px;">Gelişmiş
                                        Karşılaştırma Aracı: <span style="font-weight: normal;">Farklı mühimmat türlerini
                                            yan yana karşılaştırmanıza ve ihtiyaçlarınıza en uygun olanı seçmenize yardımcı
                                            olur. Balistik veriler, menzil, savaş başlığı türü ve fiyat gibi faktörleri göz
                                            önünde bulundurabilirsiniz.</span></span></font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span style="font-size: 16px;">Etki Analizi:
                                        <span style="font-weight: normal;">Farklı mühimmatların farklı hedeflerde nasıl
                                            performans göstereceğini görmenizi sağlar. Bu, hava savunması, tanksavar
                                            mücadelesi ve diğer savaş senaryoları için en uygun mühimmatı seçmenize yardımcı
                                            olabilir.</span></span></font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span style="font-size: 16px;">Uzman
                                        Makaleleri ve
                                        Rehberler: <span style="font-weight: normal;">Mühimmat analizi ve seçimi ile ilgili
                                            güncel bilgiler ve rehberler sunar. Kullanıcılar, platformumuzda en son trendler
                                            ve gelişmeler hakkında bilgi edinebilirler.</span></span></font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span style="font-size: 16px;">Veri
                                        Görselleştirme: <span style="font-weight: normal;">Karmaşık mühimmat verilerini
                                            anlamaya yardımcı olmak için grafikler, tablolar ve diğer görselleştirme
                                            araçları sunar.</span></span></font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span style="font-size: 16px;"><br></span>
                                </font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span
                                        style="font-size: 16px;">Platformumuzun
                                        Faydaları:&nbsp;</span></font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span style="font-size: 16px;">Geliştirilmiş
                                        Karar
                                        Verme: <span style="font-weight: normal;">Doğru mühimmat seçimi, savaşta kritik
                                            önem taşır. Platformumuz, kullanıcıların ihtiyaçlarına en uygun mühimmatı
                                            seçmelerine yardımcı olarak daha bilinçli karar vermelerine imkan
                                            sağlar.</span></span></font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span style="font-size: 16px;">Artan
                                        Operasyonel
                                        Etkinlik: <span style="font-weight: normal;">Doğru mühimmatı kullanmak, görev
                                            başarısını ve operasyonel etkinliği önemli ölçüde artırabilir.</span></span>
                                </font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span style="font-size: 16px;">Maliyet
                                        Optimizasyonu: <span style="font-weight: normal;">Platformumuz, kullanıcıların
                                            ihtiyaç duydukları mühimmatı bulmalarına ve gereksiz harcamalardan kaçınmalarına
                                            yardımcı olarak maliyetleri optimize etmelerine yardımcı olur.</span></span>
                                </font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span style="font-size: 16px;">Bilgiye
                                        Erişimi:
                                        <span style="font-weight: normal;">Platformumuz, kullanıcıların mühimmat analizi ve
                                            seçimi ile ilgili en son bilgilere erişmesini sağlayarak bilgi eksikliğini
                                            ortadan kaldırır.</span></span></font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span style="font-size: 16px;"><br></span>
                                </font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span style="font-size: 16px;">Hedef
                                        Kitlemiz:</span></font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span
                                        style="font-size: 16px; font-weight: normal;">Savunma ve havacılık sektöründeki
                                        profesyoneller</span></font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span
                                        style="font-size: 16px; font-weight: normal;">Araştırmacılar ve
                                        akademisyenler</span></font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span
                                        style="font-size: 16px; font-weight: normal;">Mühimmat seçimi ve analizi ile
                                        ilgilenen herkes</span></font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span style="font-size: 16px;"><br></span>
                                </font>
                            </h4>
                            <h4 class="card-title" style="margin-bottom: var(--bs-card-title-spacer-y);">
                                <font color="#607080" face="Times New Roman"><span
                                        style="font-size: 16px; font-weight: normal;">Platformumuz, mühimmat analizi ve
                                        seçimi için kapsamlı ve güvenilir bir kaynak sunarak savunma ve havacılık sektörüne
                                        değer katmayı amaçlamaktadır.</span></font>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@section('scripts')
    <link rel="stylesheet" href="{{ asset('backend_assets/compiled/css/iconly.css') }}">
@endsection
