@extends('Backend.index')

@section('title', 'Kategori')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/static/js/cropper/cropper.min.css') }}">
@endsection

@section('page-title', 'Kategoriler')
@section('page-subtitle', 'Mühimmat Kategorisi Ekle')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('kategori.index') }}">Kategoriler</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ isset($category) ? 'Güncelle' : 'Ekle' }}</li>
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
                                action="{{ isset($category) ? route('kategori.update', $category->id) : route('kategori.store') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @if (isset($category))
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <label for="category-name" class="form-label">Kategori Adı</label>
                                            <input type="text" id="category-name" class="form-control"
                                                placeholder="Kategori Adı" name="name"
                                                value="{{ isset($category) ? $category->name : '' }}" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <label for="parent-category" class="form-label">Üst Kategori</label>
                                            <fieldset class="form-group">
                                                <select class="form-select" name="parent" id="parent-category">
                                                    <option value="">Üst Kategori</option>
                                                    @foreach ($categories as $cat)
                                                        @php
                                                            $catName = $cat->name;
                                                            $parentCategories = [];

                                                            // Kategorinin üst kategorilerini bul ve isimlerini bir diziye ekle
                                                            $currentCategory = $cat;
                                                            while ($currentCategory->parent) {
                                                                array_unshift($parentCategories, $currentCategory->parent->name);
                                                                $currentCategory = $currentCategory->parent;
                                                            }

                                                            // Kategori ismini, üst kategorilerle birleştir
                                                            if (!empty($parentCategories)) {
                                                                $catName = implode('->', $parentCategories) . '->' . $cat->name;
                                                            }
                                                        @endphp
                                                        @if (!isset($category) || (isset($category) && $category->id != $cat->id))
                                                            <option value="{{ $cat->id }}" {{ isset($category) && $category->parent_id == $cat->id ? 'selected' : '' }}>
                                                                {{ $catName }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <label for="category-image" class="form-label">Resim</label>

                                            <div id="resimEkleAlani">
                                                <img id="image" src="" alt="">
                                                <input type="hidden" id="croppedImage" name="croppedImage" required>
                                                @if (isset($category) && !empty($category->image))
                                                    <div id="previewImage" class="image-container"
                                                        style="display: flex; justify-content: center; align-items: center;">
                                                        <img src="{{ asset('storage/' . $category->image) }}"
                                                            alt="" class="img-fluid mt-1">
                                                    </div>
                                                @endif
                                                <div class="img-container mt-3" id="previewContainer"
                                                    style="display: flex; justify-content: center; align-items: center;">
                                                </div>
                                                <input type="file" class="form-control" id="imageInput"
                                                    name="categoryImage" accept="image/*"
                                                    {{ isset($category) ? '' : 'required' }}>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="category-description" class="form-label">Açıklama</label>
                                            <textarea class="form-control" id="category-description" rows="3" name="description" placeholder="Açıklama"
                                                required>{{ isset($category) ? $category->description : '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group mandatory">
                                            <fieldset>
                                                <label class="form-label"> Durum </label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status"
                                                        id="category-status1" value="1" required
                                                        @if (!isset($category) || (isset($category) && $category->status)) checked @endif />
                                                    <label class="form-check-label form-label" for="category-status1">
                                                        Aktif
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status"
                                                        id="category-status2" value="0" required
                                                        @if (isset($category) && !$category->status) checked @endif />
                                                    <label class="form-check-label form-label" for="category-status2">
                                                        Pasif
                                                    </label>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary me-1 mb-1">
                                            {{ isset($category) ? 'Güncelle' : 'Ekle' }}
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
            let imageInput = document.getElementById('imageInput');
            let image = document.getElementById('image');
            let previewContainer = document.getElementById('previewContainer');
            let croppedImageInput = document.getElementById('croppedImage');
            let previewImage = document.getElementById('previewImage');

            let cropper;

            const _aspectRatio = 16 / 9; // width="273" height="376"

            imageInput.addEventListener('change', function() {
                let file = this.files[0];

                if (file) {
                    let reader = new FileReader();

                    reader.onload = function(e) {
                        // Eğer önceki cropper nesnesi varsa yok et
                        if (cropper) {
                            cropper.destroy();
                        }

                        if (previewImage) {
                            previewImage.remove();
                        }

                        // PreviewContainer'ı temizle
                        previewContainer.innerHTML = '';

                        // Resmi göster
                        image.src = e.target.result;
                        image.style.display = 'block';

                        // Cropper nesnesini oluştur
                        cropper = new Cropper(image, {
                            aspectRatio: _aspectRatio,
                            viewMode: 1,
                            autoCropArea: 1,
                            responsive: true
                        });
                    };

                    reader.readAsDataURL(file);
                }
            });

            // Form submit event'i
            let form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                event.preventDefault();

                // Eğer cropper nesnesi varsa kırpılmış resmi al
                if (cropper) {
                    let croppedDataUrl = cropper.getCroppedCanvas().toDataURL('image/jpeg');
                    croppedImageInput.value = croppedDataUrl;
                }

                // Formu gönder
                this.submit();
            });
        });
    </script>
@endsection
