@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Başlık *</label>
        <input type="text" name="title" class="form-control form-control-material" value="{{ old('title', $method->title ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Kod *</label>
        <input type="text" name="code" class="form-control form-control-material" value="{{ old('code', $method->code ?? '') }}" placeholder="aras, yurtici" required>
        <small class="text-muted">Benzersiz, küçük harf</small>
    </div>
    <div class="col-md-3">
        <label class="form-label">Sıra</label>
        <input type="number" name="sort_order" class="form-control form-control-material" value="{{ old('sort_order', $method->sort_order ?? 0) }}" min="0">
    </div>
    <div class="col-md-4">
        <label class="form-label">Ücret (TL) *</label>
        <input type="number" name="price" class="form-control form-control-material" step="0.01" min="0" value="{{ old('price', $method->price ?? 0) }}" required>
    </div>
    <div class="col-md-8">
        <label class="form-label">Açıklama</label>
        <input type="text" name="description" class="form-control form-control-material" value="{{ old('description', $method->description ?? '') }}" placeholder="1-2 iş günü teslimat">
    </div>
    <div class="col-12">
        <div class="form-check">
            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $method->is_active ?? true) ? 'checked' : '' }}>
            <label for="is_active" class="form-check-label">Aktif (sipariş ekranında görünsün)</label>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn-material btn-material-primary">
        <span class="material-icons">save</span> Kaydet
    </button>
    <a href="{{ route('admin.shipping-methods.index') }}" class="btn-material btn-material-secondary">İptal</a>
</div>
