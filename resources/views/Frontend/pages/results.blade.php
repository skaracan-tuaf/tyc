@extends('Frontend.index')

@section('title', '| Sonuçlar')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Hedef: <strong>{{ $targetName }}</strong> | 
                        Meteorolojik Durum: <strong>{{ $weather }}</strong>
                    </h3>
                </div>
                <div class="card-body">
                    @if(empty($results))
                        <div class="alert alert-warning">
                            <strong>Uyarı!</strong> "{{ $targetName }}" hedefi için "{{ $weather }}" hava koşullarında uygun sonuç bulunamadı.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Sıra</th>
                                        <th>Mühimmat Adı</th>
                                        <th>Maliyet</th>
                                        <th>Platform</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $result)
                                        <tr>
                                            <td>{{ $result['rank'] }}</td>
                                            <td>{{ $result['name'] }}</td>
                                            <td>{{ $result['cost'] }}</td>
                                            <td>{{ $result['platform'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <a href="{{ route('AnaSayfa') }}" class="btn btn-primary">Ana Sayfaya Dön</a>
                            <a href="{{ route('Kiyasla') }}" class="btn btn-secondary">Yeni Karşılaştırma</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 