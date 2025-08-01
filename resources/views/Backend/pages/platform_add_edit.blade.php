@extends('Backend.index')

@section('title', '| Platformlar')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/sweetalert2/sweetalert2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend_assets/static/js/cropper/cropper.min.css') }}">
    <style>
        #image {
            max-width: 100%;
            display: none;
        }
        .img-container {
            max-width: 100%;
            overflow: hidden;
        }
        .cropper-container {
            max-width: 100%;
        }
    </style>
@endsection

@section('page-title', 'Platformlar')
@section('page-subtitle', isset($platform) ? 'Platform Güncelle' : 'Platform Ekle')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('platform.index') }}">Platformlar</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ isset($platform) ? 'Güncelle' : 'Ekle' }}</li>
@endsection

@section('content')
@php
    $platformTypes = [
        'multi_role_fighter' => 'Çok Amaçlı Savaş Uçağı',
        'air_superiority_fighter' => 'Hava Üstünlük Savaş Uçağı',
        'bomber' => 'Bombardıman Uçağı',
        'attack_aircraft' => 'Taarruz Uçağı',
        'reconnaissance_aircraft' => 'Keşif Uçağı',
        'electronic_warfare_aircraft' => 'Elektronik Harp Uçağı',
        'tanker_aircraft' => 'Yakıt İkmal Uçağı',
        'trainer_aircraft' => 'Eğitim Uçağı',
        'transport_aircraft' => 'Nakliye Uçağı',
        'attack_helicopter' => 'Taarruz Helikopteri',
        'transport_helicopter' => 'Nakliye Helikopteri',
        'uav' => 'İHA (İnsansız Hava Aracı)',
        'ucav' => 'SİHA (Silahlı İHA)',
        'other' => 'Diğer',
    ];
@endphp
    <section id="multiple-column-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ isset($platform) ? 'Platform Güncelle' : 'Platform Ekle' }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form"
                                action="{{ isset($platform) ? route('platform.update', $platform->id) : route('platform.store') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @if (isset($platform))
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <label for="platform-name" class="form-label">Platform Adı</label>
                                            <input type="text" id="platform-name" class="form-control"
                                                placeholder="Platform Adı" name="name"
                                                value="{{ isset($platform) ? $platform->name : '' }}" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <label for="platform-type" class="form-label">Tip</label>
                                            <select class="form-select" name="type" id="platform-type" required>
                                                <option value="">Seçiniz...</option>
                                                @foreach ($platformTypes as $value => $label)
                                                    <option value="{{ $value }}"
                                                        {{ isset($platform) && $platform->type == $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <label for="platform-origin" class="form-label">Ülke</label>
                                            <select class="form-select" name="origin" id="platform-origin" required>
                                                <option value="">Seçiniz...</option>
                                                @foreach ($countries as $code => $name)
                                                    <option value="{{ $code }}"
                                                        {{ isset($platform) && $platform->origin == $code ? 'selected' : '' }}>
                                                        {{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <label for="platform-image" class="form-label">Resim</label>
                                            <div class="col-12">
                                                <div class="form-group row align-items-center">
                                                    <div class="row" style="display: flex; align-items: center;">
                                                        <div class="col-2">
                                                            <label class="col-form-label" for="first-name">En / Boy Oranı</label>
                                                        </div>
                                                        <div class="col-4">
                                                            <input type="number" id="input-width" class="form-control"
                                                                name="iwidth" placeholder="Genişlik" min="1" value="1200">
                                                        </div>
                                                        <span class="col-1 text-center">/</span>
                                                        <div class="col-4">
                                                            <input type="number" id="input-length" class="form-control"
                                                                name="ilength" placeholder="Yükseklik" min="1" value="807">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="resimEkleAlani">
                                                <img id="image" src="" alt="" style="max-width: 100%;">
                                                <input type="hidden" id="croppedImage" name="croppedImage"
                                                    {{ isset($platform) ? '' : 'required' }}>
                                                @if (isset($platform) && !empty($platform->image))
                                                    <div id="previewImage" class="image-container"
                                                        style="display: flex; justify-content: center; align-items: center;">
                                                        <img src="{{ asset('storage/' . $platform->image) }}" alt=""
                                                            class="img-fluid">
                                                    </div>
                                                @endif
                                                <div class="img-container mt-3" id="previewContainer"
                                                    style="display: flex; justify-content: center; align-items: center; max-width: 100%;">
                                                </div>
                                                <input type="file" class="form-control" id="imageInput" name="platformImage"
                                                    accept="image/*" {{ isset($platform) ? '' : 'required' }}>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-12">
                                        <div class="form-group">
                                            <label for="platform-description" class="form-label">Açıklama</label>
                                            <textarea class="form-control" id="platform-description" rows="4" name="description" placeholder="Açıklama">{{ isset($platform) ? $platform->description : '' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="platform-status" class="form-label">Durum</label>
                                            <select class="form-select" name="status" id="platform-status">
                                                <option value="1"
                                                    {{ isset($platform) && $platform->status ? 'selected' : '' }}>Aktif
                                                </option>
                                                <option value="0"
                                                    {{ isset($platform) && !$platform->status ? 'selected' : '' }}>Pasif
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary me-1 mb-1">
                                            {{ isset($platform) ? 'Güncelle' : 'Ekle' }}
                                        </button>
                                        <button type="reset" class="btn btn-light-secondary me-1 mb-1">
                                            Temizle
                                        </button>
                                        <a href="{{ route('platform.index') }}" class="btn btn-secondary">İptal</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
                            aspectRatio: parseInt(document.getElementById('input-width')
                                .value) / parseInt(document.getElementById('input-length')
                                    .value),
                            viewMode: 1,
                            autoCropArea: 1,
                            responsive: true,
                            dragMode: 'move',
                            restore: false,
                            guides: true,
                            center: true,
                            highlight: false,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                            toggleDragModeOnDblclick: false
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
