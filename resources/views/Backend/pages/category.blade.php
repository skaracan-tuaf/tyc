@extends('Backend.index')

@section('title', 'Kategori')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/simple-datatables/style.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/compiled/css/table-datatable.css') }}">
@endsection

@section('page-title', 'Kategoriler')
@section('page-subtitle', 'Mühimmat Kategorileri')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">DataTable</li>
@endsection

@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Kategori Veri Tablosu
                    <a style="float: right;" href="{{ route('kategori.create') }}"
                        class="btn icon icon-left btn-secondary"><i class="bi bi-plus"></i>Yeni</a>
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table-category">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Adı</th>
                            <th>Üst Kategori</th>
                            <th>Slug</th>
                            <th>Durum</th>
                            <th>Resim</th>
                            <th>Açıklama</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->parent ? $category->parent->name : 'Yok' }}</td>
                                <td>{{ $category->slug }}</td>
                                <td>
                                    <form action="{{ route('kategoriDurumunuDegistir', $category->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        @if ($category->status)
                                            <!--span class="badge bg-success">Active</span-->
                                            <button type="submit"
                                                class="btn icon icon-left btn-success me-2 text-nowrap">Yayında</button>
                                        @else
                                            <!--span class="badge bg-danger">Inactive</span-->
                                            <button type="submit"
                                                class="btn icon icon-left btn-warning me-2 text-nowrap">Beklemede</button>
                                        @endif
                                    </form>

                                </td>
                                <td>{{ $category->image }}</td>
                                <td>{{ $category->description }}</td>
                                <td>
                                    <div class="comment-actions">
                                        <button class="btn icon icon-left btn-primary me-2 text-nowrap">
                                            <a href="{{ route('kategori.edit', $category->id) }}"
                                                style="color: white; text-decoration: none;">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </button>
                                        <form id="deleteForm" action="{{ route('kategori.destroy', $category->id) }}"
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
@endsection
