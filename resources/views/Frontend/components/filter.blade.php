<div class="dis-none panel-filter w-full p-t-10">
    <div class="wrap-filter flex-w bg6 w-full p-lr-40 p-t-27 p-lr-15-sm">
        <div class="filter-col1 p-r-15 p-b-27">
            <div class="mtext-102 cl2 p-b-15">
                Sırala
            </div>
            <ul>
                <li class="p-b-6">
                    <a href="#" class="filter-link stext-106 trans-04 filter-link-active">
                        Varsayılan
                    </a>
                </li>
                <li class="p-b-6">
                    <a href="#" class="filter-link stext-106 trans-04">
                        Hız
                    </a>
                </li>
                <li class="p-b-6">
                    <a href="#" class="filter-link stext-106 trans-04">
                        Menzil
                    </a>
                </li>
                <li class="p-b-6">
                    <a href="#" class="filter-link stext-106 trans-04">
                        Fiyat
                    </a>
                </li>
            </ul>
        </div>

        <div class="filter-col2 p-r-15 p-b-27">
            <div class="mtext-102 cl2 p-b-15">
                Hedef Tipi
            </div>
            <ul>
                <li class="p-b-6">
                    <a href="#" class="filter-link stext-106 trans-04 filter-link-active">
                        Tümü
                    </a>
                </li>
                <li class="p-b-6">
                    <a href="#" class="filter-link stext-106 trans-04">
                        SOFT
                    </a>
                </li>
                <li class="p-b-6">
                    <a href="#" class="filter-link stext-106 trans-04">
                        HARD
                    </a>
                </li>
            </ul>
        </div>

        <div class="filter-col3 p-r-15 p-b-27">
            <div class="mtext-102 cl2 p-b-15">
                Kategori
            </div>
            <ul>
                <li class="p-b-6">
                    <span class="fs-15 lh-12 m-r-6" style="color: #222;">
                        <i class="zmdi zmdi-circle"></i>
                    </span>
                    <a href="#" class="filter-link stext-106 trans-04 filter-link-active">
                        Tümü
                    </a>
                </li>
                @foreach ($categories as $category)
                    @if ($category->parent_id === null)
                        <li class="p-b-6">
                            <span class="fs-15 lh-12 m-r-6" style="color: #222;">
                                <i class="zmdi zmdi-circle"></i>
                            </span>
                            <a href="#" class="filter-link stext-106 trans-04">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>

        <div class="filter-col4 p-b-27">
            <div class="mtext-102 cl2 p-b-15">
                Etiketler
            </div>
            <div class="flex-w p-t-4 m-r--5">

                @foreach ($tags as $tag)
                    <a href="#"
                        class="flex-c-m stext-107 cl6 size-301 bor7 p-lr-15 hov-tag1 trans-04 m-r-5 m-b-5">
                        {{ $tag->name }}
                    </a>
                @endforeach

            </div>
        </div>
    </div>
</div>
