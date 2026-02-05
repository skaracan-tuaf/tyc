<div class="dis-none panel-filter w-full p-t-10">
    <div class="wrap-filter flex-w bg6 w-full p-lr-40 p-t-27 p-lr-15-sm">
        @php
            $currentSort = request('sort', 'default');
            $currentTargetType = request('target_type');
            $currentCategoryId = request('category_id');
        @endphp

        <div class="filter-col1 p-r-15 p-b-27">
            <div class="mtext-102 cl2 p-b-15">
                Sırala
            </div>
            <ul>
                <li class="p-b-6">
                    <a href="{{ route('AnaSayfa') }}"
                        class="filter-link stext-106 trans-04 {{ $currentSort === 'default' ? 'filter-link-active' : '' }}">
                        Varsayılan
                    </a>
                </li>
                <li class="p-b-6">
                    <a href="{{ route('AnaSayfa', array_merge(request()->except('page'), ['sort' => 'price_asc'])) }}"
                        class="filter-link stext-106 trans-04 {{ $currentSort === 'price_asc' ? 'filter-link-active' : '' }}">
                        Fiyat (Düşük &rarr; Yüksek)
                    </a>
                </li>
                <li class="p-b-6">
                    <a href="{{ route('AnaSayfa', array_merge(request()->except('page'), ['sort' => 'price_desc'])) }}"
                        class="filter-link stext-106 trans-04 {{ $currentSort === 'price_desc' ? 'filter-link-active' : '' }}">
                        Fiyat (Yüksek &rarr; Düşük)
                    </a>
                </li>
                <li class="p-b-6">
                    <a href="{{ route('AnaSayfa', array_merge(request()->except('page'), ['sort' => 'score_desc'])) }}"
                        class="filter-link stext-106 trans-04 {{ $currentSort === 'score_desc' ? 'filter-link-active' : '' }}">
                        Skor (En Yüksek)
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
                    <a href="{{ route('AnaSayfa', request()->except('target_type', 'page')) }}"
                        class="filter-link stext-106 trans-04 {{ empty($currentTargetType) ? 'filter-link-active' : '' }}">
                        Tümü
                    </a>
                </li>
                <li class="p-b-6">
                    <a href="{{ route('AnaSayfa', array_merge(request()->except('page'), ['target_type' => 'SOFT'])) }}"
                        class="filter-link stext-106 trans-04 {{ $currentTargetType === 'SOFT' ? 'filter-link-active' : '' }}">
                        SOFT
                    </a>
                </li>
                <li class="p-b-6">
                    <a href="{{ route('AnaSayfa', array_merge(request()->except('page'), ['target_type' => 'HARD'])) }}"
                        class="filter-link stext-106 trans-04 {{ $currentTargetType === 'HARD' ? 'filter-link-active' : '' }}">
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
                    <a href="{{ route('AnaSayfa', request()->except('category_id', 'page')) }}"
                        class="filter-link stext-106 trans-04 {{ empty($currentCategoryId) ? 'filter-link-active' : '' }}">
                        Tümü
                    </a>
                </li>
                @foreach ($categories as $category)
                    @if ($category->parent_id === null)
                        <li class="p-b-6">
                            <span class="fs-15 lh-12 m-r-6" style="color: #222;">
                                <i class="zmdi zmdi-circle"></i>
                            </span>
                            <a href="{{ route('AnaSayfa', array_merge(request()->except('page'), ['category_id' => $category->id])) }}"
                                class="filter-link stext-106 trans-04 {{ (string) $currentCategoryId === (string) $category->id ? 'filter-link-active' : '' }}">
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
                    <a href="{{ route('Blog', ['tag' => $tag->slug]) }}"
                        class="flex-c-m stext-107 cl6 size-301 bor7 p-lr-15 hov-tag1 trans-04 m-r-5 m-b-5">
                        {{ $tag->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
