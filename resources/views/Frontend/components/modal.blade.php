<div class="wrap-modal1 js-modal1 p-t-60 p-b-20" id="modal-product-{{ $munition->id }}">
    <div class="overlay-modal1 js-hide-modal1"></div>

    <div class="container">
        <div class="bg0 p-t-60 p-b-30 p-lr-15-lg how-pos3-parent">
            <button class="how-pos3 hov3 trans-04 js-hide-modal1">
                <img src="{{ asset('frontend_assets/images/icons/icon-close.png') }}" alt="CLOSE">
            </button>

            <div class="row">
                <div class="col-md-6 col-lg-7 p-b-30">
                    <div class="p-l-25 p-r-30 p-lr-0-lg">
                        <div class="wrap-slick3 flex-sb flex-w">
                            <div class="wrap-slick3-dots"></div>
                            <div class="wrap-slick3-arrows flex-sb-m flex-w"></div>

                            <div class="slick3 gallery-lb">
                                @if ($munition->images->isNotEmpty())
                                    @foreach ($munition->images as $image)
                                        <div class="item-slick3" data-thumb="{{ asset('storage/' . $image->url) }}">
                                            <div class="wrap-pic-w pos-relative">
                                                <img src="{{ asset('storage/' . $image->url) }}" alt="IMG-PRODUCT">

                                                <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04"
                                                    href="{{ asset('storage/' . $image->url) }}">
                                                    <i class="fa fa-expand"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="item-slick3"
                                        data-thumb="{{ asset('frontend_assets/images/product-detail-03.jpg') }}">
                                        <div class="wrap-pic-w pos-relative">
                                            <img src="{{ asset('frontend_assets/images/product-detail-03.jpg') }}"
                                                alt="IMG-PRODUCT">

                                            <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04"
                                                href="{{ asset('frontend_assets/images/product-detail-03.jpg') }}">
                                                <i class="fa fa-expand"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-5 p-b-30">
                    <div class="p-r-50 p-t-5 p-lr-0-lg">
                        <h4 class="mtext-105 cl2 js-name-detail p-b-14">
                            {{ $munition->name }}
                        </h4>

                        <span class="mtext-106 cl2">
                            ${{ number_format($munition->price, 2, ',', '.') }}
                        </span>

                        <p class="stext-102 cl3 p-t-23">
                            {{ $munition->description }}
                        </p>

                        <!--  -->
                        <div class="p-t-33">

                            @foreach ($munition->attributes as $attribute)
                                <li class="flex-w flex-t p-b-7">
                                    <span class="stext-102 cl3 size-205">
                                        {{ $attribute->name }}
                                    </span>
                                    <span class="stext-102 cl6 size-206">
                                        @php
                                            $attrValue = $attribute;
                                        @endphp
                                        @if ($attrValue)
                                            @if ($attribute->option === 'Liste')
                                                {{ $attribute->listValues->where('id', $attrValue->pivot->value)->first()->value ?? '' }}
                                            @elseif ($attribute->option === 'Doğrulama')
                                                @if ($attrValue->pivot->value == 1)
                                                    Var
                                                @else
                                                    Yok
                                                @endif
                                            @elseif ($attribute->option === 'Aralık')
                                                {{ $attrValue->pivot->min ?? '' }} -
                                                {{ $attrValue->pivot->max ?? '' }}
                                            @else
                                                {{ $attrValue->pivot->value ?? '' }}
                                            @endif
                                        @endif
                                    </span>
                                </li>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
