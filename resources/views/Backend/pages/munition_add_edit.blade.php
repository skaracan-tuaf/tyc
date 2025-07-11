@extends('Backend.index')

@section('title', '| Mühimmatlar')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/static/js/cropper/cropper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/choices.js/public/assets/styles/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/sweetalert2/sweetalert2.min.css') }}" />
@endsection

@section('page-title', 'Mühimmatlar')
@section('page-subtitle', 'Mühimmat Ekle')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('muhimmat.index') }}">Mühimmatlar</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ isset($munition) ? 'Güncelle' : 'Ekle' }}</li>
@endsection

@section('content')
    <!-- // Basic multiple Column Form section start -->
    <section id="multiple-column-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Kategori Ekle</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form"
                                action="{{ isset($munition) ? route('muhimmat.update', $munition->id) : route('muhimmat.store') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @if (isset($munition))
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <label for="munition-name" class="form-label">Mühimmat Adı</label>
                                            <input type="text" id="munition-name" class="form-control"
                                                placeholder="Mühimmat Adı" name="name"
                                                value="{{ isset($munition) ? $munition->name : '' }}" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <div class="row">
                                                <div class="col-6">
                                                    <label for="category" class="form-label">Kategori</label>
                                                </div>
                                                <div class="col-6 text-end">
                                                    <a href="{{ route('kategori.create') }}">Yeni kategori
                                                        ekle</a>
                                                </div>
                                            </div>
                                            <fieldset class="form-group">
                                                <select class="form-select" name="category_id" id="category" required>
                                                    @if ($categories->isEmpty())
                                                        <option value="" selected disabled>Kategori bulunamadı! Lütfen
                                                            kategori ekleyin.</option>
                                                    @else
                                                        @php
                                                            $selectedCategoryId = isset($munition)
                                                                ? optional($munition->category)->id
                                                                : null;
                                                        @endphp
                                                        <option value="" selected disabled>Kategori</option>
                                                        @foreach ($categories as $cat)
                                                            @php
                                                                $categoryName = $cat->name;
                                                                $parentCategories = [];

                                                                // Kategorinin üst kategorilerini bul ve isimlerini bir diziye ekle
                                                                $currentCategory = $cat;
                                                                while ($currentCategory->parent) {
                                                                    array_unshift(
                                                                        $parentCategories,
                                                                        $currentCategory->parent->name,
                                                                    );
                                                                    $currentCategory = $currentCategory->parent;
                                                                }

                                                                // Kategori ismini, üst kategorilerle birleştir
                                                                if (!empty($parentCategories)) {
                                                                    $categoryName =
                                                                        implode('->', $parentCategories) .
                                                                        '->' .
                                                                        $cat->name;
                                                                }
                                                            @endphp
                                                            <option value="{{ $cat->id }}"
                                                                {{ $selectedCategoryId == $cat->id ? 'selected' : '' }}>
                                                                {{ $categoryName }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </fieldset>

                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <label for="munition-price" class="form-label">Mühimmat Fiyatı </label>
                                            <div class="input-group">
                                                <span class="input-group-text">$<!--&#8378;--></span>
                                                <span class="input-group-text">0.00</span>
                                                <input type="number" min="0" step="0.01" id="munition-price"
                                                    class="form-control" name="price"
                                                    placeholder="Mühimmat fiyatını girin"
                                                    value="{{ isset($munition) ? $munition->price : '' }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $countries = [
                                            'TR' => 'Türkiye',
                                            'US' => 'A.B.D',
                                            'DE' => 'Almanya',
                                            'FR' => 'Fransa',
                                            'JP' => 'Japonya',
                                            'CN' => 'Çin',
                                            'IN' => 'Hindistan',
                                            'IL' => 'İsrail',
                                            'RU' => 'Rusya',
                                            'UA' => 'Ukrayna',
                                            'BR' => 'Brezilya',
                                            'GB' => 'İngiltere',
                                            'IT' => 'İtalya',
                                            'ES' => 'İspanya',
                                            'CA' => 'Kanada',
                                            'AU' => 'Avustralya',
                                            'NL' => 'Hollanda',
                                            'CH' => 'İsviçre',
                                            'SG' => 'Singapur',
                                            'SE' => 'İsveç',
                                            'BE' => 'Belçika',
                                            'AT' => 'Avusturya',
                                            'KR' => 'Güney Kore',
                                        ];
                                    @endphp

                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <label for="munition-origin" class="form-label">Ülke</label>
                                            <fieldset class="form-group">
                                                <select class="form-select" name="origin" id="munition-origin" required>
                                                    <option value="">Seçiniz...</option>
                                                    @foreach ($countries as $code => $name)
                                                        <option value="{{ $code }}"
                                                            {{ isset($munition) && $munition->origin == $code ? 'selected' : '' }}>
                                                            {{ $name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <label for="target-type" class="form-label">Hedef Tipi</label>
                                            <fieldset class="form-group">
                                                <select class="form-select" name="target_type" id="target-type" required>
                                                    <option value="">Seçiniz...</option>
                                                    <option value="SOFT"
                                                        {{ isset($munition) && $munition->target_type == 'SOFT' ? 'selected' : '' }}>
                                                        SOFT</option>
                                                    <option value="HARD"
                                                        {{ isset($munition) && $munition->target_type == 'HARD' ? 'selected' : '' }}>
                                                        HARD</option>
                                                </select>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <!--
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group mandatory">
                                                                <label for="munition-score" class="form-label">Puan</label>
                                                                <input type="number" id="munition-score" class="form-control mr-2"
                                                                    name="score" placeholder="Puan" min="1" max="10"
                                                                    value="{{ isset($munition) ? $munition->score : '' }}">
                                                            </div>
                                                        </div>
                                                    -->

                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <label for="munition-platforms" class="form-label">Kullanılabilir
                                                Platformlar</label>
                                            <select class="choices form-select multiple-remove" name="platforms[]"
                                                id="munition-platforms" multiple>
                                                @foreach ($platforms as $platform)
                                                    <option value="{{ $platform->id }}"
                                                        {{ isset($munition) && $munition->platforms->contains('id', $platform->id) ? 'selected' : '' }}>
                                                        {{ $platform->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Birden fazla platform seçebilirsiniz.</small>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <h5>Meteorolojik Performanslar (0-100)</h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="clear_weather_performance" class="form-label">Açık
                                                    Hava</label>
                                                <input type="number" name="clear_weather_performance"
                                                    id="clear_weather_performance" class="form-control" min="0"
                                                    max="100" placeholder="0-100"
                                                    value="{{ old('clear_weather_performance', $munition->clear_weather_performance ?? '') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="fog_weather_performance" class="form-label">Sisli Hava</label>
                                                <input type="number" name="fog_weather_performance"
                                                    id="fog_weather_performance" class="form-control" min="0"
                                                    max="100" placeholder="0-100"
                                                    value="{{ old('fog_weather_performance', $munition->fog_weather_performance ?? '') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="rain_weather_performance" class="form-label">Yağmurlu
                                                    Hava</label>
                                                <input type="number" name="rain_weather_performance"
                                                    id="rain_weather_performance" class="form-control" min="0"
                                                    max="100" placeholder="0-100"
                                                    value="{{ old('rain_weather_performance', $munition->rain_weather_performance ?? '') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="munition-summary" class="form-label">Özet</label>
                                            <textarea class="form-control" id="munition-summary" rows="3" name="summary" placeholder="Özet">{{ isset($munition) ? $munition->summary : '' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="munition-description" class="form-label">Açıklama</label>
                                            <textarea class="form-control" id="munition-description" rows="6" name="description" placeholder="Açıklama">{{ isset($munition) ? $munition->description : '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table" style="display: none;">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Varyantlar</th>
                                                    <th>Fiyat</th>
                                                    <th>Stok</th>
                                                    <th>Durum</th>
                                                </tr>
                                            </thead>
                                            <tbody id="combinations-table-body">
                                                <!-- Bu kısım JavaScript ile oluşturulacak -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <hr>

                                <br>

                                <div class="row">
                                    @foreach ($attributes as $attribute)
                                        <div class="form-group col-12 row mb-3">
                                            <!-- Attribute Input -->
                                            <div class="col-6">
                                                <label for="{{ $attribute->slug }}">{{ $attribute->name }}:</label>
                                                @php
                                                    $pivot = isset($munition)
                                                        ? $munition->attributes->firstWhere('id', $attribute->id)
                                                                ->pivot ?? null
                                                        : null;
                                                @endphp
                                                @if ($attribute->option === 'Yazı')
                                                    <input type="text" id="{{ $attribute->slug }}"
                                                        name="attributes[{{ $attribute->id }}][value]"
                                                        value="{{ $pivot ? $pivot->value : '' }}" class="form-control">
                                                @elseif($attribute->option === 'Tam Sayı')
                                                    <input type="text" id="{{ $attribute->slug }}"
                                                        name="attributes[{{ $attribute->id }}][value]"
                                                        value="{{ $pivot ? $pivot->value : '' }}" class="form-control">
                                                @elseif($attribute->option === 'Ondalık')
                                                    <input type="number" id="{{ $attribute->slug }}"
                                                        name="attributes[{{ $attribute->id }}][value]"
                                                        value="{{ $pivot ? $pivot->value : '' }}" class="form-control"
                                                        step="0.01">
                                                @elseif($attribute->option === 'Doğrulama')
                                                    <div class="form-check">
                                                        <input type="radio" id="{{ $attribute->slug }}_1"
                                                            name="attributes[{{ $attribute->id }}][value]" value="1"
                                                            class="form-check-input"
                                                            {{ $pivot && $pivot->value == 1 ? 'checked' : '' }}>
                                                        <label for="{{ $attribute->slug }}_1"
                                                            class="form-check-label">Var</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="radio" id="{{ $attribute->slug }}_0"
                                                            name="attributes[{{ $attribute->id }}][value]" value="0"
                                                            class="form-check-input"
                                                            {{ $pivot && $pivot->value == 0 ? 'checked' : '' }}>
                                                        <label for="{{ $attribute->slug }}_0"
                                                            class="form-check-label">Yok</label>
                                                    </div>
                                                @elseif($attribute->option === 'Aralık')
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col">
                                                                <input type="number" id="{{ $attribute->slug }}_min"
                                                                    name="attributes[{{ $attribute->id }}][min]"
                                                                    value="{{ $pivot ? $pivot->min : '' }}"
                                                                    class="form-control" placeholder="asgari"
                                                                    step="0.01">
                                                            </div>
                                                            <div class="col">
                                                                <input type="number" id="{{ $attribute->slug }}_max"
                                                                    name="attributes[{{ $attribute->id }}][max]"
                                                                    value="{{ $pivot ? $pivot->max : '' }}"
                                                                    class="form-control" placeholder="azami"
                                                                    step="0.01">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif($attribute->option === 'Renk')
                                                    <input type="color" id="{{ $attribute->slug }}"
                                                        name="attributes[{{ $attribute->id }}][value]"
                                                        value="{{ $pivot ? $pivot->value : '' }}" class="form-control">
                                                @elseif($attribute->option === 'Resim')
                                                    <input type="file" id="{{ $attribute->slug }}"
                                                        name="attributes[{{ $attribute->id }}]"
                                                        class="form-control-file">
                                                @elseif($attribute->option === 'Buton')
                                                    <button type="button" id="{{ $attribute->slug }}"
                                                        name="attributes[{{ $attribute->id }}]"
                                                        class="btn btn-primary">{{ $attribute->name }}</button>
                                                @elseif ($attribute->option === 'Liste')
                                                    <select id="{{ $attribute->slug }}"
                                                        name="attributes[{{ $attribute->id }}][value]"
                                                        class="form-control">
                                                        <option value="">Seçiniz</option>
                                                        @if ($attribute->listValues)
                                                            @foreach ($attribute->listValues as $value)
                                                                <option value="{{ $value->id }}"
                                                                    {{ $pivot && $pivot->value == $value->id ? 'selected' : '' }}>
                                                                    {{ $value->value }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                @endif
                                            </div>
                                            <!-- Score Input -->
                                            <div class="col-6">
                                                <label for="score_{{ $attribute->slug }}">Puan:</label>
                                                <input type="number" id="score_{{ $attribute->slug }}" step="0.01"
                                                    name="attributes[{{ $attribute->id }}][score]"
                                                    value="{{ $pivot ? $pivot->score : '' }}" class="form-control"
                                                    step="1">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>



                                <hr>
                                <br>

                                <div class="row" id="resimEkleAlani">
                                    <div class="col-12">
                                        <div class="form-group row align-items-center">
                                            <label class="col-2 col-form-label" for="first-name">En / Boy Oranı</label>
                                            <div class="col-10 d-flex align-items-center">
                                                <input type="number" id="input-width" class="form-control mr-2"
                                                    name="iwidth" placeholder="Genişlik" min="1" value="730">
                                                <span class="text-center mr-2">&nbsp;/&nbsp;</span>
                                                <input type="number" id="input-length" class="form-control ml-2"
                                                    name="ilength" placeholder="Yükseklik" min="1"
                                                    value="410">
                                                <div class="d-flex justify-content-end">
                                                    &nbsp;<a href="#" class="btn btn-outline-primary">+</a>
                                                    &nbsp;<a href="#" class="btn btn-outline-primary">-</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @for ($i = 1; $i <= 6; $i++)
                                        <div class="col-12 col-xl-6 col-lg-6 col-md-6">
                                            <div class="form-group text-left">
                                                <label for="imageInput{{ $i }}">Resim {{ $i }}<span
                                                        style="color:red;"> *</span></label>
                                                <img id="image{{ $i }}" src="" alt=""
                                                    class="img-fluid mt-3" style="max-width: 100%; height: auto;">
                                                <input type="hidden" id="croppedImage{{ $i }}"
                                                    name="croppedImage{{ $i }}" required>
                                                <div class="img-container mt-1 mb-3"
                                                    id="previewContainer{{ $i }}"
                                                    style="max-width: 100%; height: auto; overflow: hidden;">
                                                    @if (isset($munition) && count($munition->images) >= $i && isset($munition->images[$i - 1]))
                                                        <div id="previewImage{{ $i }}" class="image-container"
                                                            style="position: relative; display: inline-block;">
                                                            <img src="{{ asset('storage/' . $munition->images[$i - 1]->url) }}"
                                                                alt="" class="img-fluid"
                                                                style="max-width: 100%; height: auto;">
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                                                onclick="deleteImage({{ $munition->images[$i - 1]->id }}, '{{ $i }}')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                                <input type="file" class="form-control"
                                                    id="imageInput{{ $i }}"
                                                    name="imageInput{{ $i }}" accept="image/*">
                                                <br>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group mandatory">
                                            <fieldset>
                                                <label class="form-label"> Durum </label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status"
                                                        id="munition-status1" value="1" required
                                                        @if (!isset($munition) || (isset($munition) && $munition->status)) checked @endif />
                                                    <label class="form-check-label form-label" for="munition-status1">
                                                        Aktif
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status"
                                                        id="munition-status2" value="0" required
                                                        @if (isset($munition) && !$munition->status) checked @endif />
                                                    <label class="form-check-label form-label" for="munition-status2">
                                                        Pasif
                                                    </label>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary me-1 mb-1">
                                            {{ isset($munition) ? 'Güncelle' : 'Ekle' }}
                                        </button>
                                        <button type="reset" class="btn btn-light-secondary me-1 mb-1">
                                            Temizle
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- // Basic multiple Column Form section end -->
@endsection

@section('scripts')
    <script src="{{ asset('backend_assets/extensions/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('backend_assets/extensions/choices.js/public/assets/scripts/choices.js') }}"></script>
    <script src="{{ asset('backend_assets/static/js/pages/form-element-select.js') }}"></script>
    <script src="{{ asset('backend_assets/static/js/cropper/cropper.min.js') }}"></script>
    <script src="{{ asset('backend_assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        function deleteImage(imageId, previewId) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: 'Bu resmi silmek istediğinizden emin misiniz?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Evet, sil!',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kullanıcı "Evet, sil!" butonuna tıkladı
                    // AJAX isteği göndererek resmi sil
                    fetch(`/yonetim/muhimmat/images/${imageId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                        })
                        .then(response => {
                            if (response.ok) {
                                // Resmi HTML'den kaldır
                                document.getElementById('previewImage' + previewId).remove();
                                // Başarı mesajı göster
                                Swal.fire(
                                    'Silindi!',
                                    'Resim başarıyla silindi.',
                                    'success'
                                );
                            } else {
                                // Hata mesajı göster
                                Swal.fire(
                                    'Hata!',
                                    'Resim silinirken bir hata oluştu.',
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            // Hata mesajı göster
                            Swal.fire(
                                'Hata!',
                                'Resim silinirken bir hata oluştu.',
                                'error'
                            );
                        });
                }
            });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let croppers = []; // Cropper nesnelerini saklayacak dizi

            for (let i = 1; i <= 6; i++) {
                let imageInput = document.getElementById('imageInput' + i);
                let image = document.getElementById('image' + i);
                let previewContainer = document.getElementById('previewContainer' + i);
                let croppedImageInput = document.getElementById('croppedImage' + i);
                let previewImage = document.getElementById('previewImage' + i);
                let cropper;

                imageInput.addEventListener('change', function() {
                    let file = this.files[0];
                    if (file) {
                        let reader = new FileReader();

                        reader.onload = function(e) {
                            if (cropper) {
                                cropper.destroy();
                            }

                            if (previewImage) {
                                previewImage.remove();
                            }

                            previewContainer.innerHTML = '';

                            image.src = e.target.result;
                            image.style.display = 'block';

                            cropper = new Cropper(image, {
                                aspectRatio: parseInt(document.getElementById('input-width')
                                    .value) / parseInt(document.getElementById(
                                        'input-length')
                                    .value), ///_aspectRatio,
                                viewMode: 1,
                                autoCropArea: 1,
                                responsive: true
                            });

                            // Cropper nesnesini diziye ekle
                            croppers[i - 1] = cropper;

                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            let form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                event.preventDefault();

                for (let i = 1; i <= 6; i++) {
                    let cropper = croppers[i - 1];

                    if (cropper) {
                        let croppedDataUrl = cropper.getCroppedCanvas().toDataURL('image/jpeg');
                        let croppedImageInput = document.getElementById('croppedImage' + i);
                        croppedImageInput.value = croppedDataUrl;
                    } else {
                        console.log("cropper'da hata var.");
                    }
                }

                this.submit();
            });
        });
    </script>
@endsection
