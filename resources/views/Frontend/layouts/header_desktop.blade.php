<div class="container-menu-desktop trans-03">
    <div class="wrap-menu-desktop">
        <nav class="limiter-menu-desktop p-l-45">

            <!-- Logo desktop -->
            <a href="{{ route('AnaSayfa') }}" class="logo">
                <img src="{{ asset('frontend_assets/images/icons/logo-03.png') }}" alt="IMG-LOGO">
            </a>

            <!-- Menu desktop -->
            <div class="menu-desktop">
                <ul class="main-menu">
                    <li class="{{ request()->routeIs('AnaSayfa') ? ' active-menu' : '' }}">
                        <a href="{{ route('AnaSayfa') }}">Ana Sayfa</a>
                    </li>

                    <li>
                        <a href="#">Kategoriler</a>
                        <ul class="sub-menu">
                            @foreach ($categories as $category)
                                @if ($category->parent_id === null)
                                    <li><a href="{{ route('kategoriFiltresi', $category->slug) }}">{{ $category->name }}</a></li>
                                @endif
                            @endforeach
                        </ul>
                    </li>

                    <li class="{{ request()->routeIs('Blog') ? ' active-menu' : '' }}">
                        <a href="{{ route('Blog') }}">Blog</a>
                    </li>

                    <li class="{{ request()->routeIs('Hakkimizda') ? ' active-menu' : '' }}">
                        <a href="{{ route('Hakkimizda') }}">Hakkımızda</a>
                    </li>

                    <li class="{{ request()->routeIs('Iletisim') ? ' active-menu' : '' }}">
                        <a href="{{ route('Iletisim') }}">İletişim</a>
                    </li>
                </ul>
            </div>

            <!-- Icon header -->
            <div class="wrap-icon-header flex-w flex-r-m h-full">
                <div class="flex-c-m h-full p-r-24">
                    <div class="icon-header-item cl2 hov-cl1 trans-04 p-lr-11 js-show-modal-search">
                        <i class="zmdi zmdi-search"></i>
                    </div>
                </div>

                <div class="flex-c-m h-full p-l-18 p-r-25 bor5">
                    <div class="icon-header-item cl2 hov-cl1 trans-04 p-lr-11 js-show-cart">
                        <i class="zmdi zmdi-filter-list"></i>
                    </div>
                </div>

                <div class="flex-c-m h-full p-lr-19">
                    <div class="icon-header-item cl2 hov-cl1 trans-04 p-lr-11 js-show-sidebar">
                        <i class="zmdi zmdi-menu"></i>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</div>
