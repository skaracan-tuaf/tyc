@extends('Backend.index')

@section('title', '| Etiket Yönetimi')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/simple-datatables/style.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/compiled/css/table-datatable.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/sweetalert2/sweetalert2.min.css') }}" />
@endsection

@section('page-title', 'Etiket Yönetimi')
@section('page-subtitle', 'Etiketler')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Etiket Yönetimi</li>
@endsection

@section('content')
<div class="row">
    <!-- Sol Sütun - Etiket Ekle/Düzenle -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    {{ isset($editingTag) ? 'Etiket Düzenle' : 'Yeni Etiket Ekle' }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ isset($editingTag) ? route('etiket.update', $editingTag->id) : route('etiket.store') }}" method="POST">
                    @csrf
                    @if (isset($editingTag))
                        @method('PUT')
                    @endif
                    <div class="form-group mb-3">
                        <label for="tag-name" class="form-label">Etiket Adı</label>
                        <input type="text" id="tag-name" class="form-control" name="name" value="{{ old('name', $editingTag->name ?? '') }}" required />
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Durum</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" value="1" required {{ old('status', isset($editingTag) ? $editingTag->status : 1) ? 'checked' : '' }}>
                            <label class="form-check-label">Aktif</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" value="0" required {{ old('status', isset($editingTag) ? $editingTag->status : 1) == 0 ? 'checked' : '' }}>
                            <label class="form-check-label">Pasif</label>
                        </div>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($editingTag) ? 'Güncelle' : 'Ekle' }}
                        </button>
                        @if (isset($editingTag))
                            <a href="{{ route('tag.management') }}" class="btn btn-secondary">İptal</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Sağ Sütun - Etiket Listesi -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Etiketler</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="tagTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Adı</th>
                            <th>Slug</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tags as $tag)
                            <tr>
                                <td>{{ $tag->id }}</td>
                                <td>{{ $tag->name }}</td>
                                <td>{{ $tag->slug }}</td>
                                <td>
                                    <form action="{{ route('etiketDurumunuDegistir', $tag->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm {{ $tag->status ? 'btn-success' : 'btn-warning' }}">
                                            {{ $tag->status ? 'Aktif' : 'Pasif' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <a href="{{ route('tag.management', ['edit_tag' => $tag->id]) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('etiket.destroy', $tag->id) }}" method="POST" class="d-inline delete-tag-form">
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Etiket silme işlemi
            document.querySelectorAll('.delete-tag-form').forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Emin misiniz?',
                        text: 'Bu etiketi silmek istediğinizden emin misiniz?',
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
            new simpleDatatables.DataTable("#tagTable");
        });
    </script>
@endsection
