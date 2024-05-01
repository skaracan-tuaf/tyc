@extends('Backend.index')

@section('title', 'Mühimmatlar')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/static/js/cropper/cropper.min.css') }}">
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
                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <label for="munition-origin" class="form-label">Ülke</label>
                                            <fieldset class="form-group">
                                                @php
                                                    // Ülkelerin kodlarını ve isimlerini içeren bir dizi oluştur
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
                                        <div class="form-group">
                                            <label for="munition-summary" class="form-label">Özet</label>
                                            <textarea class="form-control" id="munition-summary" rows="3" name="summary" placeholder="Özet" required>{{ isset($munition) ? $munition->summary : '' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="munition-description" class="form-label">Açıklama</label>
                                            <textarea class="form-control" id="munition-description" rows="6" name="description" placeholder="Açıklama"
                                                required>{{ isset($munition) ? $munition->description : '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="resimEkleAlani">
                                    @for ($i = 1; $i <= 6; $i++)
                                        <div class="col-12 col-xl-6">
                                            <div class="form-group">
                                                <label for="imageInput{{ $i }}">Resim
                                                    {{ $i }}</label>
                                                <img id="image{{ $i }}" src="" alt=""
                                                    class="img-fluid mt-3" style="max-width: 100%; height: auto;">
                                                <input type="hidden" id="imagePath{{ $i }}"
                                                    name="imagePath{{ $i }}">
                                                @if (isset($munition) && isset($munition->images[$i - 1]))
                                                    <img src="{{ asset('storage/' . $munition->images[$i - 1]->url) }}"
                                                        alt="" class="img-fluid mt-3"
                                                        style="max-width: 100%; height: auto;">
                                                @endif
                                                <input type="file" class="form-control"
                                                    id="imageInput{{ $i }}"
                                                    name="munitionImage{{ $i }}" accept="image/*">
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

    <script src="{{ asset('backend_assets/static/js/cropper/cropper.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const aspectRatio = 9 / 16;

            for (let i = 1; i <= 6; i++) {
                let imageInput = document.getElementById('imageInput' + i);
                let image = document.getElementById('image' + i);

                imageInput.addEventListener('change', function() {
                    let file = this.files[0];
                    if (file) {
                        let reader = new FileReader();
                        reader.onload = function(e) {
                            image.src = e.target.result;
                            image.style.display = 'block';

                            // Initialize Cropper for the image
                            let cropper = new Cropper(image, {
                                aspectRatio: aspectRatio,
                                viewMode: 1,
                                autoCropArea: 1,
                                responsive: true
                            });
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>

@endsection
