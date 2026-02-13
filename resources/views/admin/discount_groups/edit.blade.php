@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">İndirim Grubu Düzenle</h1>
    <a href="{{ route('admin.discount-groups.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Geri Dön
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
        <form action="{{ route('admin.discount-groups.update', $discountGroup) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">İndirim Grubu Adı *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="{{ old('name', $discountGroup->name) }}" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="discount_percentage" class="form-label">İndirim Yüzdesi *</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" 
                                   value="{{ old('discount_percentage', $discountGroup->discount_percentage) }}" 
                                   min="0" max="100" step="0.01" required>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Açıklama</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $discountGroup->description) }}</textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="main_category_id" class="form-label">Ana Kategori *</label>
                        <select class="form-select" id="main_category_id" name="main_category_id" required>
                            <option value="">Kategori Seçin</option>
                            @foreach($mainCategories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ old('main_category_id', $discountGroup->main_category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                   {{ old('is_active', $discountGroup->is_active ? '1' : '') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Aktif
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Başlangıç Tarihi</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="{{ old('start_date', $discountGroup->start_date?->format('Y-m-d')) }}">
                        <small class="form-text text-muted">Boş bırakılırsa hemen başlar</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Bitiş Tarihi</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="{{ old('end_date', $discountGroup->end_date?->format('Y-m-d')) }}">
                        <small class="form-text text-muted">Boş bırakılırsa süresiz olur</small>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="customer_search" class="form-label">Firma Arama</label>
                <input type="text" class="form-control" id="customer_search" placeholder="Firma adı ile arama yapın...">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Firmalar *</label>
                <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;" id="customers_container">
                    @foreach($customers as $customer)
                        <div class="form-check customer-item" data-name="{{ strtolower($customer->unvan) }}">
                            <input class="form-check-input" type="checkbox" name="customer_ids[]" 
                                   value="{{ $customer->id }}" id="customer_{{ $customer->id }}"
                                   {{ in_array($customer->id, old('customer_ids', $discountGroup->customers->pluck('id')->toArray())) ? 'checked' : '' }}>
                            <label class="form-check-label" for="customer_{{ $customer->id }}">
                                <strong>{{ $customer->unvan }}</strong>
                                @if($customer->phone)
                                    <br><small class="text-muted">Tel: {{ $customer->phone }}</small>
                                @endif
                                @if($customer->email)
                                    <br><small class="text-muted">E-posta: {{ $customer->email }}</small>
                                @endif
                            </label>
                        </div>
                    @endforeach
                </div>
                <small class="form-text text-muted">Bu indirim grubuna dahil edilecek firmaları seçin. Seçilen firmalara ait tüm kullanıcıların siparişlerine indirim uygulanacaktır.</small>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Güncelle
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('customer_search').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const customerItems = document.querySelectorAll('.customer-item');
    
    customerItems.forEach(item => {
        const name = item.getAttribute('data-name');
        
        if (name.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>

@endsection 