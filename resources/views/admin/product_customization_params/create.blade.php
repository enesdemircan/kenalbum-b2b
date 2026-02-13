@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">{{ $product->title }} - Yeni Özelleştirme Parametresi Ekle</h1>
    <a href="{{ route('admin.product-customization-params.index', $product->id) }}" class="btn btn-secondary">← Geri Dön</a>
</div>

@if($parent)
<div class="alert alert-info">
    <strong>Parent Parametre:</strong> {{ $parent->param->key }} ({{ $parent->param->category->title }})
    @if(isset($subCategoryParams) && $subCategoryParams->count() > 0)
        <br><small>Bu kategorinin alt kategorilerinde {{ $subCategoryParams->count() }} parametre bulunuyor.</small>
    @endif
</div>
@endif

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.product-customization-params.store', $product->id) }}" method="POST">
            @csrf
            <div class="row">
                @if($parent)
                    <input type="hidden" name="customization_params_ust_id" value="{{ $parent->id }}">
                @endif
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="customization_category_id" class="form-label">Kategori</label>
                        @if($parent)
                            <!-- Parent seçildiğinde kategori otomatik seçili ve disabled -->
                            <input type="hidden" name="customization_category_id" value="{{ $selectedCategoryId }}">
                            <select class="form-select" disabled>
                                <option value="{{ $selectedCategoryId }}" selected>{{ $parent->param->category->title }}</option>
                            </select>
                            <small class="text-muted">Parent parametrenin kategorisi otomatik seçildi</small>
                        @else
                            <select name="customization_category_id" id="customization_category_id" class="form-select" required>
                                <option value="">Kategori Seçin</option>
                                @foreach($customizationCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="params_id" class="form-label">Parametre</label>
                        <select name="params_id" id="params_id" class="form-select" required>
                            <option value="">Parametre Seçin</option>
                            @if(isset($subCategoryParams) && $subCategoryParams->count() > 0)
                                @foreach($subCategoryParams as $param)
                                    <option value="{{ $param->id }}">{{ $param->key }}</option>
                                @endforeach
                            @endif
                        </select>
                        @if($parent && (!isset($subCategoryParams) || $subCategoryParams->count() == 0))
                            <small class="text-warning">Bu kategorinin alt kategorilerinde parametre bulunmuyor.</small>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="price" class="form-label">Ek Fiyat (TL)</label>
                        <input type="number" name="price" id="price" class="form-control" step="0.01" min="0" placeholder="0.00">
                        <small class="text-muted">Bu parametre seçildiğinde eklenecek fiyat</small>
                    </div>
                </div>
                
                <div class="col-md-6" style="display: none;">
                    <div class="mb-3">
                        <label for="option1" class="form-label">Seçenek 1</label>
                        <input type="text" name="option1" id="option1" class="form-control" placeholder="Opsiyonel">
                    </div>
                </div>
                
                <div class="col-md-6" style="display: none;">
                    <div class="mb-3">
                        <label for="option2" class="form-label">Seçenek 2</label>
                        <input type="text" name="option2" id="option2" class="form-control" placeholder="Opsiyonel">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Parametre Ekle</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('customization_category_id');
    const paramSelect = document.getElementById('params_id');
    
    // URL'den parent parametre ID'sini al
    const urlParams = new URLSearchParams(window.location.search);
    const parentParamId = urlParams.get('parent');
    
    // Eğer parent seçilmemişse kategori değiştiğinde parametreleri yükle
    if (!parentParamId && categorySelect) {
        categorySelect.addEventListener('change', function() {
            const categoryId = this.value;
            paramSelect.innerHTML = '<option value="">Parametre Seçin</option>';
            
            if (categoryId) {
                fetch(`/admin/customization-params/${categoryId}/params`)
                    .then(response => response.json())
                    .then(params => {
                        params.forEach(param => {
                            const option = document.createElement('option');
                            option.value = param.id;
                            option.textContent = param.key;
                            paramSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Parametreler yüklenirken hata:', error);
                    });
            }
        });
    }
});
</script>

@endsection 