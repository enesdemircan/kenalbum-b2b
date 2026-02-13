@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Route Düzenle: {{ $route->name }}</h1>
    <a href="{{ route('admin.routes.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Geri Dön
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.routes.update', $route) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Route Adı <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $route->name) }}" 
                               placeholder="örn: admin.products.index" required>
                        <div class="form-text">Benzersiz route adı (örn: admin.products.index)</div>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="uri" class="form-label">URI <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('uri') is-invalid @enderror" 
                               id="uri" name="uri" value="{{ old('uri', $route->uri) }}" 
                               placeholder="örn: /admin/products" required>
                        <div class="form-text">Route URI'si (örn: /admin/products)</div>
                        @error('uri')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="method" class="form-label">HTTP Method <span class="text-danger">*</span></label>
                        <select class="form-select @error('method') is-invalid @enderror" 
                                id="method" name="method" required>
                            <option value="">Method Seçin</option>
                            @foreach($methods as $method)
                                <option value="{{ $method }}" {{ old('method', $route->method) == $method ? 'selected' : '' }}>
                                    {{ $method }}
                                </option>
                            @endforeach
                        </select>
                        @error('method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="group" class="form-label">Grup</label>
                        <input type="text" class="form-control @error('group') is-invalid @enderror" 
                               id="group" name="group" value="{{ old('group', $route->group) }}" 
                               placeholder="örn: admin, customer, api">
                        <div class="form-text">Route grubu (opsiyonel)</div>
                        @error('group')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Açıklama</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3" 
                          placeholder="Route açıklaması">{{ old('description', $route->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                           {{ old('is_active', $route->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Route Aktif
                    </label>
                    <div class="form-text">Bu route aktif mi?</div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Route Güncelle
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Method seçimine göre renk değiştir
    const methodSelect = document.getElementById('method');
    const methodColors = {
        'GET': 'success',
        'POST': 'primary',
        'PUT': 'warning',
        'PATCH': 'warning',
        'DELETE': 'danger'
    };

    function updateMethodColor() {
        const selectedMethod = methodSelect.value;
        methodSelect.className = 'form-select';
        
        if (selectedMethod && methodColors[selectedMethod]) {
            methodSelect.classList.add(`border-${methodColors[selectedMethod]}`);
        }
    }

    methodSelect.addEventListener('change', updateMethodColor);
    updateMethodColor();
});
</script>
@endsection 