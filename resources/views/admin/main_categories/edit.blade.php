@extends('admin.layout')

@section('content')
<div class="page-header mb-4">
    <h1 class="page-title">Ana Kategori Düzenle</h1>
    <p class="page-subtitle">{{ $mainCategory->title }} - Kategori bilgilerini güncelleyin</p>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="material-card-elevated">
            <div class="material-card-header">
                <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">edit</span>Kategori Bilgilerini Düzenle</h5>
            </div>
            <div class="material-card-body">
                <form action="{{ route('admin.main-categories.update', $mainCategory) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="title" class="form-label">Başlık *</label>
                        <input type="text" name="title" id="title" class="form-control form-control-material" value="{{ $mainCategory->title }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug (URL)</label>
                        <input type="text" name="slug" id="slug" class="form-control form-control-material" value="{{ $mainCategory->slug }}" placeholder="Boş bırakılırsa otomatik oluşturulur">
                        <div class="form-text">
                            <span class="material-icons" style="font-size: 14px; vertical-align: middle">info</span>
                            Örnek: "Albüm Kategorisi" → "album-kategorisi"
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="ust_id" class="form-label">Üst Kategori</label>
                        <select name="ust_id" id="ust_id" class="form-select form-control-material">
                            <option value="0">Yok (Ana Kategori)</option>
                            @foreach($mainCategories as $cat)
                                <option value="{{ $cat->id }}" @if($mainCategory->ust_id == $cat->id) selected @endif>{{ $cat->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="order" class="form-label">Sıra</label>
                        <input type="number" name="order" id="order" class="form-control form-control-material" value="{{ $mainCategory->order }}">
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.main-categories.index') }}" class="btn-material btn-material-secondary">
                            <span class="material-icons">close</span>
                            İptal
                        </a>
                        <button type="submit" class="btn-material btn-material-success">
                            <span class="material-icons">save</span>
                            Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
