@extends('Backend.index')

@section('title', 'Makale')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/static/js/cropper/cropper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/summernote/summernote-lite.css') }}">

    <link rel="stylesheet" href="{{ asset('backend_assets/compiled/css/form-editor-summernote.css') }}">

    <style>
        .note-editor.note-frame .note-editing-area .note-editable {
            background-color: #ffffff;
        }
    </style>
@endsection

@section('page-title', 'Makaleler')
@section('page-subtitle', 'Makale Ekle')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('makale.index') }}">Makaleler</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ isset($post) ? 'Güncelle' : 'Ekle' }}</li>
@endsection

@section('content')
    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <form class="form"
                        action="{{ isset($post) ? route('makale.update', $post->id) : route('makale.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if (isset($post))
                            @method('PUT')
                        @endif
                        <div class="card-header">
                            <label for="previewImage" class="form-label">Resim</label>
                            <div id="resimEkleAlani">
                                <img id="image" src="" alt="">
                                <input type="hidden" id="croppedImage" name="croppedImage" required>
                                @if (isset($post) && !empty($post->image))
                                    <div id="previewImage" class="image-container">
                                        <img src="{{ asset('storage/' . $post->image) }}" alt=""
                                            class="img-fluid mt-1">
                                    </div>
                                @endif
                                <div class="img-container mt-3" id="previewContainer"></div>
                                <input type="file" class="form-control" id="imageInput" name="postImage" accept="image/*"
                                    {{ isset($post) ? '' : 'required' }}>
                            </div>
                        </div>
                        <div class="card-body">
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
                                        <option value="" selected disabled>Kategori bulunamadı! Lütfen kategori
                                            ekleyin.</option>
                                    @else
                                        @php
                                            $selectedCategoryId = isset($post) ? optional($post->category)->id : null;
                                        @endphp
                                        <option value="" selected disabled>Kategori</option>
                                        @foreach ($categories as $cat)
                                            @php
                                                $categoryName = $cat->name;
                                                $parentCategories = [];

                                                $currentCategory = $cat;
                                                while ($currentCategory->parent) {
                                                    array_unshift($parentCategories, $currentCategory->parent->name);
                                                    $currentCategory = $currentCategory->parent;
                                                }

                                                if (!empty($parentCategories)) {
                                                    $categoryName =
                                                        implode('->', $parentCategories) . '->' . $cat->name;
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
                        <div class="card-body">
                            <label for="post-title" class="form-label">Başlık</label>
                            <input type="text" id="post-title" class="form-control" placeholder="Makale Başlığı"
                                name="title" value="{{ isset($post) ? $post->title : '' }}" required />
                        </div>
                        <div class="card-body">
                            <label for="post-summary" class="form-label">Özet</label>
                            <textarea class="form-control" id="post-summary" rows="3" name="summary" placeholder="Özet" required>{{ isset($post) ? $post->summary : '' }}</textarea>
                        </div>
                        <div class="card-body">
                            <label for="summernote" class="form-label">İçerik</label>
                            <textarea id="summernote" name='content'>{{ isset($post) ? $post->content : '' }}</textarea>
                        </div>
                        <div class="card-body">
                            <fieldset>
                                <label for="post-status" class="form-label">Durum</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="post-status1"
                                        value="1" required @if (!isset($post) || (isset($post) && $post->status)) checked @endif />
                                    <label class="form-check-label form-label" for="post-status1">Aktif</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="post-status2"
                                        value="0" required @if (isset($post) && !$post->status) checked @endif />
                                    <label class="form-check-label form-label" for="post-status2">Pasif</label>
                                </div>
                            </fieldset>
                        </div>
                        <hr>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit"
                                class="btn btn-primary me-1 mb-1">{{ isset($post) ? 'Güncelle' : 'Ekle' }}</button>
                            <button type="reset" class="btn btn-light-secondary me-1 mb-1">Temizle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('backend_assets/extensions/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('backend_assets/extensions/summernote/summernote-lite.min.js') }}"></script>
    <script src="{{ asset('backend_assets/static/js/pages/summernote.js') }}"></script>

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
