<div class="wrap-header-cart js-panel-cart">
    <div class="s-full js-hide-cart"></div>

    <div class="header-cart flex-col-l p-l-65 p-r-25">
        <div class="header-cart-title flex-w flex-sb-m p-b-8">
            <span class="mtext-103 cl2">
                Filtrele
            </span>

            <div class="fs-35 lh-10 cl2 p-lr-5 pointer hov-cl1 trans-04 js-hide-cart">
                <i class="zmdi zmdi-close"></i>
            </div>
        </div>

        <div class="header-cart-content flex-w js-pscroll">
            <form action="{{ route('Kiyasla') }}" method="GET" class="header-cart-content flex-w js-pscroll">
                <ul class="header-cart-wrapitem w-full">
                    <!-- Hedef Kategorisi -->
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label for="target-select" class="header-cart-item-name m-b-18 hov-cl1 trans-04 d-block">
                                Hedef Kategorisi:
                            </label>
                            <select id="target-select" name="target_type" class="form-control m-b-10">
                                <option value="" selected>Kategori seçin...</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </li>

                    <!-- Hedef Bilgisi -->
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label for="target-select" class="header-cart-item-name m-b-18 hov-cl1 trans-04 d-block">
                                Hedef Tipi:
                            </label>
                            <select id="target-select" name="target_type" class="form-control m-b-10">
                                <option value="" selected>Hedef seçin...</option>
                                <option value="HARD">Pist</option>
                                <option value="SOFT">Radar</option>
                            </select>
                        </div>
                    </li>

                    <!-- Menzil Bilgisi -->
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label class="header-cart-item-name m-b-18 hov-cl1 trans-04 d-block">
                                Menzil:
                            </label>
                            <div class="m-b-10">
                                <div class="m-b-10">
                                    <label for="range-min" class="header-cart-item-name m-b-5 hov-cl1 trans-04">
                                        Min
                                    </label>
                                    <input type="number" id="range-min" name="min" class="form-control"
                                        placeholder="Min">
                                </div>
                                <div>
                                    <label for="range-max" class="header-cart-item-name m-b-5 hov-cl1 trans-04">
                                        Max
                                    </label>
                                    <input type="number" id="range-max" name="max" class="form-control"
                                        placeholder="Max">
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>

                <div class="w-full">
                    <button type="submit"
                        class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10">
                        Uygula
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
