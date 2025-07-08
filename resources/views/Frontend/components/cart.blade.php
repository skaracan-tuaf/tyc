<!--
    Platform -> F-16, TB-2, F-4, vb.
    Hedef Türü -> Ana Pist, Radar, Hangar, Sığınak, Komuta Kontrol Binası, vb.
        -> ACROSS'taki gibi her mühimmatın her hedefe karşı etkinlik değeri belirlenecek. (anket)
    Meteorolojik Durum -> Açık, Sisli, Yağmurlu, vb.
    Çevresel Hassasiyet -> Var, Yok
        Var -> Hedefin Okul ile mesafesi, Cami ile mesafesi, vb.
    İmha Türü -> Tam, Geçici
        -> Bunlara göre mühimmat ve uçak sayısı hesaplanacak
    Sonuç olarak -> SCL (Standart Configuration Load) çıkacak.

    Filtre uygulandığında:
        TOPSIS yöntemi uygulanacak.

-->
<style>
    .header-cart .form-control {
        width: 100%;
        min-width: 200px;
        max-width: 100%;
        box-sizing: border-box;
    }

    .header-cart-content {
        width: 100%;
        padding-right: 0;
        box-sizing: border-box;
    }

    .header-cart-item-txt,
    .header-cart-item {
        width: 100%;
    }

    .header-cart-item-txt>div {
        margin-bottom: 12px;
    }

    .header-cart-item-txt label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
    }

    .header-cart-item-txt select,
    .header-cart-item-txt input {
        width: 100%;
        box-sizing: border-box;
    }

    .ps {
        padding-right: 10px;
    }

    #sensitivity-container .btn-outline-danger {
        padding: 6px 0;
    }

    #sensitivity-container .row {
        margin-bottom: 10px;
    }

    #sensitivity-group label {
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
    }

    #sensitivity-container .form-control {
        width: 100%;
        max-width: 100%;
        border-radius: 6px;
        font-size: 14px;
        box-sizing: border-box;
    }
</style>

