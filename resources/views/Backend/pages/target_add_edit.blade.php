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
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Üst Kategori --}}
                                <div class="col-md-6">
                                    <label for="category_id" class="form-label">Üst Kategori</label>
                                    <select name="category_id" id="category_id" class="form-select" required>
                                        <option value="">Seçiniz...</option>
                                        @foreach (collect($categories)->whereNull('parent_id') as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id', $target->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Alt Kategori --}}
                                <div class="col-md-6 mt-3">
                                    <label for="subcategory_id" class="form-label">Alt Kategori</label>
                                    <select name="subcategory_id" id="subcategory_id" class="form-select">
                                        <option value="">Alt kategori seçiniz</option>
                                        @if (isset($target) && $target->category_id)
                                            @foreach (collect($categories)->where('parent_id', $target->category_id) as $subcategory)
                                                <option value="{{ $subcategory->id }}"
                                                    {{ old('subcategory_id', $target->subcategory_id ?? '') == $subcategory->id ? 'selected' : '' }}>
                                                    {{ $subcategory->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('subcategory_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Değer --}}
                                <div class="col-md-6 mt-3">
                                    <label for="worth" class="form-label">Değer ($)</label>
                                    <input type="number" step="0.01" name="worth" id="worth" class="form-control"
                                        value="{{ old('worth', $target->worth ?? '') }}">
                                    @error('worth')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Durum --}}
                                <div class="col-md-6 mt-3">
                                    <label for="status" class="form-label">Durum</label>
                                    <input type="checkbox" name="status" id="status" value="1"
                                        {{ old('status', isset($target) && $target->status ? 1 : 0) ? 'checked' : '' }}>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Açıklama --}}
                                <div class="col-md-12 mt-3">
                                    <label for="description" class="form-label">Açıklama</label>
                                    <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $target->description ?? '') }}</textarea>
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
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
        document.addEventListener('DOMContentLoaded', function () {
            const categorySelect = document.getElementById('category_id');
            const subcategorySelect = document.getElementById('subcategory_id');
            const categories = @json($categories);

            function populateSubcategories(categoryId) {
                subcategorySelect.innerHTML = '<option value="">Alt kategori seçiniz</option>';
                if (categoryId) {
                    const subcategories = categories.filter(cat => cat.parent_id == categoryId);
                    subcategories.forEach(sub => {
                        const opt = document.createElement('option');
                        opt.value = sub.id;
                        opt.textContent = sub.name;
                        if (sub.id == "{{ old('subcategory_id', $target->subcategory_id ?? '') }}") {
                            opt.selected = true;
                        }
                        subcategorySelect.appendChild(opt);
                    });
                }
            }

            categorySelect.addEventListener('change', function () {
                populateSubcategories(this.value);
            });

            // Sayfa yüklendiğinde seçili kategorinin alt kategorilerini yükle
            if (categorySelect.value) {
                populateSubcategories(categorySelect.value);
            }

            // Choices.js başlatma
            new Choices(categorySelect);
            new Choices(subcategorySelect);
        });
    </script>
@endsection
