@extends('Backend.index')

@section('title', '| Platformlar')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/simple-datatables/style.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/compiled/css/table-datatable.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/sweetalert2/sweetalert2.min.css') }}" />
@endsection

@section('page-title', 'Platformlar')
@section('page-subtitle', 'Platformlar')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Platformlar</li>
@endsection

@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Platform Veri Tablosu
                    <a style="float: right;" href="{{ route('platform.create') }}"
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
                            <th>Ülke</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    @php
                        $countries = [
                            'TR' => 'Türkiye',
                            'US' => 'A.B.D',
                            'DE' => 'Almanya',
                            'FR' => 'Fransa',
                            'JP' => 'Japonya',
                            'CN' => 'Çin',
                            'IN' => 'Hindistan',
                            'IL' => 'İsrail',
                            'RU' => 'Rusya',
                            'UA' => 'Ukrayna',
                            'BR' => 'Brezilya',
                            'GB' => 'İngiltere',
                            'IT' => 'İtalya',
                            'ES' => 'İspanya',
                            'CA' => 'Kanada',
                            'AU' => 'Avustralya',
                            'NL' => 'Hollanda',
                            'CH' => 'İsviçre',
                            'SG' => 'Singapur',
                            'SE' => 'İsveç',
                            'BE' => 'Belçika',
                            'AT' => 'Avusturya',
                            'KR' => 'Güney Kore',
                        ];
                        function myGetCountryName($code, $countries) {
                            return $countries[$code] ?? $code;
                        }
                    @endphp
                    <tbody>
                        @foreach ($platforms as $platform)
                            <tr>
                                <td>{{ $platform->id }}</td>
                                <td>{{ $platform->name }}</td>
                                <td>{{ ucfirst($platform->type) }}</td>
                                <td>{{ myGetCountryName($platform->origin, $countries) }}</td>
                                <td>
                                    @if ($platform->status)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-warning">Pasif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="comment-actions">
                                        <a href="{{ route('platform.edit', $platform->id) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil-square"></i></a>
                                        <form action="{{ route('platform.destroy', $platform->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-x-circle"></i></button>
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
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
