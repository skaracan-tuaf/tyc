@extends('Backend.index')

@section('title', '| Hedef Yönetimi')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/simple-datatables/style.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/compiled/css/table-datatable.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/sweetalert2/sweetalert2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/choices.js/public/assets/styles/choices.css') }}">
@endsection

@section('page-title', 'Hedef Yönetimi')
@section('page-subtitle', 'Hedef Kategorileri ve Hedefler')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Hedef Yönetimi</li>
@endsection

@section('content')
    <div class="row">
        <!-- Sol Sütun - Kategori Ekleme/Düzenleme -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        {{ isset($editingCategory) ? 'Kategori Düzenle' : 'Yeni Kategori Ekle' }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ isset($editingCategory) ? route('target-category.update', $editingCategory->id) : route('target-category.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($editingCategory))
                            @method('PUT')
                        @endif

                        <div class="form-group mb-3">
                            <label for="category-name" class="form-label">Kategori Adı</label>
                            <input type="text" id="category-name" class="form-control" name="name"
                                value="{{ old('name', $editingCategory->name ?? '') }}" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="parent-category" class="form-label">Üst Kategori</label>
                            <select class="form-select" name="parent" id="parent-category">
                                <option value="">Üst Kategori Yok</option>
                                @foreach ($targetCategories as $cat)
                                    @if (!isset($editingCategory) || (isset($editingCategory) && $editingCategory->id != $cat->id))
                                        <option value="{{ $cat->id }}"
                                            {{ old('parent', $editingCategory->parent_id ?? '') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('parent')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="category-description" class="form-label">Açıklama</label>
                            <textarea class="form-control" id="category-description" rows="3" name="description">{{ old('description', $editingCategory->description ?? '') }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Durum</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" value="1" required
                                    {{ old('status', isset($editingCategory) ? $editingCategory->status : 1) ? 'checked' : '' }}>
                                <label class="form-check-label">Aktif</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" value="0" required
                                    {{ old('status', isset($editingCategory) ? $editingCategory->status : 1) == 0 ? 'checked' : '' }}>
                                <label class="form-check-label">Pasif</label>
                            </div>
                            @error('status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($editingCategory) ? 'Güncelle' : 'Ekle' }}
                            </button>
                            @if (isset($editingCategory))
                                <a href="{{ route('target.management') }}" class="btn btn-secondary">İptal</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sağ Sütun - Kategori Listesi -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Hedef Kategorileri</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped" id="categoryTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Adı</th>
                                <th>Üst Kategori</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($targetCategories as $category)
                                <tr>
                                    <td>{{ $category->id }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->parent ? $category->parent->name : '-' }}</td>
                                    <td>
                                        <form action="{{ route('target-category.changeStatus', $category->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $category->status ? 'btn-success' : 'btn-warning' }}">
                                                {{ $category->status ? 'Aktif' : 'Pasif' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="{{ route('target.management', ['edit_category' => $category->id]) }}"
                                           class="btn btn-primary btn-sm">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('target-category.destroy', $category->id) }}" method="POST" class="d-inline delete-category-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Hedefler Bölümü -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        Hedefler
                        <a href="{{ route('target.create', ['return_to' => 'management']) }}" class="btn btn-secondary float-end">
                            <i class="bi bi-plus"></i> Yeni Hedef
                        </a>
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped" id="targetTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Adı</th>
                                <th>Kategori</th>
                                <th>Alt Kategori</th>
                                <th>Değer ($)</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($targets as $target)
                                <tr>
                                    <td>{{ $target->id }}</td>
                                    <td>{{ $target->name }}</td>
                                    <td>{{ $target->category->name ?? '-' }}</td>
                                    <td>{{ $target->subcategory ? $target->subcategory->name : '-' }}</td>
                                    <td>{{ number_format($target->worth, 2) }}</td>
                                    <td>
                                        <form action="{{ route('target.changeStatus', $target->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $target->status ? 'btn-success' : 'btn-warning' }}">
                                                {{ $target->status ? 'Aktif' : 'Pasif' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="{{ route('target.edit', ['target' => $target->id, 'return_to' => 'management']) }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('target.destroy', $target->id) }}" method="POST" class="d-inline delete-target-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('backend_assets/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
    <script src="{{ asset('backend_assets/static/js/pages/simple-datatables.js') }}"></script>
    <script src="{{ asset('backend_assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('backend_assets/extensions/choices.js/public/assets/scripts/choices.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Kategori silme işlemi
            document.querySelectorAll('.delete-category-form').forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Emin misiniz?',
                        text: 'Bu kategoriyi ve alt kategorilerini silmek istediğinizden emin misiniz?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'SİL',
                        cancelButtonText: 'İptal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Hedef silme işlemi
            document.querySelectorAll('.delete-target-form').forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Emin misiniz?',
                        text: 'Bu hedefi silmek istediğinizden emin misiniz?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'SİL',
                        cancelButtonText: 'İptal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // DataTables başlatma
            new simpleDatatables.DataTable("#categoryTable");
            new simpleDatatables.DataTable("#targetTable");
        });
    </script>
@endsection
