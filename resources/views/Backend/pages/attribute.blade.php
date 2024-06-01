@extends('Backend.index')

@section('title', '| Özellikler')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/simple-datatables/style.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/compiled/css/table-datatable.css') }}">

    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/sweetalert2/sweetalert2.min.css') }}" />
@endsection

@section('page-title', 'Özellikler')
@section('page-subtitle', 'Özellik Kategorileri')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Özellikler</li>
@endsection

@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Özellik Veri Tablosu
                    <a style="float: right;" href="{{ route('ozellik.create') }}"
                        class="btn icon icon-left btn-secondary"><i class="bi bi-plus"></i>Yeni</a>
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Adı</th>
                            <th>Tip</th>
                            <th>Katsayı</th>
                            <th>Slug</th>
                            <th>Açıklama</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attributes as $attribute)
                            <tr>
                                <td>{{ $attribute->id }}</td>
                                <td>{{ $attribute->name }}</td>
                                <td>{{ $attribute->option }}</td>
                                <td>{{ $attribute->multiplier }}</td>
                                <td>{{ $attribute->slug }}</td>
                                <td>{{ $attribute->description }}</td>
                                <td>
                                    <form action="{{ route('ozellikDurumunuDegistir', $attribute->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        @if ($attribute->status)
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
                                <td>
                                    <div class="comment-actions">
                                        <button class="btn icon icon-left btn-primary me-2 text-nowrap">
                                            <a href="{{ route('ozellik.edit', $attribute->id) }}"
                                                style="color: white; text-decoration: none;">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </button>
                                        <form id="deleteForm" action="{{ route('ozellik.destroy', $attribute->id) }}"
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
                        text: 'Silme işlemini gerçekleştirmek istediğinizden emin misiniz?',
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
