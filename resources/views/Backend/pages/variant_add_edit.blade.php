@extends('Backend.index')

@section('title', '| Varyantlar')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('backend_assets/static/js/cropper/cropper.min.css') }}">
@endsection

@section('page-title', 'Varyantlar')
@section('page-subtitle', 'Varyant Ekle')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('varyant.index') }}">Varyantlar</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ isset($variant) ? 'Güncelle' : 'Ekle' }}</li>
@endsection

@section('content')
    <!-- // Basic multiple Column Form section start -->
    <section id="multiple-column-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Varyant Ekle</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form"
                                action="{{ isset($variant) ? route('varyant.update', $variant->id) : route('varyant.store') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @if (isset($variant))
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group mandatory">
                                            <label for="variant-name" class="form-label">Varyant Adı</label>
                                            <input type="text" id="variant-name" class="form-control"
                                                placeholder="Varyant Adı" name="name"
                                                value="{{ isset($variant) ? $variant->name : '' }}" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12 d-flex align-items-end">
                                        <div class="form-group">
                                            <a href="#" id="variant-add" class="btn icon btn-primary"><i
                                                    class="bi bi-plus"></i> Ekle</a>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div id="variant-values-container">
                                    <!-- Varyant değerleri buraya eklenecek -->
                                    @if(isset($variant))
                                        @foreach($variant->values as $value)
                                            <div class="row mb-1">
                                                <div class="col-md-6 col-12">
                                                    <input type="text" class="form-control" name="values[]" value="{{ $value->value }}" required>
                                                </div>
                                                <div class="col-md-6 col-12 d-flex align-items-end">
                                                    <button type="button" class="btn btn-warning me-1 mb-1" onclick="clearInput(this)">Temizle</button>
                                                    <button type="button" class="btn btn-danger me-1 mb-1" onclick="removeInput(this)">Kaldır</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary me-1 mb-1">Kaydet</button>
                                        <button type="reset" class="btn btn-light-secondary me-1 mb-1">Temizle</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- // Basic multiple Column Form section end -->
@endsection

@section('scripts')
    <script src="{{ asset('backend_assets/extensions/jquery/jquery.min.js') }}"></script>
    <script>
        document.getElementById('variant-add').addEventListener('click', function() {
            const variantValuesContainer = document.getElementById('variant-values-container');
            const inputCount = variantValuesContainer.querySelectorAll('input[type="text"]').length + 1;
            const newInput = document.createElement('div');
            newInput.classList.add('row', 'mb-1');
            newInput.innerHTML = `
                <div class="col-md-6 col-12">
                    <input type="text" class="form-control" id="variant-value-${inputCount}" name="values[]" required>
                </div>
                <div class="col-md-6 col-12 d-flex align-items-end">
                    <button type="button" class="btn btn-warning me-1 mb-1" onclick="clearInput(this)">Temizle</button>
                    <button type="button" class="btn btn-danger me-1 mb-1" onclick="removeInput(this)">Kaldır</button>
                </div>
            `;
            variantValuesContainer.appendChild(newInput);
        });

        function clearInput(button) {
            const inputContainer = button.parentElement.previousElementSibling;
            const input = inputContainer.querySelector('input[type="text"]');
            input.value = '';
        }

        function removeInput(button) {
            const inputContainer = button.parentElement.previousElementSibling;
            const row = inputContainer.parentElement;
            row.remove();
        }
    </script>

@endsection
