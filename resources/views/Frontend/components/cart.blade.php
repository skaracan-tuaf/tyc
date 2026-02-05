<div class="wrap-header-cart js-panel-cart">
    <div class="s-full js-hide-cart"></div>
    <div class="header-cart flex-col-l p-t-22 p-b-25 p-l-65 p-r-25">
        <div class="header-cart-title flex-w flex-sb-m p-b-8">
            <span class="mtext-103 cl2">Filtrele</span>
            <div class="fs-35 lh-10 cl2 p-lr-5 pointer hov-cl1 trans-04 js-hide-cart">
                <i class="zmdi zmdi-close"></i>
            </div>
        </div>

        <div class="header-cart-content flex-w w-full js-pscroll">
            <form action="{{ route('sonuclariGoster') }}" method="POST" class="w-full">
                @csrf
                <ul class="header-cart-wrapitem w-full">

                    <!-- Platform -->
                    <!--
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label class="header-cart-item-name m-b-18 hov-cl1 trans-04 d-block">Platform:</label>
                            <select name="platform" class="form-control m-b-10">
                                <option value="" disabled selected>Seçin</option>
                                @foreach ($platforms as $platform)
                                    <option value="{{ $platform->name }}">{{ $platform->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </li>
                    -->

                    <!-- Kategori -->
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label class="header-cart-item-name m-b-18 hov-cl1 trans-04 d-block">Kategori:</label>
                            <select name="category_id" class="form-control m-b-10">
                                <option value="" disabled selected>Seçin</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </li>

                    <!-- Hedef -->
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label class="header-cart-item-name m-b-18 hov-cl1 trans-04 d-block">Hedef:</label>
                            <select name="target_type" class="form-control m-b-10">
                                <option value="" disabled selected>Seçin</option>
                                @foreach ($targets as $target)
                                    <option value="{{ $target->id }}">{{ $target->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </li>

                    <!-- Meteorolojik Durum -->
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label class="header-cart-item-name m-b-18 hov-cl1 trans-04 d-block">Meteorolojik
                                Durum:</label>
                            <select name="weather" class="form-control m-b-10">
                                <option value="" disabled selected>Seçin</option>
                                <option>Açık</option>
                                <option>Sisli</option>
                                <option>Yağmurlu</option>
                            </select>
                        </div>
                    </li>

                    <!-- İmha Türü -->
                    <!--
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label class="header-cart-item-name m-b-18 hov-cl1 trans-04 d-block">İmha Türü:</label>
                            <select id="destruction-type" name="destruction_type" class="form-control m-b-10"
                                onchange="togglePercentageInput(this)">
                                <option value="" disabled selected>Seçin</option>
                                <option value="tam">Tam</option>
                                <option value="gecici">Geçici</option>
                            </select>
                            <div id="percentage-container" style="display:none;" class="m-t-10">
                                <label>Geçici İmha Yüzdesi (%):</label>
                                <input type="number" name="destruction_percentage" min="0" max="100"
                                    class="form-control" placeholder="örn: 70">
                            </div>
                        </div>
                    </li>
                    -->

                    <!-- Çevresel Hassasiyet -->
                    <!--
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label class="header-cart-item-name m-b-18 hov-cl1 trans-04 d-block">Çevresel
                                Hassasiyet:</label>
                            <select name="env_sensitivity" class="form-control m-b-10" onchange="toggleEnvInputs(this)">
                                <option value="" disabled selected>Seçin</option>
                                <option value="yok">Yok</option>
                                <option value="var">Var</option>
                            </select>

                            <div id="env-details" style="display:none;" class="m-t-10">
                                <div id="env-group-list">
                                    <div class="env-group d-flex align-items-center m-b-10">
                                        <input type="text" name="env_name[]" class="form-control me-2"
                                            placeholder="Örn: Cami">
                                        <input type="number" name="env_distance[]" class="form-control me-2"
                                            placeholder="Mesafe (m)">
                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="removeThisGroup(this)">-</button>
                                    </div>
                                </div>

                                <div class="m-t-10">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="addEnvGroup()">+</button>
                                </div>
                            </div>
                        </div>
                    </li>
                    -->
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

<script>
    function togglePercentageInput(select) {
        const container = document.getElementById('percentage-container');
        container.style.display = (select.value === 'gecici') ? 'block' : 'none';
    }

    function toggleEnvInputs(select) {
        const envDetails = document.getElementById('env-details');
        envDetails.style.display = (select.value === 'var') ? 'block' : 'none';
    }

    function addEnvGroup() {
        const container = document.getElementById('env-group-list');
        const group = document.createElement('div');
        group.className = 'env-group d-flex align-items-center m-b-10';

        group.innerHTML = `
            <input type="text" name="env_name[]" class="form-control me-2" placeholder="Örn: Okul">
            <input type="number" name="env_distance[]" class="form-control me-2" placeholder="Mesafe (m)">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeThisGroup(this)">-</button>
        `;

        container.appendChild(group);
        updateGroupBorders();
    }

    function removeThisGroup(button) {
        const group = button.closest('.env-group');
        const container = document.getElementById('env-group-list');
        if (container.children.length > 1) {
            container.removeChild(group);
            updateGroupBorders();
        }
    }

    function updateGroupBorders() {
        const groups = document.querySelectorAll('#env-group-list .env-group');
        groups.forEach((group, index) => {
            if (groups.length === 1) {
                group.style.borderBottom = 'none';
                group.style.paddingBottom = '0';
                group.style.marginBottom = '10px';
            } else {
                group.style.borderBottom = (index < groups.length - 1) ? '1px solid #ccc' : 'none';
                group.style.paddingBottom = (index < groups.length - 1) ? '10px' : '0';
                group.style.marginBottom = '10px';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateGroupBorders(); // Sayfa yüklendiğinde ilk grubu çizgisiz yap
    });
</script>
