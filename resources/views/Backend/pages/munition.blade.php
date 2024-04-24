@extends('Backend.index')

@section('title', 'Mühimmat')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/simple-datatables/style.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/compiled/css/table-datatable.css') }}">
@endsection

@section('page-title', 'Mühimmatlar')
@section('page-subtitle', 'Mühimmatlar')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">DataTable</li>
@endsection

@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Mühimmat Veri Tablosu
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
                            <th>Özet</th>
                            <th>Açıklama</th>
                            <th>Fiyat</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($munitions as $munition)
                            <tr>
                                <td>{{ $munition->id }}</td>
                                <td>{{ $munition->category->name ?? 'Belirtilmemiş' }}</td>
                                <td>{{ $munition->name }}</td>
                                <td>{{ $munition->slug }}</td>
                                <td>{{ $munition->summary ?? 'N/A' }}</td>
                                <td>{{ $munition->description ?? 'N/A' }}</td>
                                <td>{{ $munition->price }}</td>
                                <td>
                                    @if ($munition->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
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
