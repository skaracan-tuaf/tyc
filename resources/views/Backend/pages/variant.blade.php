@extends('Backend.index')

@section('title', '| Varyantlar')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/simple-datatables/style.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/compiled/css/table-datatable.css') }}">

    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/sweetalert2/sweetalert2.min.css') }}" />
@endsection

@section('page-title', 'Varyantler')
@section('page-subtitle', 'Varyantlar')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Varyantlar</li>
@endsection

@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Varyant Veri Tablosu
                    <a style="float: right;" href="{{ route('varyant.create') }}"
                        class="btn icon icon-left btn-secondary"><i class="bi bi-plus"></i>Yeni</a>
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Adı</th>
                            <th>Değerler</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($variants as $variant)
                            <tr>
                                <td>{{ $variant->id }}</td>
                                <td>{{ $variant->name }}</td>
                                <td>
                                    @foreach ($variant->values as $value)
                                        {{ $value->value }}
                                        @if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    <div class="comment-actions">
                                        <button class="btn icon icon-left btn-primary me-2 text-nowrap">
                                            <a href="{{ route('varyant.edit', $variant->id) }}"
                                                style="color: white; text-decoration: none;">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </button>
                                        <form id="deleteForm" action="{{ route('varyant.destroy', $variant->id) }}"
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
