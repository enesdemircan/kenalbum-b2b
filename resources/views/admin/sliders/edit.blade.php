@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Slider Düzenle</h1>
        <p class="page-subtitle">{{ $slider->title ?: 'Slider #' . $slider->id }}</p>
    </div>
    <a href="{{ route('admin.sliders.index') }}" class="btn-material btn-material-secondary">
        <span class="material-icons">arrow_back</span> Geri Dön
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="material-card-elevated">
            <div class="material-card-body">
                <form action="{{ route('admin.sliders.update', $slider->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Slider Resmi</label>
                        @if($slider->image)
                            <div class="mb-2">
                                <img src="{{ Storage::url($slider->image) }}" alt="Mevcut Resim" style="max-width: 200px; max-height: 120px; object-fit: cover;">
                            </div>
                        @endif
                        <input type="file" name="image" id="image" class="form-control form-control-material @error('image') is-invalid @enderror" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Başlık</label>
                            <input type="text" name="title" id="title" class="form-control form-control-material @error('title') is-invalid @enderror" value="{{ old('title', $slider->title) }}">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea name="description" id="description" class="form-control form-control-material @error('description') is-invalid @enderror" rows="3">{{ old('description', $slider->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="link" class="form-label">Link</label>
                            <input type="url" name="link" id="link" class="form-control form-control-material @error('link') is-invalid @enderror" value="{{ old('link', $slider->link) }}" placeholder="https://example.com">
                            @error('link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="order" class="form-label">Sıra</label>
                            <input type="number" name="order" id="order" class="form-control form-control-material @error('order') is-invalid @enderror" value="{{ old('order', $slider->order) }}" min="0">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $slider->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="form-check-label">Aktif</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn-material btn-material-warning">
                                <span class="material-icons">save</span> Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 