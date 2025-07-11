@extends('Backend.index')

@section('title', '| Hedef Ekle')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/choices.js/public/assets/styles/choices.css') }}">
@endsection

@section('page-title', 'Hedefler')
@section('page-subtitle', isset($target) ? 'Hedef Güncelle' : 'Yeni Hedef Ekle')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('target.index') }}">Hedefler</a></li>
    <li class="breadcrumb-item active">{{ isset($target) ? 'Güncelle' : 'Ekle' }}</li>
@endsection

@section('content')
    <section id="form-target">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ isset($target) ? 'Hedef Güncelle' : 'Yeni Hedef Ekle' }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ isset($target) ? route('target.update', $target->id) : route('target.store') }}"
                            method="POST">
                            @csrf
                            @if (isset($target)) @method('PUT') @endif

                            <div class="row">
                                {{-- Hedef Adı --}}
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Hedef Adı</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Örn: Pist, Hangar"
                                        value="{{ old('name', $target->name ?? '') }}" required>
                                </div>

                                {{-- Üst Kategori --}}
                                <div class="col-md-6">
                                    <label for="category" class="form-label">Üst Kategori</label>
                                    <select name="category" id="category" class="form-select" required>
                                        <option value="">Seçiniz...</option>
                                        @foreach ($categories as $main => $subs)
                                            <option value="{{ $main }}" {{ old('category', $target->category ?? '') == $main ? 'selected' : '' }}>
                                                {{ $main }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Alt Kategori --}}
                                <div class="col-md-6 mt-3">
                                    <label for="subcategory" class="form-label">Alt Kategori</label>
                                    <select name="subcategory" id="subcategory" class="form-select">
                                        <option value="">Alt kategori seçiniz</option>
                                    </select>
                                </div>

                                {{-- Değer --}}
                                <div class="col-md-6 mt-3">
                                    <label for="worth" class="form-label">Değer ($)</label>
                                    <input type="number" step="0.01" name="worth" id="worth" class="form-control"
                                        value="{{ old('worth', $target->worth ?? '') }}">
                                </div>

                                {{-- Açıklama --}}
                                <div class="col-md-12 mt-3">
                                    <label for="description" class="form-label">Açıklama</label>
                                    <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $target->description ?? '') }}</textarea>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary">{{ isset($target) ? 'Güncelle' : 'Ekle' }}</button>
                                <a href="{{ route('target.index') }}" class="btn btn-light-secondary ms-2">İptal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('backend_assets/extensions/choices.js/public/assets/scripts/choices.js') }}"></script>

    <script>
        const categoryMap = @json($categories);
        const oldCategory = "{{ old('category', $target->category ?? '') }}";
        const oldSubcategory = "{{ old('subcategory', $target->subcategory ?? '') }}";

        document.addEventListener('DOMContentLoaded', function () {
            const categorySelect = document.getElementById('category');
            const subcategorySelect = document.getElementById('subcategory');

            function populateSubcategories(category) {
                const subcategories = categoryMap[category] || [];
                subcategorySelect.innerHTML = '<option value="">Alt kategori seçiniz</option>';
                subcategories.forEach(sub => {
                    const opt = document.createElement('option');
                    opt.value = sub;
                    opt.textContent = sub;
                    if (sub === oldSubcategory) opt.selected = true;
                    subcategorySelect.appendChild(opt);
                });
            }

            categorySelect.addEventListener('change', function () {
                populateSubcategories(this.value);
            });

            // Sayfa yüklendiğinde seçili kategorinin alt kategorilerini yükle
            if (oldCategory) {
                categorySelect.value = oldCategory;
                populateSubcategories(oldCategory);
            }
        });
    </script>
@endsection
