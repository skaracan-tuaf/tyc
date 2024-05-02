@extends('Backend.index')

@section('title', 'Makale')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/simple-datatables/style.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/compiled/css/table-datatable.css') }}">

    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/sweetalert2/sweetalert2.min.css') }}" />
@endsection

@section('page-title', 'Makale')
@section('page-subtitle', 'Makale')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Makaleler</li>
@endsection

@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Makale Veri Tablosu
                    <a style="float: right;" href="{{ route('makale.create') }}" class="btn icon icon-left btn-secondary"><i
                            class="bi bi-plus"></i>Yeni</a>
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Resim</th>
                            <th>Başlık</th>
                            <th>Kategori</th>
                            <th>Slug</th>
                            <th>Açıklama</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($posts as $post)
                            <tr>
                                <td>{{ $post->id }}</td>
                                <td>
                                    @if ($post->image)
                                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->name }} Resmi"
                                            width="80" height="45" style="border-radius: 5%;">
                                    @else
                                        <img src="{{ asset('/backend_assets/static/images/logo/favicon.png') }}"
                                            width="40" height="40" alt="Varsayılan Resim">
                                    @endif
                                </td>
                                <td>{{ $post->title }}</td>
                                <td>{{ $post->category->name ?? 'Belirtilmemiş' }}</td>
                                <td>{{ $post->slug }}</td>
                                <td>{{ $post->summary }}</td>
                                <td>
                                    <form action="{{ route('makaleDurumunuDegistir', $post->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        @if ($post->status == 1)
                                            <!--span class="badge bg-success">Active</span-->
                                            <button type="submit"
                                                class="btn icon icon-left btn-success me-2 text-nowrap">Yayında</button>
                                        @elseif ($post->status == 0)
                                            <!--span class="badge bg-danger">Inactive</span-->
                                            <button type="submit"
                                                class="btn icon icon-left btn-warning me-2 text-nowrap">Beklemede</button>
                                        @else
                                            <i class="btn icon icon-left btn-secondary">
                                                Arşivlendi</i>
                                        @endif
                                    </form>
                                </td>
                                <td>
                                    <div class="comment-actions">
                                        <button class="btn icon icon-left btn-primary me-2 text-nowrap">
                                            <a href="{{ route('makale.edit', $post->id) }}"
                                                style="color: white; text-decoration: none;">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </button>
                                        <form action="{{ route('makaleyiArsivle', $post->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn icon icon-left btn-dark me-2 text-nowrap">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        <form id="deleteForm" action="{{ route('makale.destroy', $post->id) }}"
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
                        text: 'Makale kalıcı olarak silinecek. Silme işlemini gerçekleştirmek istediğinizden emin misiniz?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'SİL',
                        cancelButtonText: 'İptal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Kullanıcı "Evet, sil!" butonuna tıkladı
                            form.submit(); // Formu submit et
                        }
                    });
                });
            });
        });
    </script>
@endsection
