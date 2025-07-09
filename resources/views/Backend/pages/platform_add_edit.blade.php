@extends('Backend.index')

@section('title', '| Platformlar')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/sweetalert2/sweetalert2.min.css') }}" />
@endsection

@section('page-title', 'Platformlar')
@section('page-subtitle', isset($platform) ? 'Platform Güncelle' : 'Platform Ekle')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('platform.index') }}">Platformlar</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ isset($platform) ? 'Güncelle' : 'Ekle' }}</li>
@endsection

@section('content')
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
                                                @foreach ($types as $type)
                                                    <option value="{{ $type }}" {{ (isset($platform) && $platform->type == $type) ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
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
                                                    <option value="{{ $code }}" {{ (isset($platform) && $platform->origin == $code) ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="platform-image" class="form-label">Görsel</label>
                                            <input type="file" id="platform-image" class="form-control" name="image" accept="image/*">
                                            @if (isset($platform) && $platform->image)
                                                <img src="{{ asset('storage/' . $platform->image) }}" alt="Platform Görseli" style="max-width: 120px; max-height: 120px; margin-top: 10px;">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="platform-description" class="form-label">Açıklama</label>
                                            <textarea class="form-control" id="platform-description" rows="4" name="description" placeholder="Açıklama">{{ isset($platform) ? $platform->description : '' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="platform-status" class="form-label">Durum</label>
                                            <select class="form-select" name="status" id="platform-status">
                                                <option value="1" {{ (isset($platform) && $platform->status) ? 'selected' : '' }}>Aktif</option>
                                                <option value="0" {{ (isset($platform) && !$platform->status) ? 'selected' : '' }}>Pasif</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">{{ isset($platform) ? 'Güncelle' : 'Ekle' }}</button>
                                    <a href="{{ route('platform.index') }}" class="btn btn-secondary">İptal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
