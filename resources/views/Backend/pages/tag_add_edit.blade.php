@extends('Backend.index')

@section('title', 'Etiket')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/static/js/cropper/cropper.min.css') }}">
@endsection

@section('page-title', 'Etiketler')
@section('page-subtitle', 'Etiket Ekle')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('ozellik.index') }}">Etiketler</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ isset($tag) ? 'Güncelle' : 'Ekle' }}</li>
@endsection

@section('content')
    <!-- // Basic multiple Column Form section start -->
    <section id="multiple-column-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Etiket Ekle</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form"
                                action="{{ isset($tag) ? route('ozellik.update', $tag->id) : route('ozellik.store') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @if (isset($tag))
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <label for="tag-name" class="form-label">Etiket Adı</label>
                                            <input type="text" id="tag-name" class="form-control"
                                                placeholder="Etiket Adı" name="name"
                                                value="{{ isset($tag) ? $tag->name : '' }}" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="tag-description" class="form-label">Açıklama</label>
                                            <textarea class="form-control" id="tag-description" rows="2" name="description" placeholder="Açıklama">{{ isset($tag) ? $tag->description : '' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <fieldset>
                                                <label class="form-label"> Durum </label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status"
                                                        id="tag-status1" value="1" required
                                                        @if (!isset($tag) || (isset($tag) && $tag->status)) checked @endif />
                                                    <label class="form-check-label form-label" for="tag-status1">
                                                        Aktif
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status"
                                                        id="tag-status2" value="0" required
                                                        @if (isset($tag) && !$tag->status) checked @endif />
                                                    <label class="form-check-label form-label" for="tag-status2">
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
                                            {{ isset($tag) ? 'Güncelle' : 'Ekle' }}
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
