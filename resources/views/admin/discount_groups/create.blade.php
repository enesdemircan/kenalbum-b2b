@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Yeni İndirim Grubu</h1>
        <p class="page-subtitle">İndirim kampanyası oluşturun</p>
    </div>
    <a href="{{ route('admin.discount-groups.index') }}" class="btn-material btn-material-secondary">
        <span class="material-icons">arrow_back</span>
        Geri Dön
    </a>
</div>

@if($errors->any())
    <div class="material-alert material-alert-danger">
        <span class="material-icons">error</span>
        <div>
            <strong>Hatalar:</strong>
            <ul class="mb-0" style="margin-top: 8px">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="material-card-elevated">
    <div class="material-card-header">
        <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">local_offer</span>İndirim Grubu Bilgileri</h5>
    </div>
    <div class="material-card-body">
        <form action="{{ route('admin.discount-groups.store') }}" method="POST">
            @csrf
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">İndirim Grubu Adı *</label>
                        <input type="text" class="form-control form-control-material" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="discount_percentage" class="form-label">İndirim Yüzdesi *</label>
                        <div class="input-group">
                            <input type="number" class="form-control form-control-material" id="discount_percentage" name="discount_percentage" 
                                   value="{{ old('discount_percentage') }}" min="0" max="100" step="0.01" required>
                            <span class="input-group-text" style="border: none; border-bottom: 2px solid var(--md-divider); background: transparent">%</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Açıklama</label>
                <textarea class="form-control form-control-material" id="description" name="description" rows="3">{{ old('description') }}</textarea>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="main_category_id" class="form-label">Ana Kategori *</label>
                        <select class="form-select form-control-material" id="main_category_id" name="main_category_id" required>
                            <option value="">Kategori Seçin</option>
                            @foreach($mainCategories as $category)
                                <option value="{{ $category->id }}" {{ old('main_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                   {{ old('is_active', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Aktif
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Başlangıç Tarihi</label>
                        <input type="date" class="form-control form-control-material" id="start_date" name="start_date" value="{{ old('start_date') }}">
                        <small class="form-text text-muted">Boş bırakılırsa hemen başlar</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Bitiş Tarihi</label>
                        <input type="date" class="form-control form-control-material" id="end_date" name="end_date" value="{{ old('end_date') }}">
                        <small class="form-text text-muted">Boş bırakılırsa süresiz olur</small>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="customer_search" class="form-label">Firma Arama</label>
                <input type="text" class="form-control form-control-material" id="customer_search" placeholder="Firma adı ile arama yapın...">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Firmalar *</label>
                <div class="material-card-outlined p-3" style="max-height: 300px; overflow-y: auto;" id="customers_container">
                    @foreach($customers as $customer)
                        <div class="form-check customer-item mb-2" data-name="{{ strtolower($customer->unvan) }}">
                            <input class="form-check-input" type="checkbox" name="customer_ids[]" 
                                   value="{{ $customer->id }}" id="customer_{{ $customer->id }}"
                                   {{ in_array($customer->id, old('customer_ids', [])) ? 'checked' : '' }}>
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
                <small class="form-text text-muted">Bu indirim grubuna dahil edilecek firmaları seçin.</small>
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.discount-groups.index') }}" class="btn-material btn-material-secondary">
                    <span class="material-icons">close</span>
                    İptal
                </a>
                <button type="submit" class="btn-material btn-material-primary">
                    <span class="material-icons">add_circle</span>
                    İndirim Grubu Oluştur
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
        item.style.display = name.includes(searchTerm) ? 'block' : 'none';
    });
});
</script>
@endsection
