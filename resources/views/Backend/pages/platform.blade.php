@extends('Backend.index')

@section('title', '| Platformlar')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/simple-datatables/style.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/compiled/css/table-datatable.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/extensions/sweetalert2/sweetalert2.min.css') }}" />
@endsection

@section('page-title', 'Platformlar')
@section('page-subtitle', 'Tüm Platformlar')

@section('breadcrumb')
    <li class="breadcrumb-item active">Platformlar</li>
@endsection

@section('content')
<section class="section">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                Platform Listesi
                <a href="{{ route('platform.create') }}" class="btn btn-secondary float-end">
                    <i class="bi bi-plus"></i> Yeni Platform
                </a>
            </h5>
        </div>
        <div class="card-body">
            <table class="table table-striped" id="table1">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Adı</th>
                        <th>Tür</th>
                        <th>Ülke</th>
                        <th>Durum</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($platforms as $platform)
                        <tr>
                            <td>{{ $platform->id }}</td>
                            <td>{{ $platform->name }}</td>
                            <td>{{ $platform->type }}</td>
                            <td>{{ $platform->origin ?? '-' }}</td>
                            <td>
                                <form action="{{ route('platform.toggle', $platform->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-sm {{ $platform->status ? 'btn-success' : 'btn-warning' }}">
                                        {{ $platform->status ? 'Aktif' : 'Pasif' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <a href="{{ route('platform.edit', $platform->id) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('platform.destroy', $platform->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i>
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
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Emin misiniz?',
                    text: 'Bu platform silinecek.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Evet, sil',
                    cancelButtonText: 'İptal'
                }).then(result => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
