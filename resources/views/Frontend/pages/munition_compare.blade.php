@extends('Frontend.index')

@section('title', '| Mühimmat Karşılaştırma')

@section('content')

    <div class="container">

        <br>

        <div class="row">
            <div class="col-md-12">
                <h2>Mühimmat Karşılaştırma</h2>
            </div>
        </div>

        {{-- Filtre formu - GET ile çalışır, Kiyasla rotasına gönderir --}}
        <div class="row mt-4">
            <div class="col-md-12">
                {{-- Tema ile uyumlu, select2 destekli filtre formu --}}
                <form action="{{ route('Kiyasla') }}" method="GET" class="card card-body mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label stext-106 cl2 m-b-5">Hedef Tipi</label>
                            <div class="rs1-select2 bor8 bg0">
                                <select name="target_type" class="js-select2">
                                    <option value="">Tümü</option>
                                    <option value="SOFT" {{ request('target_type') === 'SOFT' ? 'selected' : '' }}>SOFT
                                    </option>
                                    <option value="HARD" {{ request('target_type') === 'HARD' ? 'selected' : '' }}>HARD
                                    </option>
                                </select>
                                <div class="dropDownSelect2"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label stext-106 cl2 m-b-5">Kategori</label>
                            <div class="rs1-select2 bor8 bg0">
                                <select name="category_id" class="js-select2">
                                    <option value="">Tümü</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="dropDownSelect2"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label stext-106 cl2 m-b-5">Menzil (Min)</label>
                            <input type="number" name="min"
                                class="form-control bor8 bg0 stext-106 p-lr-15 p-tb-10"
                                value="{{ request('min') }}" placeholder="Örn: 10">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label stext-106 cl2 m-b-5">Menzil (Max)</label>
                            <input type="number" name="max"
                                class="form-control bor8 bg0 stext-106 p-lr-15 p-tb-10"
                                value="{{ request('max') }}" placeholder="Örn: 100">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('Kiyasla') }}"
                            class="flex-c-m stext-101 cl2 size-107 bg0 bor2 hov-btn3 p-lr-15 trans-04 m-r-10">
                            Sıfırla
                        </a>
                        <button type="submit"
                            class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04">
                            Filtrele ve Kıyasla
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Filtre özeti --}}
        @if (request()->filled('target_type') || request()->filled('category_id') || request()->filled('min') || request()->filled('max'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <strong>Uygulanan Filtreler:</strong>
                        @if (request()->filled('target_type'))
                            <span class="badge bg-primary ms-1">Hedef Tipi: {{ request('target_type') }}</span>
                        @endif
                        @if (request()->filled('category_id'))
                            @php
                                $selectedCategory = $categories->firstWhere('id', (int) request('category_id'));
                            @endphp
                            @if ($selectedCategory)
                                <span class="badge bg-secondary ms-1">Kategori: {{ $selectedCategory->name }}</span>
                            @endif
                        @endif
                        @if (request()->filled('min') || request()->filled('max'))
                            <span class="badge bg-success ms-1">
                                Menzil:
                                {{ request('min') ? request('min') . ' km' : '0' }}
                                -
                                {{ request('max') ? request('max') . ' km' : '∞' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                @if ($munitions->isEmpty())
                    <p>Aradığınız kriterlere uygun sonuç bulunamadı.</p>
                @else
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    @foreach ($munitions as $munition)
                                        <th>{{ $munition->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Resim</strong></td>
                                    @foreach ($munitions as $munition)
                                    <td>
                                        @if ($munition->images->isNotEmpty())
                                            <a href="{{ route('muhimmatDetay', $munition->slug) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $munition->images->first()->url) }}" alt="Mühimmat Resmi" style="max-width: 100px; max-height: 100px;">
                                            </a>
                                        @endif
                                    </td>
                                    @endforeach
                                </tr>
                                @foreach ($attributes as $attribute)
                                    <tr>
                                        <td><strong>{{ $attribute->name }}</strong></td>
                                        @foreach ($munitions as $munition)
                                            <td>
                                                @php
                                                    $attrValue = $munition->attributes
                                                        ->where('id', $attribute->id)
                                                        ->first();
                                                @endphp
                                                @if ($attrValue)
                                                    @if ($attribute->option === 'Liste')
                                                        {{ $attribute->listValues->where('id', $attrValue->pivot->value)->first()->value ?? '' }}
                                                    @elseif ($attribute->option === 'Doğrulama')
                                                        @if ($attrValue->pivot->value == 1)
                                                            Var
                                                        @else
                                                            Yok
                                                        @endif
                                                    @elseif ($attribute->option === 'Aralık')
                                                        {{ $attrValue->pivot->min }}-{{ $attrValue->pivot->max }}
                                                    @else
                                                        {{ $attrValue->pivot->value ?? '' }}
                                                    @endif
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td><strong>Puan</strong></td>
                                    @foreach ($munitions as $munition)
                                        <td>
                                            <strong>
                                                @php
                                                    $totalScore = 0;
                                                @endphp
                                                @foreach ($munition->attributes as $attribute)
                                                    @if ($attribute->pivot->score && $attribute->multiplier)
                                                        @php
                                                            $totalScore +=
                                                                $attribute->multiplier * $attribute->pivot->score;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                {{ $totalScore }}
                                            </strong>
                                        </td>
                                    @endforeach
                                </tr>

                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>


        <br>

    </div>

@endsection
