@extends('Backend.index')

@section('title', '| Hedefler')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/simple-datatables/style.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/compiled/css/table-datatable.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/sweetalert2/sweetalert2.min.css') }}" />
@endsection

@section('page-title', 'Hedefler')
@section('page-subtitle', 'Askeri Hedefler')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Hedefler</li>
@endsection

@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Hedef Veri Tablosu
                    <a style="float: right;" href="{{ route('target.create') }}"
                        class="btn icon icon-left btn-secondary"><i class="bi bi-plus"></i>Yeni</a>
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Adı</th>
                            <th>Kategori</th>
                            <th>Alt Kategori</th>
                            <th>Değer ($)</th>
                            <th>Durum</th>
                            <th>Açıklama</th>
                            <th>İşlem</th>
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
                                    <form action="{{ route('target-category.changeStatus', $target->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $target->status ? 'btn-success' : 'btn-warning' }}">
                                            {{ $target->status ? 'Aktif' : 'Pasif' }}
                                        </button>
                                    </form>
                                </td>
                                <td>{{ Str::limit($target->description, 50) }}</td>
                                <td>
                                    <a href="{{ route('target.edit', $target->id) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('target.destroy', $target->id) }}" method="POST"
                                        class="d-inline delete-form">
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
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('backend_assets/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
    <script src="{{ asset('backend_assets/static/js/pages/simple-datatables.js') }}"></script>
    <script src="{{ asset('backend_assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.delete-form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
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
                    }).then((resultan) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
?>
