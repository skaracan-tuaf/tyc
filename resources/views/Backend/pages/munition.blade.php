@extends('Backend.index')

@section('title', 'Mühimmat')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/simple-datatables/style.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/compiled/css/table-datatable.css') }}">

    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/sweetalert2/sweetalert2.min.css') }}" />
@endsection

@section('page-title', 'Mühimmatlar')
@section('page-subtitle', 'Mühimmatlar')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Mühimmatlar</li>
@endsection

@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Mühimmat Veri Tablosu
                    <a style="float: right;" href="{{ route('muhimmat.create') }}"
                        class="btn icon icon-left btn-secondary"><i class="bi bi-plus"></i>Yeni</a>
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kategori</th>
                            <th>Adı</th>
                            <th>Slug</th>
                            <th>Ülke</th>
                            <th>Fiyat</th>
                            <th>Özet</th>
                            <th>Açıklama</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    @php
                        // Ülke kodlarını ve isimlerini içeren bir dizi oluştur
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

                        // Verilen ülke kodunu isimle eşleştiren bir işlev oluştur
                        function myGetCountryName($code, $countries)
                        {
                            return $countries[$code] ?? $code;
                        }
                    @endphp
                    <tbody>
                        @foreach ($munitions as $munition)
                            <tr>
                                <td>{{ $munition->id }}</td>
                                <td>{{ $munition->category->name ?? 'Belirtilmemiş' }}</td>
                                <td>{{ $munition->name }}</td>
                                <td>{{ $munition->slug }}</td>
                                <td>{{ myGetCountryName($munition->origin, $countries) }}</td>
                                <td>{{ $munition->price }}</td>
                                <td>{{ $munition->summary ?? 'N/A' }}</td>
                                <td>{{ $munition->description ?? 'N/A' }}</td>
                                <td>
                                    <form action="{{ route('muhimmatDurumunuDegistir', $munition->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        @if ($munition->status)
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
                                            <a href="{{ route('muhimmat.edit', $munition->id) }}"
                                                style="color: white; text-decoration: none;">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </button>
                                        <form id="deleteForm" action="{{ route('muhimmat.destroy', $munition->id) }}"
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