<div class="wrap-header-cart js-panel-cart">
    <div class="s-full js-hide-cart"></div>

    <div class="header-cart flex-col-l p-l-65 p-r-25">
        <div class="header-cart-title flex-w flex-sb-m p-b-8">
            <span class="mtext-103 cl2">Filtrele</span>
            <div class="fs-35 lh-10 cl2 p-lr-5 pointer hov-cl1 trans-04 js-hide-cart">
                <i class="zmdi zmdi-close"></i>
            </div>
        </div>

        <div class="header-cart-content flex-w js-pscroll">
            <form action="{{ route('Kiyasla') }}" method="GET" class="w-full">
                <ul class="header-cart-wrapitem w-full">

                    <!-- Hedef Kategorisi -->
                    <!--
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label class="header-cart-item-name">Hedef Kategorisi:</label>
                            <select name="target_type" class="form-control">
                                <option value="" selected>Kategori seçin...</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </li>
                    -->
                    <!-- Hedef Tipi -->
                    <!--
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label class="header-cart-item-name">Hedef Tipi:</label>
                            <select name="target_type_detail" class="form-control">
                                <option value="" selected>Hedef seçin...</option>
                                <option value="HARD">Pist</option>
                                <option value="SOFT">Radar</option>
                            </select>
                        </div>
                    </li>
                    -->

                    <!-- Hedef Türü -->
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label class="header-cart-item-name">Hedef:</label>
                            <select name="target_detail" class="form-control">
                                <option value="" selected>Hedef seçin...</option>
                                <option value="Ana Pist">Ana Pist</option>
                                <option value="Radar">Radar</option>
                                <option value="Hangar">Hangar</option>
                                <option value="Sığınak">Sığınak</option>
                                <option value="Komuta Kontrol Binası">Komuta Kontrol Binası</option>
                            </select>
                        </div>
                    </li>

                    <!-- Platform -->
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label class="header-cart-item-name">Platform:</label>
                            <select name="platform" class="form-control">
                                <option value="" selected>Platform seçin...</option>
                                <option value="F-16">F-16</option>
                                <option value="TB-2">TB-2</option>
                                <option value="F-4">F-4</option>
                            </select>
                        </div>
                    </li>

                    <!-- Meteorolojik Durum -->
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label class="header-cart-item-name">Meteorolojik Durum:</label>
                            <select name="weather" class="form-control">
                                <option value="" selected>Durum seçin...</option>
                                <option value="Açık">Açık</option>
                                <option value="Sisli">Sisli</option>
                                <option value="Yağmurlu">Yağmurlu</option>
                            </select>
                        </div>
                    </li>

                    <!-- Çevresel Hassasiyet -->
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label class="header-cart-item-name">Çevresel Hassasiyet:</label>
                            <select name="env_sensitive" class="form-control" onchange="toggleSensitivityGroup(this)">
                                <option value="" selected>Seçin...</option>
                                <option value="Yok">Yok</option>
                                <option value="Var">Var</option>
                            </select>
                        </div>
                    </li>

                    <!-- Hassas Noktalar -->
                    <li id="sensitivity-group" class="header-cart-item flex-w flex-t m-b-12" style="display: none;">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label class="header-cart-item-name">Hassas Noktalar:</label>
                            <div id="sensitivity-container"></div>
                            <button type="button" class="btn btn-sm btn-secondary m-t-10"
                                onclick="addSensitivityRow()">+ Yeni Hassas Nokta</button>
                        </div>
                    </li>

                    <!-- İmha Türü -->
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label class="header-cart-item-name">İmha Türü:</label>
                            <select name="destruction_type" class="form-control"
                                onchange="toggleDestructionPercentage(this)">
                                <option value="" selected>İmha türü seçin...</option>
                                <option value="Tam">Tam</option>
                                <option value="Geçici">Geçici</option>
                            </select>
                            <div id="destruction-percentage-container" class="m-t-10" style="display:none;">
                                <label class="header-cart-item-name">Geçici Etkisizleştirme Oranı (%):</label>
                                <input type="number" name="destruction_percentage" class="form-control"
                                    placeholder="örn. 75" min="1" max="100">
                            </div>
                        </div>
                    </li>

                    <!-- Menzil -->
                    <!--
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label class="header-cart-item-name">Menzil:</label>
                            <label class="header-cart-item-name m-b-5">Min</label>
                            <input type="number" name="min" class="form-control" placeholder="Min">
                            <label class="header-cart-item-name m-b-5 m-t-10">Max</label>
                            <input type="number" name="max" class="form-control" placeholder="Max">
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
    let sensitivityCount = 0;

    function toggleSensitivityGroup(select) {
        const group = document.getElementById('sensitivity-group');
        const container = document.getElementById('sensitivity-container');

        if (select.value === 'Var') {
            group.style.display = 'block';
            if (sensitivityCount === 0) addSensitivityRow();
        } else {
            group.style.display = 'none';
            container.innerHTML = '';
            sensitivityCount = 0;
        }
    }

    function addSensitivityRow() {
        const container = document.getElementById('sensitivity-container');
        const row = document.createElement('div');
        row.className = 'row align-items-center g-2 m-b-2';
        row.innerHTML = `
            <div class="col-md-5 col-12">
                <input type="text" name="sensitivity_name[]" class="form-control" placeholder="Hassas nokta adı (örn: Okul)" required>
            </div>
            <div class="col-md-5 col-12">
                <input type="number" name="sensitivity_distance[]" class="form-control" placeholder="Mesafe (m)" required>
            </div>
            <div class="col-md-2 col-12 text-end">
                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeSensitivityRow(this)">
                    <i class="zmdi zmdi-delete"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
        sensitivityCount++;
    }

    function removeSensitivityRow(button) {
        const container = document.getElementById('sensitivity-container');
        if (container.children.length > 1) {
            button.closest('.row').remove();
            sensitivityCount--;
        } else {
            alert("En az bir hassasiyet alanı kalmalı.");
        }
    }

    function toggleDestructionPercentage(select) {
        const container = document.getElementById('destruction-percentage-container');
        container.style.display = (select.value === 'Geçici') ? 'block' : 'none';
    }
</script>
