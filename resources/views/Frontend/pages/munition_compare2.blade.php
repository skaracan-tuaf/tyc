@extends('Frontend.index')

@section('title', '| Mühimmat Karşılaştırma')

@section('content')

<div class="container mt-4">
    <h3 class="mb-4">Meteorolojik ve Hedef Seçimine Göre Sonuç</h3>

    @if (empty($munitions) || count($munitions) === 0)
        <div class="alert alert-warning">
            Aradığınız kriterlere uygun sonuç bulunamadı.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Mühimmat Adı</th>
                        <th>Sıralama</th>
                        <th>Maliyet</th>
                        <th>PLATFORM</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($munitions as $munition)
                        <tr>
                            <td>{{ $munition['name'] }}</td>
                            <td>{{ $munition['rank'] }}</td>
                            <td>{{ $munition['cost'] }}</td>
                            <td>{{ $munition['platform'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
