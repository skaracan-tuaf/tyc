@extends('Backend.index')

@section('title', '| Hedef Kategorileri')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/static/js/cropper/cropper.min.css') }}">
@endsection

@section('page-title', 'Hedef Kategorileri')
@section('page-subtitle', 'Hedef Kategorisi Ekle')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('target-category.index') }}">Hedef Kategorileri</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ isset($targetCategory) ? 'Güncelle' : 'Ekle' }}</li>
@endsection

@section('content')
    <section id="multiple-column-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Hedef Kategori Ekle</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form"
                                action="{{ isset($targetCategory) ? route('target-category.update', $targetCategory->id) : route('target-category.store') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @if (isset($targetCategory))
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <label for="category-name" class="form-label">Hedef Kategori Adı</label>
                                            <input type="text" id="category-name" class="form-control"
                                                placeholder="Hedef Kategori Adı" name="name"
                                                value="{{ isset($targetCategory) ? $targetCategory->name : '' }}" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <label for="parent-category" class="form-label">Üst Kategori</label>
                                            <fieldset class="form-group">
                                                <select class="form-select" name="parent" id="parent-category">
                                                    <option value="">Üst Kategori</option>
                                                    @foreach ($targetCategories as $cat)
                                                        @php
                                                            $catName = $cat->name;
                                                            $parentCategories = [];
                                                            $currentCategory = $cat;
                                                            while ($currentCategory->parent) {
                                                                array_unshift($parentCategories, $currentCategory->parent->name);
                                                                $currentCategory = $currentCategory->parent;
                                                            }
                                                            if (!empty($parentCategories)) {
                                                                $catName = implode('->', $parentCategories) . '->' . $cat->name;
                                                            }
                                                        @endphp
                                                        @if (!isset($targetCategory) || (isset($targetCategory) && $targetCategory->id != $cat->id))
                                                            <option value="{{ $cat->id }}"
                                                                {{ isset($targetCategory) && $targetCategory->parent_id == $cat->id ? 'selected' : '' }}>
                                                                {{ $catName }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="category-description" class="form-label">Açıklama</label>
                                            <textarea class="form-control" id="category-description" rows="3" name="description" placeholder="Açıklama">{{ isset($targetCategory) ? $targetCategory->description : '' }}</textarea>
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
                                                        @if (!isset($targetCategory) || (isset($targetCategory) && $targetCategory->status)) checked @endif />
                                                    <label class="form-check-label form-label" for="category-status1">
                                                        Aktif
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status"
                                                        id="category-status2" value="0" required
                                                        @if (isset($targetCategory) && !$targetCategory->status) checked @endif />
                                                    <label class="form-check-label form-label" for="category-status2">
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
                                            {{ isset($targetCategory) ? 'Güncelle' : 'Ekle' }}
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
@endsection

@section('scripts')
    <script src="{{ asset('backend_assets/extensions/jquery/jquery.min.js') }}"></script>
@endsection
