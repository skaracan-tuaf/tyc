@extends('Backend.index')

@section('title', '| Platform Ekle')

@section('page-title', 'Platformlar')
@section('page-subtitle', isset($platform) ? 'Platform Güncelle' : 'Yeni Platform')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('platform.index') }}">Platformlar</a></li>
    <li class="breadcrumb-item active">{{ isset($platform) ? 'Güncelle' : 'Ekle' }}</li>
@endsection

@section('content')
<section id="form-section">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h4>{{ isset($platform) ? 'Platform Güncelle' : 'Yeni Platform Ekle' }}</h4></div>
                <div class="card-body">
                    <form action="{{ isset($platform) ? route('platform.update', $platform->id) : route('platform.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($platform)) @method('PUT') @endif

                        <div class="row">
                            <div class="col-md-6">
                                <label for="name">Platform Adı</label>
                                <input type="text" name="name" id="name" class="form-control" required
                                    value="{{ old('name', $platform->name ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="type">Platform Türü</label>
                                <input type="text" name="type" id="type" class="form-control"
                                    value="{{ old('type', $platform->type ?? '') }}" required>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="origin">Ülke</label>
                                <select name="origin" class="form-select">
                                    <option value="">Seçiniz...</option>
                                    @foreach ($countries as $code => $name)
                                        <option value="{{ $code }}"
                                            {{ old('origin', $platform->origin ?? '') === $code ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label>Durum</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="active" value="1"
                                        {{ old('status', $platform->status ?? 1) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="active">Aktif</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="inactive" value="0"
                                        {{ old('status', $platform->status ?? 1) == 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="inactive">Pasif</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($platform) ? 'Güncelle' : 'Kaydet' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
