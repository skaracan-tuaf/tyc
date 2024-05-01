@extends('Backend.index')

@section('title', 'Özellik')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/static/js/cropper/cropper.min.css') }}">
@endsection

@section('page-title', 'Özellikler')
@section('page-subtitle', 'Özellik Ekle')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('ozellik.index') }}">Özellikler</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ isset($attribute) ? 'Güncelle' : 'Ekle' }}</li>
@endsection

@section('content')
    <!-- // Basic multiple Column Form section start -->
    <section id="multiple-column-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Özellik Ekle</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form"
                                action="{{ isset($attribute) ? route('ozellik.update', $attribute->id) : route('ozellik.store') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @if (isset($attribute))
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <label for="attribute-name" class="form-label">Özellik Adı</label>
                                            <input type="text" id="attribute-name" class="form-control"
                                                placeholder="Özellik Adı" name="name"
                                                value="{{ isset($attribute) ? $attribute->name : '' }}" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <label for="option-type" class="form-label">Tip</label>
                                            <fieldset class="form-group">
                                                <select class="form-select" name="option" id="option-type" required>
                                                    <option value="" disabled>Seçenek</option>
                                                    @foreach ($enumValues as $option)
                                                    <option value="{{ $option }}" {{ isset($attribute) && $option === $attribute->option ? 'selected' : '' }}>
                                                        {{ ucfirst($option) }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="attribute-description" class="form-label">Açıklama</label>
                                            <textarea class="form-control" id="attribute-description" rows="2" name="description" placeholder="Açıklama">{{ isset($attribute) ? $attribute->description : '' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <fieldset>
                                                <label class="form-label"> Durum </label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status"
                                                        id="attribute-status1" value="1" required
                                                        @if (!isset($attribute) || (isset($attribute) && $attribute->status)) checked @endif />
                                                    <label class="form-check-label form-label" for="attribute-status1">
                                                        Aktif
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status"
                                                        id="attribute-status2" value="0" required
                                                        @if (isset($attribute) && !$attribute->status) checked @endif />
                                                    <label class="form-check-label form-label" for="attribute-status2">
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
                                            {{ isset($attribute) ? 'Güncelle' : 'Ekle' }}
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
@endsection
