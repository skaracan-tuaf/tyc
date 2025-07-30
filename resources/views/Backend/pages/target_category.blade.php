@extends('Backend.index')

@section('title', '| Hedef Kategorileri')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/simple-datatables/style.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/compiled/css/table-datatable.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/sweetalert2/sweetalert2.min.css') }}" />
@endsection

@section('page-title', 'Hedef Kategorileri')
@section('page-subtitle', 'Mühimmat Hedef Kategorileri')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Hedef Kategorileri</li>
@endsection

@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Hedef Kategori Veri Tablosu
                    <a style="float: right;" href="{{ route('target-category.create') }}"
                        class="btn icon icon-left btn-secondary"><i class="bi bi-plus"></i>Yeni</a>
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Resim</th>
                            <th>Adı</th>
                            <th>Üst Kategori</th>
                            <th>Slug</th>
                            <th>Açıklama</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($targetCategories as $targetCategory)
                            <tr>
                                <td>{{ $targetCategory->id }}</td>
                                <td>
                                    @if ($targetCategory->image)
                                        <img src="{{ asset('storage/' . $targetCategory->image) }}"
                                            alt="{{ $targetCategory->name }} Resmi" width="40" height="40"
                                            style="border-radius: 50%;">
                                    @else
                                        <img src="{{ asset('/backend_assets/static/images/logo/favicon.png') }}"
                                            width="40" height="40" alt="Varsayılan Resim">
                                    @endif
                                </td>
                                <td>{{ $targetCategory->name }}</td>
                                <td>
                                    @php
                                        $parentCategories = [];
                                        $currentCategory = $targetCategory;
                                        while ($currentCategory->parent) {
                                            array_unshift($parentCategories, $currentCategory->parent->name);
                                            $currentCategory = $currentCategory->parent;
                                        }
                                    @endphp
                                    {{ implode('->', $parentCategories) ?: '-' }}
                                </td>
                                <td>{{ $targetCategory->slug }}</td>
                                <td>{{ $targetCategory->description }}</td>
                                <td>
                                    <form action="{{ route('target-category.changeStatus', $targetCategory->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('POST')
                                        @if ($targetCategory->status)
                                            <button type="submit"
                                                class="btn icon icon-left btn-success me-2 text-nowrap">Yayında</button>
                                        @else
                                            <button type="submit"
                                                class="btn icon icon-left btn-warning me-2 text-nowrap">Beklemede</button>
                                        @endif
                                    </form>
                                </td>
                                <td>
                                    <div class="comment-actions">
                                        <button class="btn icon icon-left btn-primary me-2 text-nowrap">
                                            <a href="{{ route('target-category.edit', $targetCategory->id) }}"
                                                style="color: white; text-decoration: none;">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </button>
                                        <form id="deleteForm-{{ $targetCategory->id }}"
                                            action="{{ route('target-category.destroy', $targetCategory->id) }}"
                                            method="POST" class="delete-form d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn icon icon-left btn-danger me-2 text-nowrap">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('backend_assets/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
    <script src="{{ asset('backend_assets/static/js/pages/simple-datatables.js') }}"></script>
    <script src="{{ asset('backend_assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var allForms = document.querySelectorAll('.delete-form');

            allForms.forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();

                    Swal.fire({
                        title: 'Emin misiniz?',
                        text: 'Hedef kategorinin alt kategorileri de silinecek. Silme işlemini gerçekleştirmek istediğinizden emin misiniz?',
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
        });
    </script>
@endsection
