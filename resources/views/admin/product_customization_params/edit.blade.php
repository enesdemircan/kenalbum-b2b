@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Özelleştirme Parametresi Düzenle</h1>
        <p class="page-subtitle">{{ $product->title }} - {{ $pivotParam->param->key }}</p>
    </div>
    <a href="{{ route('admin.product-customization-params.index', $product->id) }}" class="btn-material btn-material-secondary">
        <span class="material-icons">arrow_back</span> Geri Dön
    </a>
</div>

<div class="material-card-elevated">
    <div class="material-card-body">
        <form action="{{ route('admin.product-customization-params.update', [$product->id, $pivotParam->id]) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <input type="text" class="form-control form-control-material" value="{{ $pivotParam->param->category->title ?? 'Bilinmiyor' }}" readonly>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Parametre</label>
                        <input type="text" class="form-control form-control-material" value="{{ $pivotParam->param->key }}" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Parent Parametre</label>
                        <input type="text" class="form-control form-control-material" 
                               value="{{ $pivotParam->hasParent() ? $pivotParam->getParent()->param->key : 'Ana parametre' }}" readonly>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="price" class="form-label">Ek Fiyat (TL)</label>
                        <input type="number" name="price" id="price" class="form-control form-control-material" step="0.01" min="0" 
                               value="{{ $pivotParam->price }}" placeholder="0.00">
                        <small class="text-muted">Bu parametre seçildiğinde eklenecek fiyat</small>
                    </div>
                </div>
            </div>

            <div class="row" style="display: none;">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="option1" class="form-label">Seçenek 1</label>
                        <input type="text" name="option1" id="option1" class="form-control form-control-material" 
                               value="{{ $pivotParam->option1 }}" placeholder="Opsiyonel">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="option2" class="form-label">Seçenek 2</label>
                        <input type="text" name="option2" id="option2" class="form-control form-control-material" 
                               value="{{ $pivotParam->option2 }}" placeholder="Opsiyonel">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn-material btn-material-primary">
                    <span class="material-icons">save</span> Güncelle
                </button>
            </div>
        </form>
    </div>
</div>

@endsection 